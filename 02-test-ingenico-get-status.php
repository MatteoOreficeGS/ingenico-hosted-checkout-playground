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

//$callContext = new CallContext();
//$callContext->setIdempotenceKey($idempotenceKey);


$communicatorConfiguration =
    new CommunicatorConfigurationAlias(
        $_ENV['API_KEY'], $_ENV['API_SECRET'], 'https://eu.sandbox.api-ingenico.com', 'GSped'
    );
$connection = new DefaultConnectionAlias();
$communicator = new CommunicatorAlias($connection, $communicatorConfiguration);

$client = new ClientAlias($communicator);

// hosted checkout id
$response = $client->merchant("1221")->hostedcheckouts()->get($argv[1]);

printf("hostedCheckoutId                    : %s\n",$argv[1]);
printf("status                              : %s\n",$response->status);
printf("payment.status                      : %s\n",$response->createdPaymentOutput->payment->status);
printf("payment.statusOutput.statusCategory : %s\n",$response->createdPaymentOutput->payment->statusOutput->statusCategory);
printf("payment.statusOutput.isAuthorized   : %s\n",$response->createdPaymentOutput->payment->statusOutput->isAuthorized);
printf("tokens                              : %s\n",$response->createdPaymentOutput->tokens);
printf("tokenizationSucceeded               : %s\n",$response->createdPaymentOutput->tokenizationSucceeded);

printf("\nCall this php script to approve the payment\n> php 03-test-ingenico-approve.php %s\n",
    $response->createdPaymentOutput->payment->id);

