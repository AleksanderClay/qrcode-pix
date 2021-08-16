<?php

namespace App\Pix;

class Api {

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
     * Caminho absoluto até o arquivo do certificado
     * @var string
     */
    private $certificate;

    /**
     * Caminho absoluto até o arquivo do certificado
     * @var string
     */
    private $redirectUri;

    /**
     * @param string $baseUrl
     * @param string $clientId
     * @param string $clientSecret
     * @param string $certificate
     * @param string $redirectUri
     */
    public function __construct($baseUrl, $clientId, $clientSecret, $certificate, $redirectUri)
    {
        $this->baseUrl = $baseUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->certificate = $certificate;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Método Responsável por criar uma cobrança imediata
     * @param $txid
     * @param $request
     * @return array
     */
    public function createCob($txid, $request)
    {
        return $this->send('PUT', '/v2/cob/'.$txid,$request);
    }

    /**
     * Método Responsável por consultar uma cobrança imediata
     * @param $txid
     * @return array
     */
    public function consultaCob($txid)
    {
        return $this->send('GET', '/pix-bb/v1/'.$txid);
        print_r($this->send());
    }

    /**
     * Método responsável por obter o token de acesso às APIs Pix
     * @return string
     */
    private function getAccessToken()
    {
        // ENDPOINT COMPLETO
        $endpoint = $this->baseUrl.'/oauth/token';

        // CORPO DA REQUISICAO
        $headers = [
            'Authorization' => 'Basic '.$this->clientId.$this->clientSecret,
            'Content-Type' => 'application/json'
        ];

        // CORPO DA REQUISICAO
        $request = [
            'gran_type' => 'authorization_code',
            'code' => '',
            'redirect_uri' => $this->redirectUri
        ];

        // CONFIGURAÇÃO DO CURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL             => $endpoint,
            CURLOPT_USERPWD         => $this->clientId.':'.$this->clientSecret,
            CURLOPT_HTTPAUTH        => CURLAUTH_BASIC,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_POSTFIELDS      => json_encode($request),
            CURLOPT_SSLCERT         => $this->certificate,
            CURLOPT_SSLCERTPASSWD   => '',
            CURLOPT_HTTPHEADER      => $headers
        ]);

        //EXECUTA O CURL
        $response = curl_exec($curl);
        curl_close($curl);

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
        $endpoint = $this->baseUrl.$resource;

        //HEADERS
        $headers = [
          'Cache-Control: no-cache',
          'Content-type: application/json',
          'Authorization: Bearer'.$this->getAccessToken()
        ];

        // CONFIGURAÇÃO DO CURL
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL             => $endpoint,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_SSLCERT         => $this->certificate,
            CURLOPT_SSLCERTPASSWD   => '',
            CURLOPT_HTTPHEADER      => $headers
        ]);

        switch ($method){
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