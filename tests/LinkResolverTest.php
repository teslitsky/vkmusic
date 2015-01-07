<?php

namespace VkUtils\tests;

use VkUtils\LinkResolver;

class LinkResolverTest extends TestCase
{
    public function testResolveWallLink()
    {
        $resolver = new LinkResolver();
        $link = $resolver->resolve('https://vk.com/deep_01?w=wall-73467419_894');
        $this->assertEquals('https://api.vk.com/method/wall.getById?posts=-73467419_894&callback=?', $link);
    }

    public function testInvalidLinkWithoutParams()
    {
        $this->setExpectedException(
            'VkUtils\Exceptions\InvalidLinkWithoutParams',
            'https://vk.com/deep_01'
        );
        $resolver = new LinkResolver();
        $resolver->resolve('https://vk.com/deep_01');
    }

    public function testInvalidLink()
    {
        $this->setExpectedException(
            'VkUtils\Exceptions\InvalidLink',
            'https://vk.com/deep_01?w=wal-73467419_894'
        );
        $resolver = new LinkResolver();
        $resolver->resolve('https://vk.com/deep_01?w=wal-73467419_894');
    }

    public function testWallGroupLink()
    {
        $resolver = new LinkResolver();
        $link = $resolver->getWallLink('-73467419_894');
        $this->assertEquals('https://api.vk.com/method/wall.getById?posts=-73467419_894&callback=?', $link);
    }

    public function testWallUserLink()
    {
        $resolver = new LinkResolver();
        $link = $resolver->getWallLink('73467419_894');
        $this->assertEquals('https://api.vk.com/method/wall.getById?posts=73467419_894&callback=?', $link);
    }
}
