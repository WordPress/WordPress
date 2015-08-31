<?php

class Minify_ClosureCompiler {
    protected static $_pathJava = 'java';
    protected static $_pathJar = 'compiler.jar';

    public static function minify($js, $options = array()) {
        $output = null;

        self::_execute($options, $js, $output);

        return $output;
    }

    public static function test(&$error) {
        try {
            Minify_ClosureCompiler::minify('alert("ok");');
            $error = 'OK';

            return true;
        } catch (Exception $exception) {
            $error = $exception->getMessage();

            return false;
        }
    }

    public static function setPathJava($pathJava) {
        self::$_pathJava = $pathJava;
    }

    public static function setPathJar($pathJar) {
        self::$_pathJar = $pathJar;
    }

    protected static function _execute($options, $input, &$output) {
        $cmd = self::_getCmd($options);
        $return = self::_run($cmd, $input, $output);

        return $return;
    }

    protected static function _getCmd($options) {
        if (!is_file(self::$_pathJava)) {
            throw new Exception(sprintf('JAVA executable (%s) is not a valid file.', self::$_pathJava));
        }

        if (!is_file(self::$_pathJar)) {
            throw new Exception(sprintf('JAR file (%s) is not a valid file.', self::$_pathJar));
        }

        $options = array_merge(array(
            'compilation_level' => '',
            'formatting' => ''
        ), $options);

        $optionsString = '';

        foreach ($options as $option => $value) {
            if ($value) {
                $optionsString .= sprintf('--%s=%s ', $option, $value);
            }
        }

        $cmd = sprintf('%s -jar %s %s', self::$_pathJava, escapeshellarg(self::$_pathJar), $optionsString);

        return $cmd;
    }

    protected static function _run($cmd, $input, &$output) {
        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );

        $pipes = null;
        $process = proc_open($cmd, $descriptors, $pipes);

        if (!$process) {
            throw new Exception(sprintf('Unable to open process (%s).', $cmd));
        }

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return = proc_close($process);

        if ($return != 0) {
            throw new Exception(sprintf('Command (%s) execution failed. Error: %s. Return code: %d.', $cmd, $error, $return));
        }

        return $return;
    }
}
