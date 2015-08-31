<?php

class Minify_YUICompressor {
    protected static $_pathJava = 'java';
    protected static $_pathJar = 'yuicompressor.jar';

    public static function minifyJs($js, $options = array()) {
        return self::_minify('js', $js, $options);
    }

    public static function minifyCss($css, $options = array()) {
        w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/CSS/UriRewriter.php');

        $css = self::_minify('css', $css, $options);
        $css = Minify_CSS_UriRewriter::rewrite($css, $options);

        return $css;
    }

    public static function testJs(&$error) {
        try {
            Minify_YUICompressor::minifyJs('alert("ok");');
            $error = 'OK';

            return true;
        } catch (Exception $exception) {
            $error = $exception->getMessage();

            return false;
        }
    }

    public static function testCss(&$error) {
        try {
            Minify_YUICompressor::minifyCss('p{color:red}');
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

    protected static function _minify($type, $content, $options) {
        $output = null;

        self::_execute($type, $options, $content, $output);

        return $output;
    }

    protected static function _execute($type, $options, $input, &$output) {
        $cmd = self::_getCmd($type, $options);
        $return = self::_run($cmd, $input, $output);

        return $return;
    }

    protected static function _getCmd($type, $options) {
        if (!is_file(self::$_pathJava)) {
            throw new Exception(sprintf('JAVA executable (%s) is not a valid file.', self::$_pathJava));
        }

        if (!is_file(self::$_pathJar)) {
            throw new Exception(sprintf('JAR file (%s) is not a valid file.', self::$_pathJar));
        }

        $options = array_merge(array(
            'line-break' => 5000,
            'type' => $type,
            'nomunge' => false,
            'preserve-semi' => false,
            'disable-optimizations' => false
        ), $options);

        $optionsString = '';

        foreach ($options as $option => $value) {
            switch ($option) {
                case 'charset':
                case 'line-break':
                case 'type':
                    if ($value) {
                        $optionsString .= sprintf('--%s %s ', $option, $value);
                    }
                    break;

                case 'nomunge':
                case 'preserve-semi':
                case 'disable-optimizations':
                    if ($value) {
                        $optionsString .= sprintf('--%s ', $option);
                    }
                    break;
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
