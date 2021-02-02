<?php

declare(strict_types=1);

namespace App\Lib\ZloyNick\pthreads\task;

use App\Lib\ZloyNick\pthreads\AsyncTask;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ComposerAutoloaderInit265cfa55c83f224b7f33bd3f36a008a6;

class LocalRestAsyncTask extends AsyncTask
{

    public function run()
    {
        parent::run();

        $data = [];
        $client = new Client(['base_uri' => 'http://127.0.0.1:8002']);

        try {
            $response = $client->request(
                'GET',
                '/api/v1/companies/' . $this->readValue('inn'),
                [
                    'Headers' => [
                        'Accept' => 'application/json'
                    ],
                    'query' => [
                        'token' => 'si0Z9bZrD8JPqVLYeRZ5FI5DDCaLPhRu4WWVp22JVErWeh0Lssx64xPcQ3UlB6F4qqONjascVzEeskVA'
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

            if ($parties->message) {
                $data['message'] = $parties->message;
                $this->setOutput(serialize($data));
                $this->setCompleted();
                return;
            }
        }

        foreach ($parties->companies as $k => $party) {
            $data[$k] = [
                'name' => $party->name,
                'ogrn' => $party->ogrn,
                'kpp' => $party->kpp,
                'inn' => $party->inn,
                'maintrainer' => [
                    'name' => $party->owner->name,
                    'role' => $party->owner->post
                ],
                'active' => $party->active == 1,
                'address' => $party->address
            ];
        }

        $this->setOutput(serialize($data));
        $this->setCompleted();
    }

}
