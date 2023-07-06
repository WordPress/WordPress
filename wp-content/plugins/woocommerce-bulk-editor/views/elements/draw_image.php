<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//global $WOOBE;
if (empty($src)) {
    return '';
}
?>

<img src="<?php echo $src ?>" <?php if (!empty($class)): ?>class="<?php echo $class ?>"<?php endif; ?> <?php if (!empty($width)): ?>width="<?php echo $width ?>"<?php endif; ?> alt="<?php echo $alt ?>" />

