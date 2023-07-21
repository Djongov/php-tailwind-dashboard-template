<?php

namespace Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;

class Http
{
    protected $url;
    protected $data;

    public static function get($url, $sslIgnore = false, $returnArray = true)
    {
        $client = new Client([
            'verify' => CURL_CERT,
            'http_version' => '2.0'
        ]);

        try {
            $response = $client->get($url);
            if ($returnArray) {
                return json_decode($response->getBody()->getContents(), true);
            } else {
                return $response->getBody()->getContents();
            }
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorMessage = $e->getMessage();

            // Handle client errors
            if ($statusCode >= 400 && $statusCode <= 599) {
                if ($returnArray) {
                    return ['error' => $statusCode . '-' . $errorMessage];
                } else {
                    return 'error: ' . $statusCode . '-' . $errorMessage;
                }
            }
        } catch (ConnectException $e) {
            // Handle the connection exception
            $errorMessage = $e->getMessage();
            $protectedErrorMessage = Http::extractHostFromErrorMessage($errorMessage);
            if ($returnArray) {
                return ['error' => $protectedErrorMessage];
            } else {
                return 'error: ' . $protectedErrorMessage;
            }
            // or return an error response
            // return response()->json(['error' => 'Connection error'], 500);
        }
    }

    private function extractHostFromErrorMessage($errorMessage)
    {
        $hostInfo = '';

        if (preg_match('/Could not resolve host: (.+?) /', $errorMessage, $matches)) {
            $hostInfo = 'Could not resolve host: ' . $matches[1];
        } elseif (preg_match('/Connection timed out after (\d+) milliseconds/', $errorMessage, $matches)) {
            $hostInfo = 'Connection timed out after ' . $matches[1] . ' milliseconds';
        } elseif (preg_match('/Failed to connect to (.+?) port (\d+): .+/', $errorMessage, $matches)) {
            $hostInfo = 'Failed to connect to ' . $matches[1] . ' on port ' . $matches[2];
        }
        // Add more patterns for other types of connection errors as needed

        return $hostInfo;
    }
}
