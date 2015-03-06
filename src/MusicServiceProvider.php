<?php

namespace VkUtils;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use VkUtils\Exceptions\Exception as VkException;
use GuzzleHttp\Exception\RequestException;

class MusicServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $app['music.get'] = $app->protect(function (Request $request) use ($app) {
            $url = urldecode($app['vk.request']->sanitizeParam($request->request->get('url'), FILTER_SANITIZE_URL));

            $result = [
                'error' => true,
                'data'  => null,
            ];

            try {
                $link = $app['vk.resolver']->resolve($url);
                $audio = $app['vk.parser']->parse($link);
                $result = [
                    'error' => false,
                    'data'  => $audio,
                ];
            } catch (VkException $e) {
                $result['error'] = $e->getMessage();
            } catch (RequestException $e) {
                $app['monolog']->addError($e->getMessage());
            }

            return $app['vk.request']->encodeJson($result);
        });

        $app['music.download'] = $app->protect(function ($attachment) use ($app) {
            $attachment = json_decode(urldecode($attachment));

            $audio = new Audio();
            $audio->setArtist($app['vk.request']->sanitizeParam($attachment->artist));
            $audio->setTitle($app['vk.request']->sanitizeParam($attachment->title));
            $audio->setLink($app['vk.request']->sanitizeParam($attachment->link));

            try {
                $app['vk.downloader']->download($audio);
            } catch (VkException $e) {
                $app['monolog']->addError($e->getMessage());
            }
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
