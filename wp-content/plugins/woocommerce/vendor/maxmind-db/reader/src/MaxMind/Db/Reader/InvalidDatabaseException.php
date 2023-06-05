<?php

declare(strict_types=1);

namespace MaxMind\Db\Reader;

use Exception;

/**
 * This class should be thrown when unexpected data is found in the database.
 */
class InvalidDatabaseException extends Exception
{
}
