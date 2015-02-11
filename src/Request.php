<?php

namespace VkUtils;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Request
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct()
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
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