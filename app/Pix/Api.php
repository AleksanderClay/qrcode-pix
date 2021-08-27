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
    private  $baseUrl;

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
     * Token Basic
     * @var string
     */
    private $tokenBasic;

    /**
     * Developer Application Key
     * @var string
     */
    private $appKey;

    /**
     * @param string $baseUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $tokenBasic
     * @param string $appKey
     */
    public function __construct($baseUrl, $clientId, $clientSecret, $redirectUri, $tokenBasic, $appKey)
    {
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->tokenBasic = $tokenBasic;
        $this->appKey = $appKey;
    }

    /**
     * Método Responsável por criar uma cobrança imediata
     * @param $request
     * @return array
     * @throws GuzzleException
     */
    public function createCob($request)
    {
        return $this->send($request);
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
     * @return void
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
                'Authorization' => 'Basic ' . $this->tokenBasic,
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ])->getBody()->getContents();

        // RESPONSE EM ARRAY
        $responseArray = json_decode($response, true);

        // RETORNA O ACESS TOKEN
        return isset($responseArray['access_token']) ? $responseArray['access_token'] : '';
    }


    /**
     * Método responsável por enviar requisição para a PSP
     * @param string $method
     * @param string $resource
     * @param array $request
     * @return array
     * @throws GuzzleException
     */
    public function send( $request = [])
    {
        $url = 'https://api.hm.bb.com.br';

        $endpoint = 'pix-bb/v1/arrecadacao-qrcodes';

        // CONFIGURAÇÃO O CLIENT
        $client = new Client([
            'base_uri' => $url,
        ]);

        // EXECUTA O CLIENT
        $response = $client->request('POST', $endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ],
            'query' => [
                'gw-dev-app-key' => $this->appKey,
            ],
            'request-body' => [
                'content-type' => 'application/json',
                json_encode($request)
            ]

        ]);

        // RETORNA ARRAY DA RESPOSTA
        return json_decode($response, true);

    }
}