<?php

use Ingenico\Connect\Sdk\Client as ClientAlias;
use Ingenico\Connect\Sdk\Communicator as CommunicatorAlias;
use Ingenico\Connect\Sdk\CommunicatorConfiguration as CommunicatorConfigurationAlias;
use Ingenico\Connect\Sdk\DefaultConnection as DefaultConnectionAlias;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\CreateHostedCheckoutRequest;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Order as OrderAlias;

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$communicatorConfiguration =
    new CommunicatorConfigurationAlias(
        $_ENV['API_KEY'], $_ENV['API_SECRET'], 'https://eu.sandbox.api-ingenico.com', 'GSped'
    );
$connection = new DefaultConnectionAlias();
$communicator = new CommunicatorAlias($connection, $communicatorConfiguration);

$client = new ClientAlias($communicator);
$client->setClientMetaInfo(json_encode(['msg'=>"consumer specific JSON meta info"]));


$references = new \Ingenico\Connect\Sdk\Domain\Payment\Definitions\OrderReferencesApprovePayment();
$references->merchantReference = "XXX";

$order = new \Ingenico\Connect\Sdk\Domain\Payment\Definitions\OrderApprovePayment();
$order->references = $references;

$body = new \Ingenico\Connect\Sdk\Domain\Payment\ApprovePaymentRequest();

$body->directDebitPaymentMethodSpecificInput = new
\Ingenico\Connect\Sdk\Domain\Payment\Definitions\ApprovePaymentDirectDebitPaymentMethodSpecificInput();

//$body->directDebitPaymentMethodSpecificInput->token = $argv[1];
$body->directDebitPaymentMethodSpecificInput->dateCollect = \Carbon\Carbon::now()->format('Ymd');

$body->order = $order;

$response = $client->merchant("1221")->payments()->approve($argv[1], $body);
var_dump($response->toJson());


//$body = new \Ingenico\Connect\Sdk\Domain\Payment\CapturePaymentRequest();
//$body->amount = 3500;
//
//$response = $client->merchant("1221")->payments()->capture($argv[1], $body);
//var_dump($response->toJson());