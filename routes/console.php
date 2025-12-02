<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('merges:process')->everyMinute();
