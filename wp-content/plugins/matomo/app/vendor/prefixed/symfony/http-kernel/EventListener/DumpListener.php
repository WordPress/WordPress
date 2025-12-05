<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Matomo\Dependencies\Symfony\Component\HttpKernel\EventListener;

use Matomo\Dependencies\Symfony\Component\Console\ConsoleEvents;
use Matomo\Dependencies\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Matomo\Dependencies\Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Matomo\Dependencies\Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use Matomo\Dependencies\Symfony\Component\VarDumper\Server\Connection;
use Matomo\Dependencies\Symfony\Component\VarDumper\VarDumper;
/**
 * Configures dump() handler.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DumpListener implements EventSubscriberInterface
{
    private $cloner;
    private $dumper;
    private $connection;
    public function __construct(ClonerInterface $cloner, DataDumperInterface $dumper, ?Connection $connection = null)
    {
        $this->cloner = $cloner;
        $this->dumper = $dumper;
        $this->connection = $connection;
    }
    public function configure()
    {
        $cloner = $this->cloner;
        $dumper = $this->dumper;
        $connection = $this->connection;
        VarDumper::setHandler(static function ($var) use($cloner, $dumper, $connection) {
            $data = $cloner->cloneVar($var);
            if (!$connection || !$connection->write($data)) {
                $dumper->dump($data);
            }
        });
    }
    public static function getSubscribedEvents()
    {
        if (!class_exists(ConsoleEvents::class)) {
            return [];
        }
        // Register early to have a working dump() as early as possible
        return [ConsoleEvents::COMMAND => ['configure', 1024]];
    }
}
