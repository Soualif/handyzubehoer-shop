<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('shop:sync-products')->everySixHours()->withoutOverlapping();
Schedule::command('shop:sync-tracking')->hourly()->withoutOverlapping();
