<?php
require 'bootstrap.php';
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

$pixClient = new \Pix\Sdk\PixClient();

$request = [
    "numeroConvenio" => 763403,
    "indicadorCodigoBarras" => "S",
    "codigoGuiaRecebimento" => "81690000000000134302021082600000000005058809",
    "emailDevedor" => "arrecadacao.pix@bb.com.br",
    "codigoPaisTelefoneDevedor" => 55,
    "dddTelefoneDevedor" => 91,
    "numeroTelefoneDevedor" => "",
    "codigoSolicitacaoBancoCentralBrasil" => "04876447000180",
    "descricaoSolicitacaoPagamento" => "Arrecadação Pix",
    "valorOriginalSolicitacao" => 0.01,
    "cpfDevedor" => "35530561268",
    "nomeDevedor" => "Denise Clay",
    "quantidadeSegundoExpiracao" => 3600,
    "listaInformacaoAdicional" => [
        [
            "codigoInformacaoAdicional" => "IPTU",
            "textoInformacaoAdicional" => "COTA ÚNICA 2021"
        ]
    ]
];

$response = $pixClient->request('POST', 'pix-bb/v1/arrecadacao-qrcodes', $request);

//CÓDIGO DE PAGAMENTO PIX
$payloadQrCode = $response->qrCode;

//echo "<pre>";
//print_r($response);
//echo "</pre>"; exit;

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
<br>
----------------------------------------------------
<br>
<strong><?= '00020101021226870014br.gov.bcb.pix2565qrcodepix-h.bb.com.br/pix/v2/b082bf1e-0060-4c05-aea0-a16123f606fd5204000053039865802BR5925SECRETARIA DA RECEITA FED6008BRASILIA62070503***63045B61' ?></strong>
