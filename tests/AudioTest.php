<?php

namespace VkUtils\tests;

use VkUtils\Audio;

class AudioTest extends TestCase
{
    /**
     * @var Audio
     */
    private static $audio;

    public static function setUpBeforeClass()
    {
        $audio = new Audio();
        $audio->setLink('https://vk.vom/link/to/audio');
        $audio->setTitle('Demo audio');
        $audio->setArtist('Demo Artist');
        self::$audio = $audio;
    }

    public function testSettersAndGetters()
    {
        $this->assertEquals('Demo Artist', self::$audio->getArtist());
        $this->assertEquals('Demo audio', self::$audio->getTitle());
        $this->assertEquals('https://vk.vom/link/to/audio', self::$audio->getLink());
    }

    public function testFullTitle()
    {
        $this->assertEquals('Demo Artist - Demo audio', self::$audio->getFullTitle());
    }

    public function testFormattedDuration()
    {
        self::$audio->setDuration(83);
        $this->assertEquals('83', self::$audio->getDuration());
        $this->assertEquals('01:23', self::$audio->getFormattedDuration());

        self::$audio->setDuration(0);
        $this->assertEquals('0', self::$audio->getDuration());
        $this->assertEquals('00:00', self::$audio->getFormattedDuration());
    }
}
