<?php

namespace VkUtils;

use VkUtils\Exceptions\EmptyAttachments;
use VkUtils\Exceptions\InvalidLink;
use VkUtils\Exceptions\UnexpectedError;

class AudioParser
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return parsed array of Audio objects
     * @param string $link Link for post
     * @return array Parsed Audio objects
     * @throws EmptyAttachments
     * @throws InvalidLink
     * @throws UnexpectedError
     */
    public function parse($link)
    {
        if (empty($link)) {
            throw new InvalidLink($link);
        }

        $files = [];

        $result = $this->getRequest()->getJson($link);

        if (isset($result['error'])) {
            throw new UnexpectedError($this->getRequest()->encodeJson($result['error']));
        }

        if (!isset($result['response']) || !count($result['response'])) {
            throw new EmptyAttachments($link);
        }

        $postIterator = new PostFilterIterator($result['response']);
        foreach ($postIterator as $post) {
            $audioIterator = new AudioFilterIterator($post['attachments']);
            foreach ($audioIterator as $attachment) {
                $audio = new Audio();
                $audio->setArtist($this->getRequest()->sanitizeParam($attachment['audio']['artist']));
                $audio->setTitle($this->getRequest()->sanitizeParam($attachment['audio']['title']));
                $audio->setLink($this->getRequest()->sanitizeParam($attachment['audio']['url']));
                $audio->setDuration($this->getRequest()->sanitizeParam($attachment['audio']['duration']));
                $files[] = $audio;
            }
        }

        return $files;
    }
}
