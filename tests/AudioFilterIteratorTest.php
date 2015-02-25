<?php

namespace VkUtils\tests;

use VkUtils\AudioFilterIterator;

class AudioFilterIteratorTest extends TestCase
{
    public function testIterator()
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

        $audioCounter = 0;
        $iterator = new AudioFilterIterator($audioArray);
        foreach ($iterator as $key => $audio) {
            $audioCounter++;
        }

        $counterNotValid = count($audioArray) - $audioCounter;

        $this->assertEquals(2, $audioCounter);
        $this->assertEquals(5, $counterNotValid);
    }
}
