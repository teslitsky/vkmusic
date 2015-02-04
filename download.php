<?php

require_once __DIR__ . '/vendor/autoload.php';

use VkUtils\Audio;
use VkUtils\AudioParser;
use VkUtils\Downloader;
use VkUtils\Exceptions\Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('main');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/main.log', Logger::DEBUG));

$attachment = $_GET['attachment'];
$audio = new Audio();
$parser = new AudioParser();
$audio->setArtist($parser->sanitizeParam($attachment['artist']));
$audio->setTitle($parser->sanitizeParam($attachment['title']));
$audio->setLink($parser->sanitizeParam($attachment['link']));

try {
    $downloader = new Downloader();
    $downloader->download($audio);
} catch (Exception $e) {
    $log->addError($e->getMessage());
}
