<?php

namespace App\Services\Converter;

use App\Services\Converter\Contracts\Converter;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class PDFConverter implements Converter
{
    protected $uri_api = "https://dragon.pdf2go.com/api";

    protected $client;

    protected $path;

    protected $status;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache'
            ]
        ]);
    }

    public function convert($path)
    {
        $this->path = $path;

        $job = $this->dispatchJob();

        $upload_file = $this->uploadFile($job);

        $this->convertJob($upload_file);

        return $this->downloadFile($upload_file['id']['job']);
    }

    protected function downloadFile($id)
    {
        $status = $this->checkJobStatus($id);

        if ($status['code'] == 'completed') {
            $this->client->request('GET', $status['job']['output'][0]['download_uri'], ['sink' => storage_path('app/menu.docx')]);

            return $status;
        } else {
            return null;
        }
    }

    protected function checkJobStatus($id)
    {
        $job = $this->getJobNotAsync($id);

        switch ($job['status']['code']) {
            case 'queued':
            case 'processing':
                $this->checkJobStatus($job['id']);
                break;
            case 'incomplete':
                $this->status =  array('code' => $job['status']['code']);
                break;
            case 'completed':
                $this->status =  array('code' => $job['status']['code'], 'job' => $job);
                break;
            default:
                $this->status =  array('code' => 'incomplete', 'message' => 'error');
        }

        return $this->status;
    }

    protected function convertJob($file)
    {
        $id = $file['id']['job'];

        $body = [
            'json' => [
                'category' => 'document',
                'target' => 'docx',
            ]
        ];

        $response = $this->client->request('POST', $this->uri_api."/jobs/$id/conversions", $body);

        return $this->parseJson($response);
    }

    protected function uploadFile($job)
    {
        $job_id     = $job['id'];
        $job_token  = $job['token'];
        $job_server = $job['server'];

        $uploadClient = new Client([
            'headers' => [
                'X-Oc-Token' => $job_token,
                'X-Oc-Upload-Uuid' => uniqid('uid')
            ]
        ]);

        $body = [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => Utils::tryFopen($this->path, 'r'),
                ],
            ]
        ];

        $response = $uploadClient->request('POST', $job_server."/upload-file/$job_id", $body);

        return $this->parseJson($response);
    }

    protected function dispatchJob()
    {
        $form_params = [
            'form_params' => [
                'fail_on_conversion_error' => false,
                'fail_on_input_error' => false,
                'operation' => 'convertPdfToWord',
            ]
        ];

        $response = $this->client->request('POST', $this->uri_api."/jobs?async=true", $form_params);

        return $this->getJob($this->parseJson($response)['sat']['id_job']);
    }

    protected function getJob($id)
    {
        sleep(1);

        $response = $this->client->request('GET', $this->uri_api."/jobs/$id?async=true");

        return $this->parseJson($response);
    }
    protected function getJobNotAsync($id)
    {
        sleep(1);

        $response = $this->client->request('GET', $this->uri_api."/jobs/$id");

        return $this->parseJson($response);
    }

    protected function parseJson(ResponseInterface $response): array
    {
        $raw = $response->getBody();
        $result = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('JSON decoding error: ' . json_last_error_msg());
        }

        return $result;
    }
}