<?php

namespace Pix\Sdk;

use Curl\Curl;

class PixClient {

    private $curl;
    private $method;
    private $body;
    private $params;
    private $clientId;
    private $clientSecret;
    private $tokenBasic;
    private $appKey;
    private $oauthUrl;
    private $apiUrl;


    public function __construct()
    {
        $this->curl = new Curl();
        $this->clientId = $_ENV['PIX_CLIENT_ID'];
        $this->clientSecret = $_ENV['PIX_CLIENT_SECRET'];
        $this->tokenBasic = $_ENV['PIX_TOKEN_BASIC'];
        $this->oauthUrl = $_ENV['PIX_URL_OAUTH'];
        $this->apiUrl = $_ENV['PIX_URL_API'];
        $this->appKey = $_ENV['PIX_APP_KEY'];
        $this->setBasicAuthentication();
        $this->setBasicAuthorization();
    }

    private function setMethod($method)
    {
        $this->method = strtolower($method);
        $this->curl->setOpt(CURLOPT_CUSTOMREQUEST, $method);
    }

    protected function setHeader()
    {
        $this->curl->setHeader("Content-Type", "application/json");
    }

    protected function setParams($params)
    {
        $this->params = "?" . http_build_query(array_merge($params, ['gw-dev-app-key' => $this->appKey]));
    }

    protected function setBody($body)
    {
        $this->body = $body;
    }

    public function request($method, $uri, $body = [], $params = [])
    {
        $this->setMethod($method);
        $this->setBody($body);
        $this->setParams($params);
        $this->setBearerAuthorizationToken();

        $this->curl->{"$this->method"}($this->apiUrl . '/' . $uri . $this->params, $this->body, true);

        return json_decode($this->curl->getResponse());
    }

    private function setBasicAuthentication()
    {
        $this->curl->setBasicAuthentication(
            $this->clientId,
            $this->clientSecret
        );
    }

    private function setBearerAuthorizationToken()
    {
        $this->curl->setHeader("Authorization", 'Bearer ' . $this->getAuthorizationToken()->access_token);

        return $this;
    }

    private function setBasicAuthorization()
    {
        $this->curl->setHeader("Authorization", 'Basic ' . $this->tokenBasic);
    }

    private function getAuthorizationToken()
    {
        $this->setBasicAuthentication();
        $this->curl->post($this->oauthUrl.'/oauth/token?grant_type=client_credentials');

        return json_decode($this->curl->getResponse());
    }
}

