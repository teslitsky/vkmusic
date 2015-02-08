<?php

namespace VkUtils;

use GuzzleHttp\Client;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use VkUtils\Exceptions\EmptyAttachments;
use VkUtils\Exceptions\InvalidLink;

class AudioParser
{
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
            $audio->setArtist($this->sanitizeParam($attachment['audio']['artist']));
            $audio->setTitle($this->sanitizeParam($attachment['audio']['title']));
            $audio->setLink($this->sanitizeParam($attachment['audio']['url']));
            $files[] = $audio;
        }

        return $files;
    }

    /**
     * @param mixed $param Input param for sanitize
     * @return string Sanitized param
     */
    public function sanitizeParam($param)
    {
        return html_entity_decode(filter_var($param, FILTER_SANITIZE_STRING));
    }

    /**
     * Encode data to JSON
     * @param mixed $data Input data
     * @return string JSON encoded data
     */
    public function getJsonRequest($data)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($data, 'json');
    }

    /**
     * Decode data from JSON
     * @param mixed $data Input data
     * @return mixed mixed Decoded data
     */
    public function decodeJson($data)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->decode($data, 'json');
    }
}