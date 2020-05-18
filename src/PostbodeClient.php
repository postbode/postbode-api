<?php

namespace Postbode;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class PostbodeClient
{
    protected $apihost = 'https://app.postbode.nu/api';
    protected $apikey = null;
    protected $client = null;
    protected $version = null;
    protected $os = null;
    protected $letter_queue = [];

    public function __construct($apikey)
    {
        $this->apikey = $apikey;
        $this->version = phpversion();
        $this->os = PHP_OS;
    }

    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Client([
                RequestOptions::VERIFY => true,
                RequestOptions::TIMEOUT => 30,
            ]);
        }

        return $this->client;
    }

    protected function sendRequest($method = null, $path = null, $body = null)
    {
        $client = $this->getClient();

        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'User-Agent' => 'PostbodeClient (PHP Version: '.$this->version.', OS: '.$this->os.')',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Authorization' => $this->apikey,
            ],
        ];

        if (!empty($body)) {
            $cleanParams = array_filter($body, function ($value) {
                return $value !== null;
            });

            switch ($method) {
                case 'GET':
                case 'HEAD':
                case 'DELETE':
                case 'OPTIONS':
                    $options[RequestOptions::QUERY] = $cleanParams;
                    break;
                case 'PUT':
                case 'POST':
                case 'PATCH':
                    $options[RequestOptions::JSON] = $cleanParams;
                    break;
            }
        }

        $response = $client->request($method, $this->apihost.$path, $options);

        $response_code = $response->getStatusCode();
        if ($response_code == 200) {
            return json_decode($response->getBody(), true);
        } else {
            return $response_code;
        }
    }

    public function getMailboxes()
    {
        return $this->sendRequest('GET', '/mailbox');
    }

    public function getLetters($mailbox_id)
    {
        return $this->sendRequest('GET', '/mailbox/'.$mailbox_id.'/letters');
    }

    public function getLetter($mailbox_id, $letter_id)
    {
        return $this->sendRequest('GET', '/mailbox/'.$mailbox_id.'/letter/'.$letter_id);
    }

    public function sendLetter($mailbox_id, $filename, $envelope_id = null, $country = null, $registered = false, $send = true, $color = 'FC', $printing = 'simplex', $printer = 'inkjet')
    {
        $letter = [
            'documents' => [
                [
                    'name' => basename($filename),
                    'content' => base64_encode(file_get_contents($filename)),
                ],
            ],
            'envelope_id' => $envelope_id,
            'country' => $country,
            'registered' => $registered,
            'send' => $send,
            'color' => $color,
            'printing' => $printing,
            'printer' => $printer,
        ];

        return $this->sendRequest('POST', '/mailbox/'.$mailbox_id.'/letters', $letter);
    }

    public function addLetterToQueue($mailbox_id, $filename, $envelope_id = null, $country = null, $registered = false, $send = true, $color = 'FC', $printing = 'simplex', $printer = 'inkjet')
    {
        $this->letter_queue[] = [
            'documents' => [
                [
                    'name' => basename($filename),
                    'content' => base64_encode(file_get_contents($filename)),
                ],
            ],
            'envelope_id' => $envelope_id,
            'country' => $country,
            'registered' => $registered,
            'send' => $send,
            'color' => $color,
            'printing' => $printing,
            'printer' => $printer,
        ];
    }

    public function sendLetterQueue()
    {
        return $this->sendRequest('POST', '/mailbox/'.$mailbox_id.'/letterbatch', $this->letter_queue);
    }
}
