<?php

use App\Console\Commands\DeleteExpiredPastes;
 
use Illuminate\Support\Facades\Schedule;
 

Schedule::call(DeleteExpiredPastes::class)->daily();

