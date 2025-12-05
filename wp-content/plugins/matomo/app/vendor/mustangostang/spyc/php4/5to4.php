<?php

namespace {
    \php5to4("../spyc.php", 'spyc-latest.php4');
    function php5to4($src, $dest)
    {
        $code = \file_get_contents($src);
        $code = \preg_replace('#(public|private|protected)\\s+\\$#i', 'var \\$', $code);
        $code = \preg_replace('#(public|private|protected)\\s+static\\s+\\$#i', 'var \\$', $code);
        $code = \preg_replace('#(public|private|protected)\\s+function#i', 'function', $code);
        $code = \preg_replace('#(public|private|protected)\\s+static\\s+function#i', 'function', $code);
        $code = \preg_replace('#throw new Exception\\(([^)]*)\\)#i', 'trigger_error($1,E_USER_ERROR)', $code);
        $code = \str_replace('self::', '$this->', $code);
        $f = \fopen($dest, 'w');
        \fwrite($f, $code);
        \fclose($f);
        print "Written to {$dest}.\n";
    }
}
