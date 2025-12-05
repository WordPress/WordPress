<?php

namespace {
    final class XHProfExtensionUnitTestEngine extends \ArcanistUnitTestEngine
    {
        public function run()
        {
            $root = $this->getWorkingCopy()->getProjectRoot() . '/extension/';
            $start_time = \microtime(\true);
            \id(new \ExecFuture('phpize && ./configure && make -j4'))->setCWD($root)->resolvex();
            $out = \id(new \ExecFuture('make -f Makefile.local test_with_exit_status'))->setCWD($root)->setEnv(array('TEST_PHP_ARGS' => '-q'))->resolvex();
            // NOTE: REPORT_EXIT_STATUS doesn't seem to work properly in some versions
            // of PHP. Just "parse" stdout to approximate the results.
            list($stdout) = $out;
            $tests = array();
            foreach (\phutil_split_lines($stdout) as $line) {
                $matches = null;
                // NOTE: The test script writes the name of the test originally, then
                // uses "\r" to erase it and write the result. This splits as a single
                // line.
                if (\preg_match('/^TEST .*\\r(PASS|FAIL) (.*)/', $line, $matches)) {
                    if ($matches[1] == 'PASS') {
                        $result = \ArcanistUnitTestResult::RESULT_PASS;
                    } else {
                        $result = \ArcanistUnitTestResult::RESULT_FAIL;
                    }
                    $name = \trim($matches[2]);
                    $tests[] = \id(new \ArcanistUnitTestResult())->setName($name)->setResult($result)->setDuration(\microtime(\true) - $start_time);
                }
            }
            return $tests;
        }
    }
}
