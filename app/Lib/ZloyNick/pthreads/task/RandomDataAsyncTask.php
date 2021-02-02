<?php

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads\task;

use App\Lib\ZloyNick\pthreads\AsyncTask;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

use function serialize, mt_rand, json_decode;

class RandomDataAsyncTask extends AsyncTask
{

    public function run() : void
    {
        parent::run();

        $data = [];
        $client = new Client(['base_uri' => 'https://api.randomdatatools.ru/']);

        try {
            $response = $client->request(
                'POST',
                '',
                [
                    'Headers' => [
                        'Accept' => 'application/json'
                    ],
                    'query' => [
                        'count' => 10,
                        'params' => 'LastName,FirstName,FatherName,Address,kpp,ogrn,PasportOtd'
                    ],
                    'timeout' => 5
                ]
            );
        } catch (GuzzleException $e) {
            $data['message'] = $e->getMessage();
            $this->setOutput(serialize($data));
            $this->setCompleted();

            return;
        } finally {
            $parties = json_decode($response->getBody()->getContents());
        }

        foreach ($parties as $k => $party)
        {
            $data[$k] = [
                'name' => $party->PasportOtd,
                'ogrn' => $party->ogrn,
                'kpp' => $party->kpp,
                'inn' => $this->readValue('inn'),
                'maintrainer' => [
                    'name' => "{$party->LastName} {$party->FirstName} {$party->FatherName}",
                    'role' => "наЩяльникЕ"
                ],
                'active' => (bool)mt_rand(0, 1),
                'address' => $party->Address
            ];
        }

        $this->setOutput(serialize($data));
        $this->setCompleted();
    }

}
