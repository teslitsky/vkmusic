<?php

namespace VkUtils;

/**
 * Stub audio class
 */
class Audio
{
    /**
     * @var string Artist name
     */
    protected $artist;

    /**
     * @var string Audio title
     */
    protected $title;

    /**
     * @var string Audio link
     */
    protected $link;

    /**
     * @var int Audio duration in seconds
     */
    protected $duration = 0;

    /**
     * @return string Artist name
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param string $artist Artist name
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return string Audio title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title Audio title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string Audio link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link Audio link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return int Audio duration in seconds
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $duration Audio duration in seconds
     */
    public function setDuration($duration)
    {
        $this->duration = (int)$duration;
    }

    /**
     * @return string Full audio title
     */
    public function getFullTitle()
    {
        return $this->artist . ' - ' . $this->title;
    }

    /**
     * @return string Formatted audio duration like 01:23
     */
    public function getFormattedDuration()
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration - ($minutes * 60);

        return sprintf("%02d:%02d", $minutes, $seconds);
    }
}
