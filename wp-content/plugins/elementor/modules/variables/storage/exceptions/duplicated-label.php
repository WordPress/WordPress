<?php

namespace Elementor\Modules\Variables\Storage\Exceptions;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DuplicatedLabel extends Exception {}
