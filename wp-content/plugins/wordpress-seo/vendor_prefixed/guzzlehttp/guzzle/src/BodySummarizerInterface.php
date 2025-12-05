<?php

namespace YoastSEO_Vendor\GuzzleHttp;

use YoastSEO_Vendor\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(\YoastSEO_Vendor\Psr\Http\Message\MessageInterface $message) : ?string;
}
