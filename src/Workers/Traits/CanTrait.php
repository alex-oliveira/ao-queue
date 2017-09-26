<?php

namespace AoQueue\Workers\Traits;

trait CanTrait
{

    /**
     * Return "true" if the worker can work at informed date.
     *
     * @param \DateTime $date
     * @return bool
     */
    protected function canWorkDateTime(\DateTime $date)
    {
        $day = $date->format('N');
        $hour = $date->format('H');

        if (in_array($day, $this->workDays()) && $hour >= $this->wakeUpHour() && $hour < $this->sleepHour())
            return true;

        return false;
    }

    /**
     * If now isn't the work's hour, this method make the worker sleep until it's the work's hour.
     */
    protected function canWork()
    {
        $this->log('Requiring authorizations to work...');

        do {
            $sleep = 0;

            $now = new \DateTime();

            $this->log('Checking authorization...');

            if ($this->canWorkDateTime($now)) {
                $this->log();
                $this->log('Confirmed! I can work! Let\'s go!!!');

            } else {
                $next = new \DateTime($now->format('Y-m-d'));
                $next->add(new \DateInterval('PT' . $this->wakeUpHour() . 'H'));

                if (($next->getTimestamp() - $now->getTimestamp()) < 0)
                    $next->add(new \DateInterval('P1D'));

                while (!$this->canWorkDateTime($next)) {
                    $next->add(new \DateInterval('P1D'));
                }

                $sleep = $next->getTimestamp() - $now->getTimestamp();

                $this->log('I only can work at ' . $next->format('H:i:s') . ' in ' . $next->format('d/m/Y') . '.');
                $this->log('I go sleep ' . $sleep . ' second(s).');
            }

            sleep($sleep);

            if ($sleep > 0)
                $this->refresh();

        } while ($sleep > 0);
    }

}