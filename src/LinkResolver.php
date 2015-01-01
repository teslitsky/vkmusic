<?php

namespace VkUtils;

use VkUtils\Exceptions\NoResolvedParams;

class LinkResolver
{
    /**
     * @param string $url
     * @return string $postID
     * @throws NoResolvedParams
     */
    public function resolve($url)
    {
        $link = false;
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);

        if (!count($params)) {
            throw new NoResolvedParams('No resolved params by URL ' . $url);
        }

        if (array_key_exists('w', $params)) {
            $param = current(explode('/', $params['w']));
            $postID = str_replace('wall', '', $param);
            $link = $this->getWallLink($postID);
        }

        if (!$link) {
            throw new NoResolvedParams('No resolved params by URL ' . $url);
        }

        return $link;
    }

    public function getWallLink($postID)
    {
        return "https://api.vk.com/method/wall.getById?posts={$postID}&callback=?";
    }
}