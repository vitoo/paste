<?php

use App\Console\Commands\DeleteExpiredPastes;
 
use Illuminate\Support\Facades\Schedule;
 

Schedule::command('delete-expired-pastes')->daily();

