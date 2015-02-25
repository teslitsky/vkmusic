<?php

namespace VkUtils\tests;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use VkUtils\Audio;
use VkUtils\Request;
use VkUtils\AudioParser;
use VkUtils\RequestInterface;

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

    public function testUnexpectedError()
    {
        $link = 'https://api.vk.com/any/link';
        $this->setExpectedException('VkUtils\Exceptions\UnexpectedError');

        $stub = $this->getMockBuilder('VkUtils\Request')->getMock();
        $error = ['error' => 'error data'];
        $stub->expects($this->any())->method('getJson')->will($this->returnValue($error));
        $parser = new AudioParser($stub);
        $parser->parse($link);
    }

    public function testParseEmptyAttachments()
    {
        $link = 'https://api.vk.com/any/link';
        $this->setExpectedException('VkUtils\Exceptions\EmptyAttachments', $link);

        $stub = $this->getMockBuilder('VkUtils\Request')->getMock();
        $responseError = ['not-response' => 'error data'];
        $stub->expects($this->any())->method('getJson')->will($this->returnValue($responseError));
        $parser = new AudioParser($stub);
        $parser->parse($link);

        $responseEmpty = ['response' => []];
        $stub->expects($this->any())->method('getJson')->will($this->returnValue($responseEmpty));
        $parser = new AudioParser($stub);
        $parser->parse($link);
    }

    public function testParseErrorLink()
    {
        $link = 'error-link';
        $this->setExpectedException('\RuntimeException', $link);
        $parser = new AudioParser(self::$request);
        $parser->parse($link);
    }

    public function testOnePost()
    {
        $link = 'https://api.vk.com/any/link';
        $data = [
            'response' => [
                0 => [
                    'attachments' => [
                        0 => [
                            'type'  => 'audio',
                            'audio' => [
                                'artist'   => 'Demo Artist',
                                'title'    => 'Demo audio',
                                'url'      => 'https://vk.com/link/to/audio',
                                'duration' => '83',
                            ],
                        ],
                    ],
                ]
            ],
        ];

        foreach ($data['response'] as $post) {
            $audio = new Audio();
            $audio->setArtist($post['attachments'][0]['audio']['artist']);
            $audio->setTitle($post['attachments'][0]['audio']['title']);
            $audio->setLink($post['attachments'][0]['audio']['url']);
            $audio->setDuration($post['attachments'][0]['audio']['duration']);
            $files[] = $audio;
        }

        $parser = new AudioParser($this->getProphesizeRequest($data));
        $this->assertEquals($files, $parser->parse($link));
    }

    public function testFewPosts()
    {
        $link = 'https://api.vk.com/any/link';
        $data = [
            'response' => [
                0 => [
                    'attachments' => [
                        0 => [
                            'type'  => 'audio',
                            'audio' => [
                                'artist'   => 'Demo Artist',
                                'title'    => 'Demo audio',
                                'url'      => 'https://vk.com/link/to/audio',
                                'duration' => '83',
                            ],
                        ],
                    ],
                ],
                1 => [
                    'attachments' => [
                        0 => [
                            'type'  => 'audio',
                            'audio' => [
                                'artist'   => 'Demo Artist 3',
                                'title'    => 'Demo audio',
                                'url'      => 'https://vk.com/link/to/audio',
                                'duration' => '83',
                            ],
                        ],
                    ],
                ]
            ],
        ];

        foreach ($data['response'] as $post) {
            $audio = new Audio();
            $audio->setArtist($post['attachments'][0]['audio']['artist']);
            $audio->setTitle($post['attachments'][0]['audio']['title']);
            $audio->setLink($post['attachments'][0]['audio']['url']);
            $audio->setDuration($post['attachments'][0]['audio']['duration']);
            $files[] = $audio;
        }

        $parser = new AudioParser($this->getProphesizeRequest($data));
        $this->assertEquals($files, $parser->parse($link));
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

    /**
     * @param $data
     * @return RequestInterface
     */
    private function getProphesizeRequest($data)
    {
        $observer = $this->prophesize('VkUtils\Request');
        $observer->willImplement('VkUtils\RequestInterface');
        /** @var $observer RequestInterface|ObjectProphecy */
        $observer->getJson(Argument::type('string'))->willReturn($data);
        $observer->sanitizeParam(Argument::type('string'))->will(function ($args) {
            return html_entity_decode(filter_var($args[0], FILTER_SANITIZE_STRING));
        });

        return $observer->reveal();
    }
}
