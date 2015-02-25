<?php

require_once __DIR__ . '/../vendor/autoload.php';

use VkUtils\LinkResolver;
use VkUtils\Request;
use VkUtils\AudioParser;
use VkUtils\Exceptions\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Exception\RequestException;

$log = new Logger('main');
$log->pushHandler(new StreamHandler(__DIR__ . '/../../logs/main.log', Logger::DEBUG));

$url = html_entity_decode(filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL));

$request = new Request();
$resolver = new LinkResolver($request);
$parser = new AudioParser($request);
$result = [
    'error' => true,
    'data'  => null,
];

try {
    $link = $resolver->resolve($url);

    $audio = $parser->parse($link);
    $result = [
        'error' => false,
        'data'  => $audio,
    ];
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
} catch (RequestException $e) {
    $log->addError($e->getMessage());
}

echo $request->encodeJson($result);
