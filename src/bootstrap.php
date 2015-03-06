<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use VkUtils\AudioParser;
use VkUtils\Downloader;
use VkUtils\LinkResolver;
use VkUtils\Request as VkRequest;
use VkUtils\MusicServiceProvider;

/** @var $app Application */
$app['debug'] = true;

$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../logs/development.log',
]);

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
]);

$app['vk.downloader'] = $app->share(function () {
    return new Downloader();
});

$app['vk.request'] = $app->share(function () {
    return new VkRequest();
});

$app['vk.resolver'] = $app->share(function (Application $app) {
    return new LinkResolver($app['vk.request']);
});

$app['vk.parser'] = $app->share(function (Application $app) {
    return new AudioParser($app['vk.request']);
});

$app->register(new MusicServiceProvider());

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }
});

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
})->bind('homepage');

$app->post('/get', function (Request $request) use ($app) {
    return $app['music.get']($request);
})->bind('get');

$app->get('/download/{attachment}', function (Application $app, Request $request, $attachment) use ($app) {
    return $app['music.download']($attachment);
})->bind('download');
