<?php

namespace VkUtils\tests;

use VkUtils\Audio;

class AudioTest extends TestCase
{
    public function testGetters()
    {
        $audio = new Audio();
        $audio->setLink('https://vk.vom/link/to/audio');
        $audio->setTitle('Demo audio');
        $audio->setArtist('Demo Artist');

        $this->assertEquals('Demo Artist', $audio->getArtist());
        $this->assertEquals('Demo audio', $audio->getTitle());
        $this->assertEquals('https://vk.vom/link/to/audio', $audio->getLink());
        $this->assertEquals('Demo Artist - Demo audio', $audio->getFullTitle());
    }
}
