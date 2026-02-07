<?php

declare (strict_types=1);
namespace Sentry\Monolog;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use WPSentry\ScopedVendor\Psr\Log\LogLevel;
use Sentry\Breadcrumb;
use Sentry\Event;
use Sentry\State\HubInterface;
use Sentry\State\Scope;
/**
 * This Monolog handler logs every message as a {@see Breadcrumb} into the current {@see Scope},
 * to enrich any event sent to Sentry.
 */
final class BreadcrumbHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    /**
     * @var HubInterface
     */
    private $hub;
    /**
     * @param HubInterface $hub    The hub to which errors are reported
     * @param int|string   $level  The minimum logging level at which this
     *                             handler will be triggered
     * @param bool         $bubble Whether the messages that are handled can
     *                             bubble up the stack or not
     *
     * @phpstan-param int|string|Level|LogLevel::* $level
     */
    public function __construct(\Sentry\State\HubInterface $hub, $level = \Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        $this->hub = $hub;
        parent::__construct($level, $bubble);
    }
    /**
     * @psalm-suppress MoreSpecificImplementedParamType
     *
     * @param LogRecord|array{
     *      level: int,
     *      channel: string,
     *      datetime: \DateTimeImmutable,
     *      message: string,
     *      extra?: array<string, mixed>
     * } $record {@see https://github.com/Seldaek/monolog/blob/main/doc/message-structure.md}
     */
    protected function write($record) : void
    {
        $breadcrumb = new \Sentry\Breadcrumb($this->getBreadcrumbLevel($record['level']), $this->getBreadcrumbType($record['level']), $record['channel'], $record['message'], ($record['context'] ?? []) + ($record['extra'] ?? []), $record['datetime']->getTimestamp());
        $this->hub->addBreadcrumb($breadcrumb);
    }
    /**
     * @param Level|int $level
     */
    private function getBreadcrumbLevel($level) : string
    {
        if ($level instanceof \Monolog\Level) {
            $level = $level->value;
        }
        switch ($level) {
            case \Monolog\Logger::DEBUG:
                return \Sentry\Breadcrumb::LEVEL_DEBUG;
            case \Monolog\Logger::INFO:
            case \Monolog\Logger::NOTICE:
                return \Sentry\Breadcrumb::LEVEL_INFO;
            case \Monolog\Logger::WARNING:
                return \Sentry\Breadcrumb::LEVEL_WARNING;
            case \Monolog\Logger::ERROR:
                return \Sentry\Breadcrumb::LEVEL_ERROR;
            default:
                return \Sentry\Breadcrumb::LEVEL_FATAL;
        }
    }
    private function getBreadcrumbType(int $level) : string
    {
        if ($level >= \Monolog\Logger::ERROR) {
            return \Sentry\Breadcrumb::TYPE_ERROR;
        }
        return \Sentry\Breadcrumb::TYPE_DEFAULT;
    }
}
