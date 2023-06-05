<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container;

use Automattic\WooCommerce\Vendor\Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param ContainerInterface $container
     *
     * @return self
     */
    public function setContainer(ContainerInterface $container) : ContainerAwareInterface;

    /**
     * Get the container
     *
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface;

    /**
     * Set a container. This will be removed in favour of setContainer receiving Container in next major release.
     *
     * @param Container $container
     *
     * @return self
     */
    public function setLeagueContainer(Container $container) : self;

    /**
     * Get the container. This will be removed in favour of getContainer returning Container in next major release.
     *
     * @return Container
     */
    public function getLeagueContainer() : Container;
}
