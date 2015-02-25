<?php

namespace VkUtils;

use VkUtils\Exceptions\InvalidLink;

class LinkResolver
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Get resolved link to posts
     * @param string $url Input URL
     * @return string $link
     * @throws InvalidLink
     */
    public function resolve($url)
    {
        if (!is_string($url)) {
            throw new InvalidLink($this->request->encodeJson($url));
        }

        // Check wall link
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $params);

        if (array_key_exists('w', $params)) {
            $param = current(explode('/', $params['w']));
            $postID = str_replace('wall', '', $param);

            if ($postID == $param) {
                throw new InvalidLink($url);
            }

            return $this->getWallLink($postID);
        }

        // Check group/user by the title
        $params = parse_url($url);
        $path = trim($params['path'], '/\\');

        // Check public group without title
        if (strpos($path, 'public') === 0) {
            $owner = '-' . substr($path, 6);

            return $this->getLinkByOwner($owner);
        }

        // Check club group without title
        if (strpos($path, 'club') === 0) {
            $owner = '-' . substr($path, 4);

            return $this->getLinkByOwner($owner);
        }

        // Check user without title
        if (strpos($path, 'id') === 0) {
            $owner = substr($path, 2);

            return $this->getLinkByOwner($owner);
        }

        // Return group/user link by title otherwise
        return $this->getLinkByTitle($path);
    }

    /**
     * @param string $postID Post ID
     * @return string Link for post on the wall
     */
    public function getWallLink($postID)
    {
        return "https://api.vk.com/method/wall.getById?posts={$postID}&callback=?";
    }

    /**
     * @param string $owner Post owner ID
     * @param int $count Posts count
     * @param int $offset Posts offset
     * @return string Link for post on the wall
     */
    public function getLinkByOwner($owner, $count = 20, $offset = 0)
    {
        return "https://api.vk.com/method/wall.get?owner_id={$owner}&count={$count}&offset={$offset}&callback=?";
    }

    /**
     * @param string $title Group/user title
     * @param int $count Posts count
     * @param int $offset Posts offset
     * @return string Link for post on the wall
     */
    public function getLinkByTitle($title, $count = 20, $offset = 0)
    {
        return "https://api.vk.com/method/wall.get?domain={$title}&count={$count}&offset={$offset}&callback=?";
    }
}
