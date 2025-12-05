<?php

declare (strict_types=1);
namespace Matomo\Dependencies\PhpDocReader\PhpParser;

use SplFileObject;
/**
 * Parses a file for "use" declarations.
 *
 * Class taken and adapted from doctrine/annotations to avoid pulling the whole package.
 *
 * Authors: Fabien Potencier <fabien@symfony.com> and Christian Kaps <christian.kaps@mohiva.com>
 */
class UseStatementParser
{
    /**
     * @return array A list with use statements in the form (Alias => FQN).
     */
    public function parseUseStatements(\ReflectionClass $class) : array
    {
        $filename = $class->getFilename();
        if ($filename === \false) {
            return [];
        }
        $content = $this->getFileContent($filename, $class->getStartLine());
        if ($content === null) {
            return [];
        }
        $namespace = preg_quote($class->getNamespaceName(), '/');
        $content = preg_replace('/^.*?(\\bnamespace\\s+' . $namespace . '\\s*[;{].*)$/s', '\\1', $content);
        $tokenizer = new TokenParser('<?php ' . $content);
        return $tokenizer->parseUseStatements($class->getNamespaceName());
    }
    /**
     * Gets the content of the file right up to the given line number.
     *
     * @param string $filename The name of the file to load.
     * @param int $lineNumber The number of lines to read from file.
     */
    private function getFileContent(string $filename, int $lineNumber) : string
    {
        if (!is_file($filename)) {
            throw new \RuntimeException("Unable to read file {$filename}");
        }
        $content = '';
        $lineCnt = 0;
        $file = new SplFileObject($filename);
        while (!$file->eof()) {
            if ($lineCnt++ === $lineNumber) {
                break;
            }
            $content .= $file->fgets();
        }
        return $content;
    }
}
