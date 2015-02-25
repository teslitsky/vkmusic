<?php

namespace VkUtils;

/**
 * Filter for only 'audio' type attachments
 */
class AudioFilterIterator extends \FilterIterator
{
    public function __construct(array $data)
    {
        $iterator = new \ArrayIterator($data);
        parent::__construct($iterator);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        $current = $this->getInnerIterator()->current();
        if (!isset($current['type'])) {
            return false;
        }

        if ($current['type'] === 'audio') {
            return true;
        }

        return false;
    }
}
