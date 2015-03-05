<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

use VkUtils\Audio;
use VkUtils\AudioParser;
use VkUtils\Downloader;
use VkUtils\LinkResolver;
use VkUtils\Request as VkRequest;
use VkUtils\Exceptions\Exception;
use GuzzleHttp\Exception\RequestException;

/** @var $app Application */
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../logs/development.log',
]);

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
]);

$app['downloader'] = $app->share(function () {
    return new Downloader();
});

$app['vkrequest'] = $app->share(function () {
    return new VkRequest();
});

$app['linkresolver'] = $app->share(function (Application $app) {
    return new LinkResolver($app['vkrequest']);
});

$app['parser'] = $app->share(function (Application $app) {
    return new AudioParser($app['vkrequest']);
});

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
    $url = urldecode($app['vkrequest']->sanitizeParam($request->request->get('url'), FILTER_SANITIZE_URL));

    $result = [
        'error' => true,
        'data'  => null,
    ];

    try {
        $link = $app['linkresolver']->resolve($url);
        $audio = $app['parser']->parse($link);
        $result = [
            'error' => false,
            'data'  => $audio,
        ];
    } catch (Exception $e) {
        $result['error'] = $e->getMessage();
    } catch (RequestException $e) {
        $app['monolog']->addError($e->getMessage());
    }

    return $app['vkrequest']->encodeJson($result);
})->bind('get');

$app->get('/download/{attachment}', function (Application $app, Request $request, $attachment) use ($app) {
    $attachment = json_decode(urldecode($attachment));

    $audio = new Audio();
    $audio->setArtist($app['vkrequest']->sanitizeParam($attachment->artist));
    $audio->setTitle($app['vkrequest']->sanitizeParam($attachment->title));
    $audio->setLink($app['vkrequest']->sanitizeParam($attachment->link));

    try {
        $app['downloader']->download($audio);
    } catch (Exception $e) {
        $app['monolog']->addError($e->getMessage());
    }
})->bind('download');
