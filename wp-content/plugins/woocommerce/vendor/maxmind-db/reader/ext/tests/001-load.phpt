--TEST--
Check for maxminddb presence
--SKIPIF--
<?php if (!extension_loaded('maxminddb')) {
    echo 'skip';
} ?>
--FILE--
<?php
echo 'maxminddb extension is available';
?>
--EXPECT--
maxminddb extension is available
