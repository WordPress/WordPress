--TEST--
openbase_dir is followed
--INI--
open_basedir=/--dne--
--FILE--
<?php
use MaxMind\Db\Reader;

$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');
?>
--EXPECTREGEX--
.*open_basedir restriction in effect.*
