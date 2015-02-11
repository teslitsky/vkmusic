<?php

namespace VkUtils\tests;

use VkUtils\Request;
use VkUtils\AudioParser;

class AudioParserTest extends TestCase
{
    protected static $request;

    public static function setUpBeforeClass()
    {
        self::$request = new Request();
    }

    public function testParseEmptyLink()
    {
        $link = '';
        $this->setExpectedException('VkUtils\Exceptions\InvalidLink', $link);
        $parser = new AudioParser(self::$request);
        $parser->parse($link);
    }

    public function testParseErrorLink()
    {
        $link = 'error-link';
        $this->setExpectedException('\RuntimeException', $link);
        $parser = new AudioParser(self::$request);
        $parser->parse($link);
    }

    public function testParseEmptyAttachments()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=1139277_3645&callback=?';
        $this->setExpectedException('VkUtils\Exceptions\EmptyAttachments', $link);
        $parser = new AudioParser(self::$request);
        $parser->parse($link);
    }

    public function testParseEmptyAudioAttachments()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=-56339827_118&callback=?';
        $parser = new AudioParser(self::$request);
        $this->assertCount(0, $parser->parse($link));
    }

    public function testSingleAttachment()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=-77831791_3075&callback=?';
        $parser = new AudioParser(self::$request);
        $files = $parser->parse($link);
        $this->assertInternalType('array', $files);
        $this->assertCount(1, $files);
    }

    public function testMultipleAttachments()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=216463522_4347&callback=?';
        $parser = new AudioParser(self::$request);
        $files = $parser->parse($link);
        $this->assertInternalType('array', $files);
        $this->assertCount(2, $files);
    }
}
