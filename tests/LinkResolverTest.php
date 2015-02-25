<?php

namespace VkUtils\tests;

use VkUtils\LinkResolver;
use VkUtils\Request;

class LinkResolverTest extends TestCase
{
    protected static $request;

    public static function setUpBeforeClass()
    {
        self::$request = new Request();
    }

    public function testResolveWallLink()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->resolve('https://vk.com/deep_01?w=wall-73467419_894');
        $this->assertEquals('https://api.vk.com/method/wall.getById?posts=-73467419_894&callback=?', $link);
    }

    public function testInvalidLink()
    {
        $this->setExpectedException(
            'VkUtils\Exceptions\InvalidLink',
            'https://vk.com/deep_01?w=wal-73467419_894'
        );
        $resolver = new LinkResolver(self::$request);
        $resolver->resolve('https://vk.com/deep_01?w=wal-73467419_894');
    }

    public function testInvalidLinkNotString()
    {
        $this->setExpectedException('VkUtils\Exceptions\InvalidLink');
        $resolver = new LinkResolver(self::$request);
        $resolver->resolve([]);
    }

    public function testWallGroupLink()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->getWallLink('-73467419_894');
        $this->assertEquals('https://api.vk.com/method/wall.getById?posts=-73467419_894&callback=?', $link);
    }

    public function testWallUserLink()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->getWallLink('73467419_894');
        $this->assertEquals('https://api.vk.com/method/wall.getById?posts=73467419_894&callback=?', $link);
    }

    public function testGetLinkByOwner()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->getLinkByOwner('123');
        $this->assertEquals('https://api.vk.com/method/wall.get?owner_id=123&count=20&offset=0&callback=?', $link);
    }

    public function testGetLinkByTitle()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->getLinkByTitle('title');
        $this->assertEquals('https://api.vk.com/method/wall.get?domain=title&count=20&offset=0&callback=?', $link);
    }

    public function testGroupPublicLink()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->resolve('https://vk.com/public123');
        $this->assertEquals('https://api.vk.com/method/wall.get?owner_id=-123&count=20&offset=0&callback=?', $link);
    }

    public function testGroupClubLink()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->resolve('https://vk.com/club123');
        $this->assertEquals('https://api.vk.com/method/wall.get?owner_id=-123&count=20&offset=0&callback=?', $link);
    }

    public function testUserID()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->resolve('https://vk.com/id123');
        $this->assertEquals('https://api.vk.com/method/wall.get?owner_id=123&count=20&offset=0&callback=?', $link);
    }

    public function testGroupByTitle()
    {
        $resolver = new LinkResolver(self::$request);
        $link = $resolver->resolve('https://vk.com/random');
        $this->assertEquals('https://api.vk.com/method/wall.get?domain=random&count=20&offset=0&callback=?', $link);
    }
}
