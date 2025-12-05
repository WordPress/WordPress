<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Matomo\Dependencies\Symfony\Bridge\Monolog\Processor;

use Matomo\Dependencies\Symfony\Component\Console\ConsoleEvents;
use Matomo\Dependencies\Symfony\Component\Console\Event\ConsoleEvent;
use Matomo\Dependencies\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Matomo\Dependencies\Symfony\Contracts\Service\ResetInterface;
/**
 * Adds the current console command information to the log entry.
 *
 * @author Piotr Stankowski <git@trakos.pl>
 */
class ConsoleCommandProcessor implements EventSubscriberInterface, ResetInterface
{
    private $commandData;
    private $includeArguments;
    private $includeOptions;
    public function __construct(bool $includeArguments = \true, bool $includeOptions = \false)
    {
        $this->includeArguments = $includeArguments;
        $this->includeOptions = $includeOptions;
    }
    public function __invoke(array $records)
    {
        if (null !== $this->commandData && !isset($records['extra']['command'])) {
            $records['extra']['command'] = $this->commandData;
        }
        return $records;
    }
    public function reset()
    {
        $this->commandData = null;
    }
    public function addCommandData(ConsoleEvent $event)
    {
        $this->commandData = ['name' => $event->getCommand()->getName()];
        if ($this->includeArguments) {
            $this->commandData['arguments'] = $event->getInput()->getArguments();
        }
        if ($this->includeOptions) {
            $this->commandData['options'] = $event->getInput()->getOptions();
        }
    }
    public static function getSubscribedEvents()
    {
        return [ConsoleEvents::COMMAND => ['addCommandData', 1]];
    }
}
