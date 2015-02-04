<?php

require_once __DIR__ . '/vendor/autoload.php';

use VkUtils\LinkResolver;
use VkUtils\AudioParser;
use VkUtils\Exceptions\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Exception\RequestException;

$log = new Logger('main');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/main.log', Logger::DEBUG));

$url = html_entity_decode(filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL));

$resolver = new LinkResolver();
$parser = new AudioParser();
$result = [
    'error' => true,
    'data'  => null,
];

try {
    $postLink = $resolver->resolve($url);
    $audio = $parser->parse($postLink);
    $result = [
        'error' => false,
        'data'  => $audio,
    ];
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
} catch (RequestException $e) {
    $log->addError($e->getMessage());
}

echo $parser->getJsonRequest($result);
exit;
