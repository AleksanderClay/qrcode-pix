<?php

namespace App\Pix;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Api
{

    /**
     * URL base do PSP
     * @var string
     */
    private $baseUrl;

    /**
     * Client ID do oAuth2 do PSP
     * @var string
     */
    private $clientId;

    /**
     * Client secret do oAuth2 do PSP
     * @var string
     */
    private $clientSecret;

    /**
     * URL de Callback
     * @var string
     */
    private $redirectUri;

    /**
     * Token do APP
     * @var string
     */
    private $tokenApp;

    /**
     * @param string $baseUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $tokenApp
     */
    public function __construct($baseUrl, $clientId, $clientSecret, $redirectUri, $tokenApp)
    {
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->tokenApp = $tokenApp;
    }

    /**
     * Método Responsável por criar uma cobrança imediata
     * @param $txid
     * @param $request
     * @return array
     */
    public function createCob($txid, $request)
    {
        return $this->send('PUT', '/pix-bb/v1/' . $txid, $request);
    }

    /**
     * Método Responsável por consultar uma cobrança imediata
     * @param $txid
     * @return array
     */
    public function consultaCob($txid)
    {
        return $this->send('GET', '/pix-bb/v1/' . $txid);
    }

    /**
     * Método responsável por obter o token de acesso às APIs Pix
     * @return resource
     * @throws GuzzleException
     */
    private function getAccessToken()
    {
        // ENDPOINT COMPLETO
        $endpoint = 'oauth/token';

        // CONFIGURAÇÃO O CLIENT
        $client = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        // EXECUTA O CLIENT
        $response = $client->request('POST', $endpoint, [
            'headers' => [
                'Authorization' => 'Basic ' . $this->tokenApp,
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ])->getBody()->getContents();

        // RESPONSE EM ARRAY
        $responseArray = json_decode($response, true);

        // RETORNA O ACESS TOKEN
        return isset($responseArray['acess_token']) ? $responseArray['acess_token'] : '';
    }

    /**
     * Método responsável por enviar requisição para a PSP
     * @param string $method
     * @param string $resource
     * @param array $request
     * @return array
     */
    public function send($method, $resource, $request = [])
    {
        //ENDPOINT COMPLETO
        $endpoint = $this->baseUrl . $resource;

        //HEADERS
        $headers = [
            'Cache-Control: no-cache',
            'Content-type: application/json',
            'Authorization: Bearer' . $this->getAccessToken()
        ];

        // CONFIGURAÇÃO DO CURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSLCERTPASSWD => '',
            CURLOPT_HTTPHEADER => $headers
        ]);

        switch ($method) {
            case 'POST':
            case 'PUT':
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
                break;
        }

        //EXECUTA O CURL
        $response = curl_exec($curl);
        curl_close($curl);

        // RETORNA ARRAY DA RESPOSTA
        return json_decode($response, true);

    }
}