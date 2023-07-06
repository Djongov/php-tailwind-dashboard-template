<?php

namespace Request;

class NativeHttp {

    protected $url;
    protected $data;

    public function get($url, $options = [
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true
        ]
    ], $sslIgnore = false)
    {
        if (!$sslIgnore) {
            $options['ssl'] = [
                'cafile'            => CURL_CERT,
                //'peer_fingerprint'  => openssl_x509_fingerprint(file_get_contents('/path/to/key.crt')),
                'verify_peer'       => true,
                'verify_peer_name'  => true,
                'allow_self_signed' => false,
                'verify_depth'      => 0,
                'CN_match'          => parse_url($url)['host']
            ];
        }

        $context  = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        $responseCode = $this->getResponseCode($http_response_header[0]);

        var_dump($responseCode); // Display the response code

        return var_dump($response);
    }

    private function getResponseCode($responseHeader)
    {
        if ($responseHeader != null) {
            preg_match('/\d{3}/', $responseHeader, $matches);
            return $matches[0] ?? null;
        }
    }
}
