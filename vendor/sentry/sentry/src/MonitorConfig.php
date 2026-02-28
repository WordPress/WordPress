<?php

declare(strict_types=1);

namespace Sentry;

final class MonitorConfig
{
    /**
     * @var MonitorSchedule The schedule of the monitor
     */
    private $schedule;

    /**
     * @var int|null The check-in margin in minutes
     */
    private $checkinMargin;

    /**
     * @var int|null The maximum runtime in minutes
     */
    private $maxRuntime;

    /**
     * @var string|null The timezone
     */
    private $timezone;

    /**
     * @var int|null The number of consecutive failed check-ins it takes before an issue is created
     */
    private $failureIssueThreshold;

    /**
     * @var int|null The number of consecutive OK check-ins it takes before an issue is resolved
     */
    private $recoveryThreshold;

    public function __construct(
        MonitorSchedule $schedule,
        ?int $checkinMargin = null,
        ?int $maxRuntime = null,
        ?string $timezone = null,
        ?int $failureIssueThreshold = null,
        ?int $recoveryThreshold = null
    ) {
        $this->schedule = $schedule;
        $this->checkinMargin = $checkinMargin;
        $this->maxRuntime = $maxRuntime;
        $this->timezone = $timezone;
        $this->failureIssueThreshold = $failureIssueThreshold;
        $this->recoveryThreshold = $recoveryThreshold;
    }

    public function getSchedule(): MonitorSchedule
    {
        return $this->schedule;
    }

    public function setSchedule(MonitorSchedule $schedule): self
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getCheckinMargin(): ?int
    {
        return $this->checkinMargin;
    }

    public function setCheckinMargin(?int $checkinMargin): self
    {
        $this->checkinMargin = $checkinMargin;

        return $this;
    }

    public function getMaxRuntime(): ?int
    {
        return $this->maxRuntime;
    }

    public function setMaxRuntime(?int $maxRuntime): self
    {
        $this->maxRuntime = $maxRuntime;

        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getFailureRecoveryThreshold(): ?int
    {
        return $this->failureIssueThreshold;
    }

    public function setFailureRecoveryThreshold(?int $failureIssueThreshold): self
    {
        $this->failureIssueThreshold = $failureIssueThreshold;

        return $this;
    }

    public function getRecoveryThreshold(): ?int
    {
        return $this->recoveryThreshold;
    }

    public function setRecoveryThreshold(?int $recoveryThreshold): self
    {
        $this->recoveryThreshold = $recoveryThreshold;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'schedule' => $this->schedule->toArray(),
            'checkin_margin' => $this->checkinMargin,
            'max_runtime' => $this->maxRuntime,
            'timezone' => $this->timezone,
            'failure_issue_threshold' => $this->failureIssueThreshold,
            'recovery_threshold' => $this->recoveryThreshold,
        ];
    }
}
