<?php

namespace VkUtils;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface as GuzzleRequest;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Request implements RequestInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
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