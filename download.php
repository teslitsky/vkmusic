<?php

require_once __DIR__ . '/vendor/autoload.php';

use VkUtils\Audio;
use VkUtils\Request;
use VkUtils\AudioParser;
use VkUtils\Downloader;
use VkUtils\Exceptions\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('main');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/main.log', Logger::DEBUG));

$attachment = $_GET['attachment'];
$audio = new Audio();
$request = new Request();
$parser = new AudioParser($request);
$audio->setArtist($request->sanitizeParam($attachment['artist']));
$audio->setTitle($request->sanitizeParam($attachment['title']));
$audio->setLink($request->sanitizeParam($attachment['link']));

try {
    $downloader = new Downloader();
    $downloader->download($audio);
} catch (Exception $e) {
    $log->addError($e->getMessage());
}
