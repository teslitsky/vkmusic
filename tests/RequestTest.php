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
        $audio2->setDuration(83);

        return [[[$audio, $audio2, '', 'string', false, null]]];
    }

    /**
     * @dataProvider arrayParamProvider
     */
    public function testEncodeJson($provider)
    {
        $request = new Request();
        $this->assertEquals(
            '[{"artist":"Demo Artist","title":"Demo audio","link":"https:\/\/vk.com\/link\/to\/audio","duration":0,"fullTitle":"Demo Artist - Demo audio","formattedDuration":"00:00"},{"artist":"Demo Artist 2","title":"Demo audio 2","link":"https:\/\/vk.com\/link\/to\/audio2","duration":83,"fullTitle":"Demo Artist 2 - Demo audio 2","formattedDuration":"01:23"},"","string",false,null]',
            $request->encodeJson($provider)
        );
    }

    /**
     * @dataProvider arrayParamProvider
     */
    public function testDecodeJson($provider)
    {
        $expected = [
            0 => [
                'artist'            => 'Demo Artist',
                'title'             => 'Demo audio',
                'link'              => 'https://vk.com/link/to/audio',
                'fullTitle'         => 'Demo Artist - Demo audio',
                'duration'          => 0,
                'formattedDuration' => '00:00',
            ],
            1 => [
                'artist'            => 'Demo Artist 2',
                'title'             => 'Demo audio 2',
                'link'              => 'https://vk.com/link/to/audio2',
                'fullTitle'         => 'Demo Artist 2 - Demo audio 2',
                'duration'          => 83,
                'formattedDuration' => '01:23',
            ],
            2 => '',
            3 => 'string',
            4 => '',
            5 => '',
        ];
        $request = new Request();
        $result = $request->encodeJson($provider);
        $this->assertEquals($expected, $request->decodeJson($result));
    }

    public function testGetRequest()
    {
        $request = new Request();
        $result = $request->get('http://jsonplaceholder.typicode.com/posts/1');
        $this->assertInstanceOf('\GuzzleHttp\Message\Response', $result);
    }

    public function testGetJsonRequest()
    {
        $request = new Request();
        $result = $request->getJson('http://jsonplaceholder.typicode.com/posts/1');
        $this->assertTrue(is_array($result) && count($result));
    }
}
