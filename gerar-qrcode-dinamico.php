<?php

    require __DIR__ . '/vendor/autoload.php';

    use \App\Pix\Api;
    use \App\Pix\Payload;
    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;

    $obApiPix = new Api('https://api.hm.bb.com.br',
        'eyJpZCI6IjQzMWYiLCJjb2RpZ29QdWJsaWNhZG9yIjowLCJjb2RpZ29Tb2Z0d2FyZSI6MjA4MDcsInNlcXVlbmNpYWxJbnN0YWxhY2FvIjoxfQ',
        'eyJpZCI6IjUyZDhhYTYtNTVmNi00MDFmIiwiY29kaWdvUHVibGljYWRvciI6MCwiY29kaWdvU29mdHdhcmUiOjIwODA3LCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE2MjkxMzkxNzE2NjB9',
        '',
        'https://www.janelaunica.com.br/retorno-pix'
    );

    $request = [
        'calendario' => [
            'expiracao' => 3600
        ],
        'devedor' => [
            'cnpj' => "12345678000195",
            'nome' => "Empresa de Serviços SA"
        ],
        'valor' => [
            'original' => "37.00",
          ],
        'chave' => "01141107279",
        'solicitacaoPagador' => "Serviço realizado.",
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
    <strong><?=$payloadQrCode?></strong>

