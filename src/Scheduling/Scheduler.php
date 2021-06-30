<?php

namespace Tower\Scheduling;

use Carbon\Carbon;

class Scheduler
{
    /**
     *  crontab parser
     *
     *                              0    1    2    3    4    5
     *                              *    *    *    *    *    *
     *                              -    -    -    -    -    -
     *                              |    |    |    |    |    |
     *                              |    |    |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *                              |    |    |    |    +----- month (1 - 12)
     *                              |    |    |    +------- day of month (1 - 31)
     *                              |    |    +--------- hour (0 - 23)
     *                              |    +----------- min (0 - 59)
     *                              +------------- sec (0-59)
     */

    private string $task;

    private string $cron;

    public function call($task): self
    {
        $this->task = $task;

        Kernel::getInstance()->tasks[$task] = null;

        return $this;
    }

    private function setCron(string $cron): void
    {
        $this->cron = $cron;
    }

    public function cron(string $cron = '* * * * * *'): void
    {
        $this->setCron($cron);

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyMinutes(): void
    {
        $this->setCron('0 * * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTwoMinutes(): void
    {
        $this->setCron('0 */2 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTreeMinutes(): void
    {
        $this->setCron('0 */3 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyFourMinutes(): void
    {
        $this->setCron('0 */4 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyFiveMinutes(): void
    {
        $this->setCron('0 */5 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTenMinutes(): void
    {
        $this->setCron('0 */10 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyFifteenMinutes(): void
    {
        $this->setCron('0 */15 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTwentyMinutes(): void
    {
        $this->setCron('0 */20 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyThirtyMinutes(): void
    {
        $this->setCron('0 */30 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function hourly(): void
    {
        $this->setCron('0 0 * * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function hourlyAt(int $minute): void
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute * * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTwoHours(): void
    {
        $this->setCron('0 0 */2 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTwoHoursAt(int $minute): void
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute */2 * * *");
        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTreeHours(): void
    {
        $this->setCron('0 0 */3 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTreeHoursAt(int $minute): void
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute */3 * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyFourHours(): void
    {
        $this->setCron('0 0 */4 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyFourHoursAt(int $minute): void
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute */4 * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everySixHours(): void
    {
        $this->setCron('0 0 */6 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everySixHoursAt(int $minute): void
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute */6 * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyEightHours(): void
    {
        $this->setCron('0 0 */8 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyEightHoursAt(int $minute)
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute */8 * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTwelveHours(): void
    {
        $this->setCron('0 0 */12 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function everyTwelveHoursAt(int $minute): void
    {
        if ($minute < 0)
            $minute = 0;

        if ($minute > 59)
            $minute = 59;

        $this->setCron("0 $minute */12 * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function daily(): void
    {
        $this->setCron('0 0 0 * * *');

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function dailyAt(string $time): void
    {
        $time = explode(':' , $time);
        if (count($time) == 2){
            if ($time[0] < 0) $time[0] = 0;

            if ($time[0] > 23) $time[0] = 23;

            if ($time[1] < 0) $time[1] = 0;

            if ($time[1] > 59) $time[1] = 59;

            $this->setCron("0 $time[1] $time[0] * * *");

            Kernel::getInstance()->tasks[$this->task] = $this->cron;
            return;
        }

        $this->setCron("0 0 0 * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function twiceDaily(int $firstTime , int $secondTime): void
    {
        if ($firstTime < 0) $firstTime = 0;

        if ($firstTime > 23) $firstTime = 23;

        if ($secondTime < 0) $secondTime = 0;

        if ($secondTime > 23) $secondTime = 23;

        $this->setCron("0 0 $firstTime,$secondTime * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function tripleDaily(int $firstTime , int $secondTime , int $thirdTime): void
    {
        if ($firstTime < 0) $firstTime = 0;

        if ($firstTime > 23) $firstTime = 23;

        if ($secondTime < 0) $secondTime = 0;

        if ($secondTime > 23) $secondTime = 23;

        if ($thirdTime < 0) $thirdTime = 0;

        if ($thirdTime > 23) $thirdTime = 23;

        $this->setCron("0 0 $firstTime,$secondTime,$thirdTime * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function quadrupleDaily(int $firstTime , int $secondTime , int $thirdTime , int $fourTime): void
    {
        if ($firstTime < 0) $firstTime = 0;

        if ($firstTime > 23) $firstTime = 23;

        if ($secondTime < 0) $secondTime = 0;

        if ($secondTime > 23) $secondTime = 23;

        if ($thirdTime < 0) $thirdTime = 0;

        if ($thirdTime > 23) $thirdTime = 23;

        if ($fourTime < 0) $fourTime = 0;

        if ($fourTime > 23) $fourTime = 23;

        $this->setCron("0 0 $firstTime,$secondTime,$thirdTime,$fourTime * * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function weekly(): void
    {
        $this->setCron("0 0 0 * * 0");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function weeklyOn(int $day , int $hour = null): void
    {
        if ($day < 0)
            $day = 0;
        if ($day > 6)
            $day = 6;

        if (! is_null($hour)){
            if ($hour < 0)
                $hour = 0;
            if ($hour > 23)
                $hour = 23;
        }

        if (is_null($hour))
            $this->setCron("0 0 0 * * $day");
        else
            $this->setCron("0 0 $hour * * $day");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function monthly(): void
    {
        $this->setCron("0 0 0 1 * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function monthlyOn(int $day , int $hour = null): void
    {
        if ($day < 1)
            $day = 1;

        if ($day > 31)
            $day = 31;

        if (! is_null($hour)){
            if ($hour < 0)
                $hour = 0;
            if ($hour > 23)
                $hour = 23;
        }

        if (is_null($hour))
            $this->setCron("0 0 0 $day * *");
        else
            $this->setCron("0 0 $hour $day * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function twiceMonthly(int $firstDay , int $secondDay , int $hour = null): void
    {
        if ($firstDay < 1)
            $firstDay = 1;

        if ($firstDay > 31)
            $firstDay = 31;

        if ($secondDay < 1)
            $secondDay = 1;

        if ($secondDay > 31)
            $secondDay = 31;

        if (! is_null($hour)){
            if ($hour < 0)
                $hour = 0;
            if ($hour > 23)
                $hour = 23;
        }

        if (is_null($hour))
            $this->setCron("0 0 0 $firstDay,$secondDay * *");
        else
            $this->setCron("0 0 $hour $firstDay,$secondDay * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function lastDayOfMonth()
    {
        $lastDay = Carbon::now()->endOfMonth()->day;
        $this->setCron("0 0 0 $lastDay * *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function quarterly()
    {
        $this->setCron("0 0 0 1 */3 *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function lastDayOfMonthQuarterly()
    {
        $lastDay = Carbon::now()->endOfMonth()->day;

        $this->setCron("0 0 0 $lastDay */3 *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function yearly()
    {
        $this->setCron("0 0 0 1 1 *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }

    public function yearlyOn(int $month, int $dayOfMonth, int $hour)
    {
        if($month < 1)
            $month = 1;

        if ($month > 12)
            $month = 12;

        if($dayOfMonth < 1)
            $dayOfMonth = 1;

        if ($dayOfMonth > 31)
            $dayOfMonth = 31;

        if($hour < 0)
            $hour = 0;

        if ($hour > 23)
            $hour = 23;

        $this->setCron("0 0 $hour $dayOfMonth $month *");

        Kernel::getInstance()->tasks[$this->task] = $this->cron;
    }
}