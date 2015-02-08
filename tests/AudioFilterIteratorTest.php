<?php

namespace VkUtils\tests;

use VkUtils\AudioFilterIterator;

class AudioFilterIteratorTest extends TestCase
{
    public function testGetters()
    {
        $audioArray = [
            0 => [
                'type' => 'audio',
            ],
            1 => [
                'type' => 'video',
            ],
            2 => [
                'type' => 'image',
            ],
            3 => [
                'type' => 'audio',
            ],
            4 => [
                'type' => '',
            ],
            5 => [
                'type' => false,
            ],
            6 => [
                'notType' => false,
            ],
        ];

        $counterAudio = 0;
        $iterator = new AudioFilterIterator($audioArray);
        foreach ($iterator as $key => $audio) {
            $counterAudio++;
        }

        $counterNotAudio = count($audioArray) - $counterAudio;

        $this->assertEquals(2, $counterAudio);
        $this->assertEquals(5, $counterNotAudio);
    }
}
