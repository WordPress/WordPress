<?php

declare(strict_types=1);

namespace Sentry;

/**
 * This class represents the Exception Interface and contains the details of an
 * exception or error that occurred in the program.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class ExceptionDataBag
{
    /**
     * @var string The type of exception, e.g. RuntimeException
     */
    private $type;

    /**
     * @var string The value of the exception
     */
    private $value;

    /**
     * @var Stacktrace|null An optional stack trace object corresponding to the Stack Trace Interface
     */
    private $stacktrace;

    /**
     * @var ExceptionMechanism|null An optional object describing the mechanism that created this exception
     */
    private $mechanism;

    public function __construct(\Throwable $exception, ?Stacktrace $stacktrace = null, ?ExceptionMechanism $mechanism = null)
    {
        $this->type = \get_class($exception);
        $this->value = $exception->getMessage();
        $this->stacktrace = $stacktrace;
        $this->mechanism = $mechanism;
    }

    /**
     * Gets the type of exception, e.g. RuntimeException.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the type of the exception.
     *
     * @param string $type The exception type
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of the exception.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Sets the value of the exception.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Gets the stack trace object corresponding to the Stack Trace Interface.
     */
    public function getStacktrace(): ?Stacktrace
    {
        return $this->stacktrace;
    }

    /**
     * Sets the stack trace object corresponding to the Stack Trace Interface.
     *
     * @param Stacktrace $stacktrace The stacktrace
     */
    public function setStacktrace(Stacktrace $stacktrace): self
    {
        $this->stacktrace = $stacktrace;

        return $this;
    }

    /**
     * Gets the object describing the mechanism that created this exception.
     */
    public function getMechanism(): ?ExceptionMechanism
    {
        return $this->mechanism;
    }

    /**
     * Sets the object describing the mechanism that created this exception.
     *
     * @param ExceptionMechanism|null $mechanism The mechanism that created this exception
     */
    public function setMechanism(?ExceptionMechanism $mechanism): self
    {
        $this->mechanism = $mechanism;

        return $this;
    }
}
