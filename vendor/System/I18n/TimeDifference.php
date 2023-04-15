<?php
/**
 * Source from CodeIgniter 4
 */
namespace DomacinskiBurek\System\I18n;

use DateTime;
use IntlCalendar;


class TimeDifference
{
    const DAY    = 86400;
    const HOUR   = 3600;
    const MINUTE = 60;
    const MONTH  = 2592000;
    const WEEK   = 604800;
    const YEAR   = 31536000;

    protected IntlCalendar $currentTime;


    protected float $testTime;


    protected int|float $eras = 0;

    protected int|float $years = 0;

    protected int|float $months = 0;

    protected int $weeks = 0;

    protected int $days = 0;

    protected int $hours = 0;

    protected int $minutes = 0;

    protected int $seconds = 0;

    protected int $difference;

    public function __construct(DateTime $currentTime, DateTime $testTime)
    {
        $this->difference = $currentTime->getTimestamp() - $testTime->getTimestamp();

        $current = IntlCalendar::fromDateTime($currentTime);
        $time    = IntlCalendar::fromDateTime($testTime)->getTime();

        $this->currentTime = $current;
        $this->testTime    = $time;
    }

    public function getYears(bool $raw = false): float|int
    {
        if ($raw) {
            return $this->difference / self::YEAR;
        }

        $time = clone $this->currentTime;

        return $time->fieldDifference($this->testTime, IntlCalendar::FIELD_YEAR);
    }

    public function getMonths(bool $raw = false): float|int
    {
        if ($raw) {
            return $this->difference / self::MONTH;
        }

        $time = clone $this->currentTime;

        return $time->fieldDifference($this->testTime, IntlCalendar::FIELD_MONTH);
    }

    public function getWeeks(bool $raw = false): float|int
    {
        if ($raw) {
            return $this->difference / self::WEEK;
        }

        $time = clone $this->currentTime;

        return (int) ($time->fieldDifference($this->testTime, IntlCalendar::FIELD_DAY_OF_YEAR) / 7);
    }

    public function getDays(bool $raw = false): float|int
    {
        if ($raw) {
            return $this->difference / self::DAY;
        }

        $time = clone $this->currentTime;

        return $time->fieldDifference($this->testTime, IntlCalendar::FIELD_DAY_OF_YEAR);
    }

    public function getHours(bool $raw = false): float|int
    {
        if ($raw) {
            return $this->difference / self::HOUR;
        }

        $time = clone $this->currentTime;

        return $time->fieldDifference($this->testTime, IntlCalendar::FIELD_HOUR_OF_DAY);
    }

    public function getMinutes(bool $raw = false): float|int
    {
        if ($raw) {
            return $this->difference / self::MINUTE;
        }

        $time = clone $this->currentTime;

        return $time->fieldDifference($this->testTime, IntlCalendar::FIELD_MINUTE);
    }

    public function getSeconds(bool $raw = false): int
    {
        if ($raw) {
            return $this->difference;
        }

        $time = clone $this->currentTime;

        return $time->fieldDifference($this->testTime, IntlCalendar::FIELD_SECOND);
    }

    public function humanize(?string $locale = null): string
    {
        $current = clone $this->currentTime;

        $years   = $current->fieldDifference($this->testTime, IntlCalendar::FIELD_YEAR);
        $months  = $current->fieldDifference($this->testTime, IntlCalendar::FIELD_MONTH);
        $days    = $current->fieldDifference($this->testTime, IntlCalendar::FIELD_DAY_OF_YEAR);
        $hours   = $current->fieldDifference($this->testTime, IntlCalendar::FIELD_HOUR_OF_DAY);
        $minutes = $current->fieldDifference($this->testTime, IntlCalendar::FIELD_MINUTE);

        $phrase = null;

        if ($years !== 0) {
            $phrase = lang('Time.years', [abs($years)], $locale);
            $before = $years < 0;
        } elseif ($months !== 0) {
            $phrase = lang('Time.months', [abs($months)], $locale);
            $before = $months < 0;
        } elseif ($days !== 0 && (abs($days) >= 7)) {
            $weeks  = ceil($days / 7);
            $phrase = lang('Time.weeks', [abs($weeks)], $locale);
            $before = $days < 0;
        } elseif ($days !== 0) {
            $phrase = lang('Time.days', [abs($days)], $locale);
            $before = $days < 0;
        } elseif ($hours !== 0) {
            $phrase = lang('Time.hours', [abs($hours)], $locale);
            $before = $hours < 0;
        } elseif ($minutes !== 0) {
            $phrase = lang('Time.minutes', [abs($minutes)], $locale);
            $before = $minutes < 0;
        } else {
            return lang('Time.now', [], $locale);
        }

        return $before
            ? lang('Time.ago', [$phrase], $locale)
            : lang('Time.inFuture', [$phrase], $locale);
    }

    public function __get(string $name)
    {
        $name   = ucfirst(strtolower($name));
        $method = "get$name";

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return null;
    }

    public function __isset(string $name)
    {
        $name   = ucfirst(strtolower($name));
        $method = "get$name";

        return method_exists($this, $method);
    }
}