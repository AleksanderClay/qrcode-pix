<?php

    require __DIR__.'/vendor/autoload.php';

    use \App\Pix\Payload;
    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;

    // INSTANCIA PRINCIPAL DO PAYLOAD PIX
    $obPayload = (new Payload)
        ->setPixKey('01141107279')
        ->setDescription('Pagamento do Pedido')
        ->setMerchantName('Aleksander Clay')
        ->setMerchantCity('Belem')
        ->setTxId('10id')
        ->setAmount('100.00');

    //CÓDIGO DE PAGAMENTO PIX
    $payloadQrCode = $obPayload->getPayload();

    //QR CODE
    $obQrCode = new QrCode($payloadQrCode);

    // IMAGEM DO QR CODE
    $image = (new Output\Png)->output($obQrCode, 400);

?>

    <h1>Qr Code Pix</h1>

    <br/>

    <img src="data:image/png;base64, <?=base64_encode($image)?>" alt="">

    <br/><br/>

    Código Pix: <br/>
    <strong><?=$payloadQrCode?></strong>
