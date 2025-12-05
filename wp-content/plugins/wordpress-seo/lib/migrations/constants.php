<?php

namespace Yoast\WP\Lib\Migrations;

/**
 * Yoast migrations constants class.
 */
class Constants {

	public const MYSQL_MAX_IDENTIFIER_LENGTH = 64;
	public const SQL_UNKNOWN_QUERY_TYPE      = 1;
	public const SQL_SELECT                  = 2;
	public const SQL_INSERT                  = 4;
	public const SQL_UPDATE                  = 8;
	public const SQL_DELETE                  = 16;
	public const SQL_ALTER                   = 32;
	public const SQL_DROP                    = 64;
	public const SQL_CREATE                  = 128;
	public const SQL_SHOW                    = 256;
	public const SQL_RENAME                  = 512;
	public const SQL_SET                     = 1024;
}
