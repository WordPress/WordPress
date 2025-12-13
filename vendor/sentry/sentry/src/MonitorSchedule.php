<?php

declare(strict_types=1);

namespace Sentry;

final class MonitorSchedule
{
    /**
     * @var string The type of the schedule
     */
    private $type;

    /**
     * @var string|int The value of the schedule
     */
    private $value;

    /**
     * @var MonitorScheduleUnit|null The unit of the schedule
     */
    private $unit;

    public const TYPE_CRONTAB = 'crontab';

    public const TYPE_INTERVAL = 'interval';

    /**
     * @param string                   $type  The type of the schedule
     * @param string|int               $value The value of the schedule
     * @param MonitorScheduleUnit|null $unit  The unit of the schedule
     */
    public function __construct(
        string $type,
        $value,
        ?MonitorScheduleUnit $unit = null
    ) {
        $this->type = $type;
        $this->value = $value;
        $this->unit = $unit;
    }

    public static function crontab(string $value): self
    {
        return new self(self::TYPE_CRONTAB, $value);
    }

    public static function interval(int $value, MonitorScheduleUnit $unit): self
    {
        return new self(self::TYPE_INTERVAL, $value, $unit);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|int $value
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUnit(): ?MonitorScheduleUnit
    {
        return $this->unit;
    }

    public function setUnit(?MonitorScheduleUnit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'unit' => (string) $this->unit,
        ];
    }
}
