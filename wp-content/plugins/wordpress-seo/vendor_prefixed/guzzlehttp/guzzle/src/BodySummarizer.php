<?php

namespace YoastSEO_Vendor\GuzzleHttp;

use YoastSEO_Vendor\Psr\Http\Message\MessageInterface;
final class BodySummarizer implements \YoastSEO_Vendor\GuzzleHttp\BodySummarizerInterface
{
    /**
     * @var int|null
     */
    private $truncateAt;
    public function __construct(int $truncateAt = null)
    {
        $this->truncateAt = $truncateAt;
    }
    /**
     * Returns a summarized message body.
     */
    public function summarize(\YoastSEO_Vendor\Psr\Http\Message\MessageInterface $message) : ?string
    {
        return $this->truncateAt === null ? \YoastSEO_Vendor\GuzzleHttp\Psr7\Message::bodySummary($message) : \YoastSEO_Vendor\GuzzleHttp\Psr7\Message::bodySummary($message, $this->truncateAt);
    }
}
