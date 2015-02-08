<?php

namespace VkUtils\tests;

use VkUtils\Audio;
use VkUtils\AudioParser;

class AudioParserTest extends TestCase
{
    public function testParseEmptyLink()
    {
        $link = '';
        $this->setExpectedException('VkUtils\Exceptions\InvalidLink', $link);
        $parser = new AudioParser();
        $parser->parse($link);
    }

    public function testParseErrorLink()
    {
        $link = 'error-link';
        $this->setExpectedException('\RuntimeException', $link);
        $parser = new AudioParser();
        $parser->parse($link);
    }

    public function testParseEmptyAttachments()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=1139277_3645&callback=?';
        $this->setExpectedException('VkUtils\Exceptions\EmptyAttachments', $link);
        $parser = new AudioParser();
        $parser->parse($link);
    }

    public function testParseEmptyAudioAttachments()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=-56339827_118&callback=?';
        $parser = new AudioParser();
        $this->assertCount(0, $parser->parse($link));
    }

    public function testSingleAttachment()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=-77831791_3075&callback=?';
        $parser = new AudioParser();
        $files = $parser->parse($link);
        $this->assertInternalType('array', $files);
        $this->assertCount(1, $files);
    }

    public function testMultipleAttachments()
    {
        $link = 'https://api.vk.com/method/wall.getById?posts=216463522_4347&callback=?';
        $parser = new AudioParser();
        $files = $parser->parse($link);
        $this->assertInternalType('array', $files);
        $this->assertCount(2, $files);
    }

    public function testSanitizeParam()
    {
        $parser = new AudioParser();
        $this->assertEquals('string', $parser->sanitizeParam('string'));
        $this->assertEquals('string', $parser->sanitizeParam('string<p>'));
        $this->assertEquals(
            'https://api.vk.com/method/wall.getById?posts=1139277_3645&callback=?',
            $parser->sanitizeParam('https://api.vk.com/method/wall.getById?posts=1139277_3645&callback=?')
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
        $parser = new AudioParser();
        $this->assertEquals(
            '[{"artist":"Demo Artist","title":"Demo audio","link":"https:\/\/vk.com\/link\/to\/audio","fullTitle":"Demo Artist - Demo audio"},{"artist":"Demo Artist 2","title":"Demo audio 2","link":"https:\/\/vk.com\/link\/to\/audio2","fullTitle":"Demo Artist 2 - Demo audio 2"},"","string",false,null]',
            $parser->getJsonRequest($provider)
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
        $parser = new AudioParser();
        $result = $parser->getJsonRequest($provider);
        $this->assertEquals($expected, $parser->decodeJson($result));
    }
}
