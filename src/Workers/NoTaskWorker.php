<?php

namespace AoQueue\Workers;

use AoQueue\Workers\Traits\CanTrait;
use AoQueue\Workers\Traits\RepeatTrait;
use AoQueue\Workers\Traits\TypeTrait;
use Carbon\Carbon;

abstract class NoTaskWorker extends RepeaterWorker
{



}