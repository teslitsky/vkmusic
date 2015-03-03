<?php

use VkUtils\LinkResolver;
use VkUtils\Audio;
use VkUtils\AudioParser;
use VkUtils\Downloader;
use VkUtils\Exceptions\Exception;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Request;

$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../logs/development.log',
]);

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
]);

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
    $url = urldecode(filter_var($request->request->get('url'), FILTER_SANITIZE_URL));

    $request = new \VkUtils\Request();
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
        $app['monolog']->addError($e->getMessage());
    }

    return $request->encodeJson($result);
})->bind('get');

$app->get('/download/{attachment}/', function ($attachment) use ($app) {
    $attachment = json_decode(urldecode($attachment));
    $audio = new Audio();
    $request = new \VkUtils\Request();
    $audio->setArtist($request->sanitizeParam($attachment->artist));
    $audio->setTitle($request->sanitizeParam($attachment->title));
    $audio->setLink($request->sanitizeParam($attachment->link));

    try {
        $downloader = new Downloader();
        $downloader->download($audio);
    } catch (Exception $e) {
        $app['monolog']->addError($e->getMessage());
    }
})->bind('download');
