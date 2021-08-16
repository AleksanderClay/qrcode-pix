<?php

    require __DIR__ . '/vendor/autoload.php';

    use \App\Pix\Api;
    use \App\Pix\Payload;
    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;

    $obApiPix = new Api('https://api.hm.bb.com.br', '', '', '');

    $response = $obApiPix->consultaCob('ALEK1478523699874563210458');

    if (!isset($response['location'])){
        echo "Problema ao gerar Pix";
        echo "<pre>";
        print_r($response);
        echo "</pre>"; exit;
    }
