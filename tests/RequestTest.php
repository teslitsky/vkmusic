<?php

namespace VkUtils\tests;

use VkUtils\Audio;
use VkUtils\Request;

class RequestTest extends TestCase
{
    public function testSanitizeParam()
    {
        $request = new Request();
        $this->assertEquals('string', $request->sanitizeParam('string'));
        $this->assertEquals('string', $request->sanitizeParam('string<p>'));
        $this->assertEquals(
            'https://api.vk.com/method/wall.getById?posts=1139277_3645&callback=?',
            $request->sanitizeParam('https://api.vk.com/method/wall.getById?posts=1139277_3645&callback=?')
        );
    }

    public function arrayParamProvider()
    {
        $audio = new Audio();
        $audio->setLink('https://vk.com/link/to/audio');
        $audio->setTitle('Demo audio');
        $audio->setArtist('Demo Artist');

        $audio2 = new Audio();
        $audio2->setLink('https://vk.com/link/to/audio2');
        $audio2->setTitle('Demo audio 2');
        $audio2->setArtist('Demo Artist 2');

        return [[[$audio, $audio2, '', 'string', false, null]]];
    }

    /**
     * @dataProvider arrayParamProvider
     */
    public function testGetJsonRequest($provider)
    {
        $request = new Request();
        $this->assertEquals(
            '[{"artist":"Demo Artist","title":"Demo audio","link":"https:\/\/vk.com\/link\/to\/audio","fullTitle":"Demo Artist - Demo audio"},{"artist":"Demo Artist 2","title":"Demo audio 2","link":"https:\/\/vk.com\/link\/to\/audio2","fullTitle":"Demo Artist 2 - Demo audio 2"},"","string",false,null]',
            $request->getJsonRequest($provider)
        );
    }

    /**
     * @dataProvider arrayParamProvider
     */
    public function testDecodeJson($provider)
    {
        $expected = [
            0 => [
                'artist'    => 'Demo Artist',
                'title'     => 'Demo audio',
                'link'      => 'https://vk.com/link/to/audio',
                'fullTitle' => 'Demo Artist - Demo audio',
            ],
            1 => [
                'artist'    => 'Demo Artist 2',
                'title'     => 'Demo audio 2',
                'link'      => 'https://vk.com/link/to/audio2',
                'fullTitle' => 'Demo Artist 2 - Demo audio 2',
            ],
            2 => '',
            3 => 'string',
            4 => '',
            5 => '',
        ];
        $request = new Request();
        $result = $request->getJsonRequest($provider);
        $this->assertEquals($expected, $request->decodeJson($result));
    }
}
