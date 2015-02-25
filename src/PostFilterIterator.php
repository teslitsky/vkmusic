<?php

namespace VkUtils;

/**
 * Filter for only valid posts
 */
class PostFilterIterator extends \FilterIterator
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
        if (is_array($current) && isset($current['attachments']) && is_array($current['attachments'])) {
            return true;
        }

        return false;
    }
}
