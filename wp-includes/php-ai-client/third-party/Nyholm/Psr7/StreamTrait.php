<?php

declare (strict_types=1);
namespace WordPress\AiClientDependencies\Nyholm\Psr7;

use WordPress\AiClientDependencies\Psr\Http\Message\StreamInterface;
use WordPress\AiClientDependencies\Symfony\Component\Debug\ErrorHandler as SymfonyLegacyErrorHandler;
use WordPress\AiClientDependencies\Symfony\Component\ErrorHandler\ErrorHandler as SymfonyErrorHandler;
if (\PHP_VERSION_ID >= 70400 || (new \ReflectionMethod(StreamInterface::class, '__toString'))->hasReturnType()) {
    /**
     * @internal
     */
    trait StreamTrait
    {
        public function __toString(): string
        {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
            return $this->getContents();
        }
    }
} else {
    /**
     * @internal
     */
    trait StreamTrait
    {
        /**
         * @return string
         */
        public function __toString()
        {
            try {
                if ($this->isSeekable()) {
                    $this->seek(0);
                }
                return $this->getContents();
            } catch (\Throwable $e) {
                if (\is_array($errorHandler = \set_error_handler('var_dump'))) {
                    $errorHandler = $errorHandler[0] ?? null;
                }
                \restore_error_handler();
                if ($e instanceof \Error || $errorHandler instanceof SymfonyErrorHandler || $errorHandler instanceof SymfonyLegacyErrorHandler) {
                    return \trigger_error((string) $e, \E_USER_ERROR);
                }
                return '';
            }
        }
    }
}
