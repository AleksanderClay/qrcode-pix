<?php

namespace App\Pix;

class Payload
{
    /**
     * IDs do Payload do Pix
     * @var string
     */
    const ID_PAYLOAD_FORMAT_INDICATOR = '00';
    const ID_POINT_OF_INITIATION_METHOD = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
    const ID_MERCHANT_ACCOUNT_INFORMATION_URL = '25';
    const ID_MERCHANT_CATEGORY_CODE = '52';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_MERCHANT_NAME = '59';
    const ID_MERCHANT_CITY = '60';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
    const ID_CRC16 = '63';

    /**
     * Chave Pix
     * @var string
     */
    private $pixKey;

    /**
     * Descrição do pagamento
     * @var string
     */
    private $description;

    /**
     * Nome do titular da conta
     * @var string
     */
    private $merchantName;

    /**
     * Cidade do titular da conta
     * @var string
     */
    private $merchantCity;

    /**
     * ID da transação
     * @var string
     */
    private $txId;

    /**
     * Valor da transação
     * @var string
     */
    private $amount;

    /**
     * Define se o pagamento deve ser feito apenas uma vez
     * @var boolean
     */
    private $uniquePayment = false;

    /**
     * URL do payload dinâmico
     * @var string
     */
    private $url;

    /**
     * Método responsável por definir o valor da $pixKey
     * @param string $pixKey
     */
    public function setPixKey($pixKey)
    {
        $this->pixKey = $pixKey;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $description
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $url
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $uniquePayment
     * @param boolean $uniquePayment
     */
    public function setUniquePayment($uniquePayment)
    {
        $this->uniquePayment = $uniquePayment;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $merchantName
     * @param string $merchantName
     */
    public function setMerchantName($merchantName)
    {
        $this->merchantName = $merchantName;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $merchantCity
     * @param string $merchantCity
     */
    public function setMerchantCity($merchantCity)
    {
        $this->merchantCity = $merchantCity;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $txId
     * @param string $txId
     */
    public function setTxId($txId)
    {
        $this->txId = $txId;
        return $this;
    }

    /**
     * Método responsável por definir o valor da $amount
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (string)number_format($amount, 2, ',', '');
        return $this;
    }

    /**
     * Responsavel por retonar o valor completo de um obejto do Payload
     * @param string $id
     * @param string $value
     * @return string $id.$size.$value
     */
    public function getValue($id, $value)
    {
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);

        return $id . $size . $value;
    }

    /**
     * Método responsável por retornar os valores completos da informação da conta
     * @return string
     */
    public function getMerchantAccountInformation()
    {
        // DOMINÍO DO BANCO
        $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');

        // CHAVE PIX
        $pixKey = strlen($this->pixKey) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pixKey) : '';

        // DESCRIÇÃO DO PAGAMENTO
        $description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';

        // URL DO QR CODE DINÂMICO
        $url = strlen($this->url) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_URL, $this->url) : '';

        // VALOR COMPLETO DA CONTA
        return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui . $pixKey . $description . $url);
    }

    /**
     * Método responsável por os valores completos do campo adicional do pix (TXID)
     */
    private function getAdditionalDataFieldTemplate()
    {
        //TXID
        $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txId);

        // RETORNA O VALOR COMPLETO
        return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
    }

    /**
     * Método responsável por retornar o valor do ID_POINT_OF_INITIATION_METHOD
     * @return string
     */
    private function getUniquePayment()
    {
        return $this->uniquePayment ? $this->getValue(self::ID_POINT_OF_INITIATION_METHOD, '12') : '';
    }

    /**
     * Método responsável por gerar o código completo do Payload Pix
     * @return string
     */
    public function getPayload()
    {
        //CRIA PAYLOAD
        $payload = $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01') .
            $this->getUniquePayment() .
            $this->getMerchantAccountInformation() .
            $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000') .
            $this->getValue(self::ID_TRANSACTION_CURRENCY, '986') .
            $this->getValue(self::ID_TRANSACTION_AMOUNT, '100.00') .
            $this->getValue(self::ID_COUNTRY_CODE, 'BR') .
            $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName) .
            $this->getValue(self::ID_MERCHANT_CITY, $this->merchantCity) .
            $this->getAdditionalDataFieldTemplate();

        //RETORNA O PAYLOAD + CRC16
        return $payload . $this->getCRC16($payload);
    }

    /**
     * Método responsável por calcular o valor da hash de validação do código pix
     * @return string
     */
    private function getCRC16($payload)
    {
        //ADICIONA DADOS GERAIS NO PAYLOAD
        $payload .= self::ID_CRC16 . '04';

        //DADOS DEFINIDOS PELO BACEN
        $polinomio = 0x1021;
        $resultado = 0xFFFF;

        //CHECKSUM
        if (($length = strlen($payload)) > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $resultado ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                    $resultado &= 0xFFFF;
                }
            }
        }

        //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
        return self::ID_CRC16 . '04' . strtoupper(dechex($resultado));
    }


}