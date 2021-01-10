<?php

declare(strict_types=1);

namespace App\Http\Controllers;

//validator
//use App\Http\Requests\SearchCompanyRequest;
use amp\tasks\AsyncDadataRequestTask;
use Illuminate\Http\Request;
use Amp\Delayed;
use Amp\Loop;
use Amp\Parallel\Context\Parallel;
use function Amp\call;
use function Amp\Promise\wait;

class CompanySearchController extends Controller
{

    /**
     *
     * <p>
     *  Returns companies with given "INN"
     *  Only for axios.
     * </p>
     *
     * <p> Example </p>
     * <code>
     *      this.axios.post(
     *          "/href",
     *          [
     *              inn: this.inn
     *          ]
     *      ).then(res () => {
     *           //etc...
     *      })
     * </code>
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function search(Request $request)
    {
        // Передаваемый ИНН организации.
        $inn = $request->offsetGet('inn');

        // Получаем данные с dadata.ru
        // и конвертируем в нужный нам вид
        // Насчёт асинхронности: да, задача выполняется
        // параллельно.
        // Вместо pthreads заюзал amphp/parallel
        // ибо в винде бинарник с pthreads компилится криво,
        // а именно, php.ini не может подключить его.
        // Шиндоус мне пока что нужна=)

        $dadataProcess = call(
            function () use ($inn) {
                $result =
                    (
                    new \Dadata_Service_Rest(
                        new \Dadata_Client(
                            [
                                'api_key' => env('DADATA_TOKEN'),
                                'base_uri' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party/',
                                'content_type' => 'application/json'
                            ]
                        )
                    )
                    )->suggest->party(['query' => $inn]);
                $parties = $result->getSuggestions();
                $data = [];
                /**
                 *
                 * <p>
                 *  Приводим данные в нужный нам вид.
                 *  Взял пока только основное=)
                 * </p>
                 *
                 * @var int $k
                 * @var \Dadata_Suggest_Party_Data $party
                 */
                foreach ($parties as $k => $party) {
                    $party = optional($party->getData());

                    $data[$k] = [
                        'name' => $party->name['full_with_opf'],
                        'ogrn' => $party->ogrn,
                        'kpp' => $party->kpp,
                        'inn' => $party->inn,
                        'maintrainer' => [
                            'name' => $party->management['name'],
                            'role' => $party->management['post']
                        ],
                        'active' => $party->state['status'] == 'ACTIVE',
                        'address' => $party->address['value']
                    ];
                }

                // Возвращаем закодированный сериализованный класс
                // base64 нужен, чтобы не "пихнулся" null byte \0
                return base64_encode(serialize($data));
            }
        );

        Loop::run(function () {
            $timer = Loop::repeat(1000, function () {
                static $i;
                $i = $i ? ++$i : 1;
                print "Demonstrating how alive the parent is for the {$i}th time.\n";
            });

            try {
                // Create a new child thread that does some blocking stuff.
                $context = yield Parallel::run(__DIR__ . "/blocking-process.php");

                \assert($context instanceof Parallel);

                print "Waiting 2 seconds to send start data...\n";
                yield new Delayed(2000);

                yield $context->send("Start data"); // Data sent to child process, received on line 9 of blocking-process.php

                \printf("Received the following from child: %s\n", yield $context->receive()); // Sent on line 14 of blocking-process.php
                \printf("Process ended with value %d!\n", yield $context->join());
            } finally {
                Loop::cancel($timer);
            }
        });

        $rawDadataData = wait($dadataProcess);
        $dadataData = unserialize(base64_decode($rawDadataData));

        return;
        //return \response()->json($dadataData, 200);
    }
}
