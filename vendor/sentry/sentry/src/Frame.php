<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This class represents a single frame of a stacktrace.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class Frame
{
    public const INTERNAL_FRAME_FILENAME = '[internal]';

    /**
     * @deprecated This constant is deprecated and will be removed in 5.x.
     */
    public const ANONYMOUS_CLASS_PREFIX = "class@anonymous\x00";

    /**
     * @var string|null The name of the function being called
     */
    private $functionName;

    /**
     * @var string|null The original function name, if the function name is
     *                  shortened or demangled
     */
    private $rawFunctionName;

    /**
     * @var string The file where the frame originated
     */
    private $file;

    /**
     * @var string|null The absolute path to the source file
     */
    private $absoluteFilePath;

    /**
     * @var int The line at which the frame originated
     */
    private $line;

    /**
     * @var string[] A list of source code lines before the one where the frame
     *               originated
     */
    private $preContext = [];

    /**
     * @var string|null The source code written at the line number of the file that
     *                  originated this frame
     */
    private $contextLine;

    /**
     * @var string[] A list of source code lines after the one where the frame
     *               originated
     */
    private $postContext = [];

    /**
     * @var bool Flag telling whether the frame is related to the execution of
     *           the relevant code in this stacktrace
     */
    private $inApp;

    /**
     * @var array<string, mixed> A mapping of variables which were available within
     *                           this frame (usually context-locals)
     */
    private $vars = [];

    /**
     * Initializes a new instance of this class using the provided information.
     *
     * @param string|null          $functionName     The name of the function being called
     * @param string               $file             The file where the frame originated
     * @param string|null          $rawFunctionName  The original function name, if the function
     *                                               name is shortened or demangled
     * @param string|null          $absoluteFilePath The absolute path to the source file
     * @param int                  $line             The line at which the frame originated
     * @param array<string, mixed> $vars             A mapping of variables which were available
     *                                               within the frame
     * @param bool                 $inApp            Whether the frame is related to the
     *                                               execution of code relevant to the
     *                                               application
     */
    public function __construct(?string $functionName, string $file, int $line, ?string $rawFunctionName = null, ?string $absoluteFilePath = null, array $vars = [], bool $inApp = true)
    {
        $this->functionName = $functionName;
        $this->file = $file;
        $this->line = $line;
        $this->rawFunctionName = $rawFunctionName;
        $this->absoluteFilePath = $absoluteFilePath;
        $this->vars = $vars;
        $this->inApp = $inApp;
    }

    /**
     * Gets the name of the function being called.
     */
    public function getFunctionName(): ?string
    {
        return $this->functionName;
    }

    /**
     * Gets the original function name, if the function name is shortened or
     * demangled.
     */
    public function getRawFunctionName(): ?string
    {
        return $this->rawFunctionName;
    }

    /**
     * Gets the file where the frame originated.
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Gets the absolute path to the source file.
     */
    public function getAbsoluteFilePath(): ?string
    {
        return $this->absoluteFilePath;
    }

    /**
     * Gets the line at which the frame originated.
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * Gets a list of source code lines before the one where the frame originated.
     *
     * @return string[]
     */
    public function getPreContext(): array
    {
        return $this->preContext;
    }

    /**
     * Sets a list of source code lines before the one where the frame originated.
     *
     * @param string[] $preContext The source code lines
     */
    public function setPreContext(array $preContext): self
    {
        $this->preContext = $preContext;

        return $this;
    }

    /**
     * Gets the source code written at the line number of the file that originated
     * this frame.
     */
    public function getContextLine(): ?string
    {
        return $this->contextLine;
    }

    /**
     * Sets the source code written at the line number of the file that originated
     * this frame.
     *
     * @param string|null $contextLine The source code line
     */
    public function setContextLine(?string $contextLine): self
    {
        $this->contextLine = $contextLine;

        return $this;
    }

    /**
     * Gets a list of source code lines after the one where the frame originated.
     *
     * @return string[]
     */
    public function getPostContext(): array
    {
        return $this->postContext;
    }

    /**
     * Sets a list of source code lines after the one where the frame originated.
     *
     * @param string[] $postContext The source code lines
     */
    public function setPostContext(array $postContext): self
    {
        $this->postContext = $postContext;

        return $this;
    }

    /**
     * Gets whether the frame is related to the execution of the relevant code
     * in this stacktrace.
     */
    public function isInApp(): bool
    {
        return $this->inApp;
    }

    /**
     * Sets whether the frame is related to the execution of the relevant code
     * in this stacktrace.
     *
     * @param bool $inApp flag indicating whether the frame is application-related
     */
    public function setIsInApp(bool $inApp): self
    {
        $this->inApp = $inApp;

        return $this;
    }

    /**
     * Gets a mapping of variables which were available within this frame
     * (usually context-locals).
     *
     * @return array<string, mixed>
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * Sets a mapping of variables which were available within this frame
     * (usually context-locals).
     *
     * @param array<string, mixed> $vars The variables
     */
    public function setVars(array $vars): self
    {
        $this->vars = $vars;

        return $this;
    }

    /**
     * Gets whether the frame is internal.
     */
    public function isInternal(): bool
    {
        return $this->file === self::INTERNAL_FRAME_FILENAME;
    }
}
