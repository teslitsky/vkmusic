<?php

namespace VkUtils;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface as GuzzleRequest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class Request implements RequestInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new GetSetMethodNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->client = new Client();
    }

    /**
     * Make GET request
     * @param string $url Request URL
     * @param array $options Request options
     * @return GuzzleRequest
     * @throws \RuntimeException
     */
    public function get($url = null, $options = [])
    {
        return $this->client->get($url, $options);
    }

    /**
     * Make GET request and return JSON encoded result
     * @param string $url Request URL
     * @param array $options Request options
     * @return array
     * @throws \RuntimeException
     */
    public function getJson($url = null, $options = [])
    {
        return $this->client->get($url, $options)->json();
    }

    /**
     * @param string $param Input param for sanitize
     * @param int $flag Filtering flag
     * @return string Sanitized param
     */
    public function sanitizeParam($param, $flag = FILTER_SANITIZE_STRING)
    {
        return html_entity_decode(filter_var($param, $flag));
    }

    /**
     * Encode data to JSON
     * @param mixed $data Input data
     * @return string JSON encoded data
     */
    public function encodeJson($data)
    {
        return $this->serializer->serialize($data, 'json');
    }

    /**
     * Decode data from JSON
     * @param mixed $data Input data
     * @return mixed mixed Decoded data
     */
    public function decodeJson($data)
    {
        return $this->serializer->decode($data, 'json');
    }
}
