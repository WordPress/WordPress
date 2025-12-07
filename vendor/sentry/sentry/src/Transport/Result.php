<?php

declare(strict_types=1);

namespace Sentry\Transport;

use Sentry\Event;

/**
 * This class contains the details of the sending operation of an event, e.g.
 * if it was sent successfully or if it was skipped because of some reason.
 */
class Result
{
    /**
     * @var ResultStatus The status of the sending operation of the event
     */
    private $status;

    /**
     * @var Event|null The instance of the event being sent, or null if it
     *                 was not available yet
     */
    private $event;

    public function __construct(ResultStatus $status, ?Event $event = null)
    {
        $this->status = $status;
        $this->event = $event;
    }

    /**
     * Gets the status of the sending operation of the event.
     */
    public function getStatus(): ResultStatus
    {
        return $this->status;
    }

    /**
     * Gets the instance of the event being sent, or null if it was not available yet.
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }
}
