<?php

namespace VkUtils;

use GuzzleHttp\Client;
use VkUtils\Exceptions\EmptyAttachments;
use VkUtils\Exceptions\InvalidLink;

class AudioParser
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return parsed array of Audio objects
     * @param string $postLink Link for post
     * @return array Parsed Audio objects
     * @throws EmptyAttachments
     * @throws InvalidLink
     */
    public function parse($postLink)
    {
        if (empty($postLink)) {
            throw new InvalidLink($postLink);
        }

        $files = [];
        $client = new Client();

        $response = $client->get($postLink);
        $json = $response->json();
        if (!isset($json['response'][0]['attachments'])) {
            throw new EmptyAttachments($postLink);
        }

        $attachments = $json['response'][0]['attachments'];
        $iterator = new AudioFilterIterator($attachments);
        foreach ($iterator as $attachment) {
            $audio = new Audio();
            $audio->setArtist($this->request->sanitizeParam($attachment['audio']['artist']));
            $audio->setTitle($this->request->sanitizeParam($attachment['audio']['title']));
            $audio->setLink($this->request->sanitizeParam($attachment['audio']['url']));
            $files[] = $audio;
        }

        return $files;
    }
}