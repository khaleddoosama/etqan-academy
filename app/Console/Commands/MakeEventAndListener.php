<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


class MakeEventAndListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:event-listener {name : The name of the event and listener}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event and its listener';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $eventName = $name . 'Event';
        // Create the Event
        Artisan::call('make:event', ['name' => $eventName]);
        $this->info("Event $eventName created successfully.");

        // Create the Listener
        $listenerName = $name . 'Listener';
        Artisan::call('make:listener', ['name' => $listenerName, '--event' => $eventName]);
        $this->info("Listener $listenerName created successfully.");
    }
}
