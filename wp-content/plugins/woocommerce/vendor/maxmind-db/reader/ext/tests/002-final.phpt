--TEST--
Check that Reader class is not final
--SKIPIF--
<?php if (!extension_loaded('maxminddb')) {
    echo 'skip';
} ?>
--FILE--
<?php
$reflectionClass = new \ReflectionClass('MaxMind\Db\Reader');
var_dump($reflectionClass->isFinal());
?>
--EXPECT--
bool(false)
