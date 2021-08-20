<?php

    require __DIR__ . '/vendor/autoload.php';

    use \App\Pix\Api;
    use \App\Pix\Payload;
    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;

    $obApiPix = new Api('https://oauth.hm.bb.com.br',
        ''.getenv('CLIENT_ID'),
        ''.getenv('CLIENT_SECRET'),
        'https://www.janelaunica.com.br/retorno-pix',
        ''.getenv('APP_KEY')
    );

    $request = [
          "numeroConvenio" => 62191,
          "indicadorCodigoBarras" => "S",
          "codigoGuiaRecebimento" => "83660000000199800053846101173758000000000000",
          "emailDevedor"=> "contribuinte.silva@provedor.com.br",
          "codigoPaisTelefoneDevedor" => 55,
          "dddTelefoneDevedor" => 61,
          "numeroTelefoneDevedor" => "999731240",
          "codigoSolicitacaoBancoCentralBrasil" => "88a33759-78b0-43b7-8c60-e5e3e7cb55fe",
          "descricaoSolicitacaoPagamento" => "Arrecadação Pix",
          "valorOriginalSolicitacao" => 19.98,
          "cpfDevedor" => "19917885250",
          "nomeDevedor" => "Contribuinte da Silva",
          "quantidadeSegundoExpiracao" => 3600,
          "listaInformacaoAdicional" => [
            [
              "codigoInformacaoAdicional"=> "IPTU",
              "textoInformacaoAdicional"=> "COTA ÚNICA 2021"
            ]
          ]
    ];

    $response = $obApiPix->createCob('ALEK1478523699874563210458', $request);

    if (!isset($response['location'])){
        echo "Problema ao gerar Pix";
        echo "<pre>";
        print_r($response);
        echo "</pre>"; exit;
    }

    // INSTANCIA PRINCIPAL DO PAYLOAD PIX
    $obPayload = (new Payload)
        ->setMerchantName('Aleksander Clay')
        ->setMerchantCity('Belem')
        ->setAmount($response['valor']['original'])
        ->setTxId($response['txid'])
        ->setUrl($response['location'])
        ->setUniquePayment(true);

    //CÓDIGO DE PAGAMENTO PIX
    $payloadQrCode = $obPayload->getPayload();

    //QR CODE
    $obQrCode = new QrCode($payloadQrCode);

    // IMAGEM DO QR CODE
    $image = (new Output\Png)->output($obQrCode, 400);

    ?>

    <h1>Qr Code Dinâmico Pix</h1>

    <br/>

    <img src="data:image/png;base64, <?=base64_encode($image)?>" alt="">

    <br/><br/>

    Código Pix: <br/>
    <strong><?= $payloadQrCode ?></strong>

