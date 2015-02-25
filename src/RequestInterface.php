<?php

namespace VkUtils;

interface RequestInterface
{
    /**
     * Make GET request
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function get($url = null, $options = []);

    /**
     * Make GET request and return JSON encoded result
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public function getJson($url = null, $options = []);

    /**
     * @param string $param Input param for sanitize
     * @return string Sanitized param
     */
    public function sanitizeParam($param);

    /**
     * Encode data to JSON
     * @param mixed $data Input data
     * @return string JSON encoded data
     */
    public function encodeJson($data);

    /**
     * Decode data from JSON
     * @param mixed $data Input data
     * @return mixed mixed Decoded data
     */
    public function decodeJson($data);
}