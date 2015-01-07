<?php

namespace VkUtils;

use VkUtils\Exceptions\InvalidLink;
use VkUtils\Exceptions\InvalidLinkWithoutParams;

class LinkResolver
{
    /**
     * Get resolved link for post
     * @param string $url Post URL
     * @return string $link
     * @throws InvalidLink
     * @throws InvalidLinkWithoutParams
     */
    public function resolve($url)
    {
        $link = false;
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);

        if (!count($params)) {
            throw new InvalidLinkWithoutParams($url);
        }

        if (array_key_exists('w', $params)) {
            $param = current(explode('/', $params['w']));
            $postID = str_replace('wall', '', $param);

            if ($postID == $param) {
                throw new InvalidLink($url);
            }

            $link = $this->getWallLink($postID);
        }

        return $link;
    }

    public function getWallLink($postID)
    {
        return "https://api.vk.com/method/wall.getById?posts={$postID}&callback=?";
    }
}