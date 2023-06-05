<?php

namespace Automattic\WooCommerce\Vendor\League\Container\Exception;

use Automattic\WooCommerce\Vendor\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
