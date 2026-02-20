<?php

namespace WordPress\AiClientDependencies\Http\Discovery\Exception;

use WordPress\AiClientDependencies\Http\Discovery\Exception;
/**
 * Thrown when all discovery strategies fails to find a resource.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class DiscoveryFailedException extends \Exception implements Exception
{
    /**
     * @var \Exception[]
     */
    private $exceptions;
    /**
     * @param string       $message
     * @param \Exception[] $exceptions
     */
    public function __construct($message, array $exceptions = [])
    {
        $this->exceptions = $exceptions;
        parent::__construct($message);
    }
    /**
     * @param \Exception[] $exceptions
     */
    public static function create($exceptions)
    {
        $message = 'Could not find resource using any discovery strategy. Find more information at http://docs.php-http.org/en/latest/discovery.html#common-errors';
        foreach ($exceptions as $e) {
            $message .= "\n - " . $e->getMessage();
        }
        $message .= "\n\n";
        return new self($message, $exceptions);
    }
    /**
     * @return \Exception[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }
}
