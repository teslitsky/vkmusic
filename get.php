<?php

require_once __DIR__ . '/vendor/autoload.php';

use VkUtils\LinkResolver;
use VkUtils\Downloader;

$url = html_entity_decode(filter_input(INPUT_GET, 'wall', FILTER_SANITIZE_URL));

$resolver = new LinkResolver();
$link = $resolver->resolve($url);
$downloader = new Downloader();
$downloader->download($link);
