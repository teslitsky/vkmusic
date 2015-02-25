<?php

namespace VkUtils\tests;

use VkUtils\PostFilterIterator;

class PostFilterIteratorTest extends TestCase
{
    public function testIterator()
    {
        $posts = [
            0,
            1,
            2 => [],
            3 => [
                'attachments' => null,
            ],
            4 => [
                'attachments' => [],
            ],
            5 => [
                'attachments' => [
                    0 => 0,
                    1 => 1,
                ],
            ],
        ];

        $postCounter = 0;
        $iterator = new PostFilterIterator($posts);
        foreach ($iterator as $key => $post) {
            $postCounter++;
        }

        $counterNotValid = count($posts) - $postCounter;

        $this->assertEquals(2, $postCounter);
        $this->assertEquals(4, $counterNotValid);
    }
}
