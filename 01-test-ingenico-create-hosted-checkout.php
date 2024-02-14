<?php

use Ingenico\Connect\Sdk\Client as ClientAlias;
use Ingenico\Connect\Sdk\Communicator as CommunicatorAlias;
use Ingenico\Connect\Sdk\CommunicatorConfiguration as CommunicatorConfigurationAlias;
use Ingenico\Connect\Sdk\DefaultConnection as DefaultConnectionAlias;
use Ingenico\Connect\Sdk\Domain\Hostedcheckout\CreateHostedCheckoutRequest;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Order as OrderAlias;
use Ingenico\Connect\Sdk\Domain\Definitions\Card;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\CardPaymentMethodSpecificInput;

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//$callContext = new CallContext();
//$callContext->setIdempotenceKey($idempotenceKey);

$logFile = fopen('hosted-checkout-test.log','a');


$communicatorConfiguration =
    new CommunicatorConfigurationAlias(
        $_ENV['API_KEY'], $_ENV['API_SECRET'], 'https://eu.preprod.api-ingenico.com', 'GSped'
    );
$connection = new DefaultConnectionAlias();
$communicator = new CommunicatorAlias($connection, $communicatorConfiguration);

$client = new ClientAlias($communicator);
$client->setClientMetaInfo(json_encode(['msg'=>"consumer specific JSON meta info"]));
$client->enableLogging(new \Ingenico\Connect\Sdk\ResourceLogger($logFile));

$amountOfMoney = new \Ingenico\Connect\Sdk\Domain\Definitions\AmountOfMoney();
$amountOfMoney->amount = 3500;
$amountOfMoney->currencyCode = "USD";

$billingAddress = new \Ingenico\Connect\Sdk\Domain\Definitions\Address();
$billingAddress->countryCode = "IT";

$customer = new \Ingenico\Connect\Sdk\Domain\Payment\Definitions\Customer();
$customer->locale = "it_IT";
$customer->billingAddress = $billingAddress;

$order = new OrderAlias();
$order->amountOfMoney = $amountOfMoney;
$order->customer = $customer;

$body = new CreateHostedCheckoutRequest();
$body->order = $order;


$cardPaymentMethodSpecificInput = new CardPaymentMethodSpecificInput();
$cardPaymentMethodSpecificInput->authorizationMode = 'SALE';
$body->cardPaymentMethodSpecificInput = $cardPaymentMethodSpecificInput;

$hcsi = new \Ingenico\Connect\Sdk\Domain\Hostedcheckout\Definitions\HostedCheckoutSpecificInput();
$hcsi->returnUrl = $_ENV['RETURN_URL'] ?? 'https://www.gsped.it';
$hcsi->showResultPage = true;
$body->hostedCheckoutSpecificInput = $hcsi;
$body->hostedCheckoutSpecificInput->paymentProductFilters = new \Ingenico\Connect\Sdk\Domain\Hostedcheckout\Definitions\PaymentProductFiltersHostedCheckout();
$body->hostedCheckoutSpecificInput->paymentProductFilters->restrictTo = new \Ingenico\Connect\Sdk\Domain\Definitions\PaymentProductFilter();
$body->hostedCheckoutSpecificInput->paymentProductFilters->restrictTo->groups = ['cards'];
//$body->hostedCheckoutSpecificInput->paymentProductFilters->restrictTo->products = [1];



$response = $client->merchant(2502)->hostedcheckouts()->create($body);

// questi vanno salvati
printf("RETURNMAC         : %s\n",$response->RETURNMAC);
printf("partialRedirectUrl: %s\n",$response->partialRedirectUrl);
printf("merchantReference : %s\n",$response->merchantReference?:'merchantReference is EMPTY !!');
printf("\nPlease, complete payment on: %s\n\n",'https://payment.'.$response->partialRedirectUrl);
printf("Select VISA payment method\n");
printf("Use this CC number test case : %s\n",'4012000033330026');
printf("Use this CC CCV : %s\n",'123');
printf("Use this CC expiry date : %s\n",'0624');
printf("\nCall this php script after completing online payment\n> php 02-test-ingenico-get-status.php %s\n",
    $response->hostedCheckoutId);
