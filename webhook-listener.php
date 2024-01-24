<?php

require __DIR__ . '/vendor/autoload.php';

$http = new React\Http\HttpServer(function (Psr\Http\Message\ServerRequestInterface $request) {

    $data = [
        'METHOD' => (string)$request->getMethod(),
        'URI' => (string)$request->getUri(),

    ] + $request->getServerParams() + ['BODY' => (string)$request->getBody()];

    printf(PHP_EOL.'> New request received'.PHP_EOL);
    foreach ($data as $k => $value) {
        printf('%20s: %s'.PHP_EOL,$k,$value);
    }
    $validationHeader = $request->getHeader('X-GCS-Webhooks-Endpoint-Verification');
    if(sizeof($validationHeader)>0){
        printf('%20s: %s'.PHP_EOL,'X-GCS-Webhooks-Endpoint-Verification',$request->getHeader('X-GCS-Webhooks-Endpoint-Verification')[0]);
        return \React\Http\Message\Response::plaintext((string)$request->getHeader('X-GCS-Webhooks-Endpoint-Verification')[0]);
    }
    return \React\Http\Message\Response::plaintext('OK');
});

$socket = new React\Socket\SocketServer($listen = $argv[1] ?? '0.0.0.0:8080');
$http->listen($socket);

$socket->on('error', function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

echo 'Listening on :' . $listen . PHP_EOL;