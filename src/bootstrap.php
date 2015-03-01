<?php

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../views',
]);

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig');
});
