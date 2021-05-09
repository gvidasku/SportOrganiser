<?php

namespace Illuminate\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ListFailedCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all of the failed queue sportevents';

    /**
     * The table headers for the command.
     *
     * @var string[]
     */
    protected $headers = ['ID', 'Connection', 'Queue', 'Class', 'Failed At'];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (count($sportevents = $this->getFailedsportevents()) === 0) {
            return $this->info('No failed sportevents!');
        }

        $this->displayFailedsportevents($sportevents);
    }

    /**
     * Compile the failed sportevents into a displayable format.
     *
     * @return array
     */
    protected function getFailedsportevents()
    {
        $failed = $this->laravel['queue.failer']->all();

        return collect($failed)->map(function ($failed) {
            return $this->parseFailedsportevent((array) $failed);
        })->filter()->all();
    }

    /**
     * Parse the failed sportevent row.
     *
     * @param  array  $failed
     * @return array
     */
    protected function parseFailedsportevent(array $failed)
    {
        $row = array_values(Arr::except($failed, ['payload', 'exception']));

        array_splice($row, 3, 0, $this->extractsporteventName($failed['payload']) ?: '');

        return $row;
    }

    /**
     * Extract the failed sportevent name from payload.
     *
     * @param  string  $payload
     * @return string|null
     */
    private function extractsporteventName($payload)
    {
        $payload = json_decode($payload, true);

        if ($payload && (! isset($payload['data']['command']))) {
            return $payload['sportevent'] ?? null;
        } elseif ($payload && isset($payload['data']['command'])) {
            return $this->matchsporteventName($payload);
        }
    }

    /**
     * Match the sportevent name from the payload.
     *
     * @param  array  $payload
     * @return string|null
     */
    protected function matchsporteventName($payload)
    {
        preg_match('/"([^"]+)"/', $payload['data']['command'], $matches);

        return $matches[1] ?? $payload['sportevent'] ?? null;
    }

    /**
     * Display the failed sportevents in the console.
     *
     * @param  array  $sportevents
     * @return void
     */
    protected function displayFailedsportevents(array $sportevents)
    {
        $this->table($this->headers, $sportevents);
    }
}
