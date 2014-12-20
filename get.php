<?php

require_once __DIR__ . '/vendor/autoload.php';

use VkUtils\LinkResolver;
use VkUtils\Downloader;
use VkUtils\Exceptions\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('main');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/main.log', Logger::DEBUG));

$url = html_entity_decode(filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL));

$resolver = new LinkResolver();
try {
    $postID = $resolver->resolve($url);
    $downloader = new Downloader();
    $downloader->download($postID);
} catch (Exception $e) {
    $log->addError($e->getMessage());
}
