<?php

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads\task;

use App\Lib\ZloyNick\pthreads\AsyncTask;

use Dadata_Client, Dadata_Service_Rest, Exception;
use function serialize;

class DadataAsyncTask extends AsyncTask
{

    public function run(): void
    {
        parent::run();

        try {
            $dadataClient = new Dadata_Client(
                [
                    'api_key' => $this->readValue('dadataTokenKey'),
                    'base_uri' => 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party/',
                    'content_type' => 'application/json'
                ]
            );
            $restService = new Dadata_Service_Rest($dadataClient);

            $result = $restService->suggest->party(['query' => $this->readValue('inn')]);
        } catch (Exception $exception) {
            $this->setOutput(serialize(['message' => $exception->getMessage()]));
            $this->setCompleted();

            return;
        }

        $parties = $result->getSuggestions();
        $data = [];

        /**
         *
         * @var int $k
         * @var \Dadata_Suggest_Party_Data $party
         *
         */
        foreach ($parties as $k => $party) {
            $party = $party->getData();
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

        $this->setOutput(serialize($data));
        $this->setCompleted();
    }

}
