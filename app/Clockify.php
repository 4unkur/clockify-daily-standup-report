<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Http\Client\Factory;
use Illuminate\Contracts\Config\Repository;

class Clockify
{
    private $client;

    private $config;

    private $baseUrl;

    private $baseReportsUrl;

    private $workspaceId;


    public function __construct(Factory $client, Repository $config)
    {
        $this->client = $client;
        $this->config = $config;

        $this->baseUrl = $this->config->get('app.clockify_api.base_url');
        $this->baseReportsUrl = $this->config->get('app.clockify_api.reports_url');
        $this->workspaceId = $this->config->get('app.clockify_api.workspace_id');
    }


    public function generateReport(): string
    {
        $fromDate = Carbon::now()->dayOfWeek === Carbon::MONDAY
            ? Carbon::parse('Last Friday')
            : Carbon::yesterday();

        $response = $this->client->withHeaders(['X-Api-Key' => $this->config->get('app.clockify_api.key')])
            ->asJson()
            ->acceptJson()
            ->post("$this->baseReportsUrl/workspaces/$this->workspaceId/reports/summary", [
                "dateRangeStart" => $fromDate->toJSON(),
                "dateRangeEnd" => Carbon::today()->toJSON(),
                "sortOrder" => "ASCENDING",
                "summaryFilter" => [
                    "sortColumn" => "GROUP",
                    "groups" => [
                        "PROJECT",
                        "TASK",
                        "TIMEENTRY"
                    ]
                ],
                "amountShown" => "HIDE_AMOUNT",
            ]);

        $log = '';

        if ($response->ok()) {
            $data = $response->json();

            foreach ($data['groupOne'] as $project) {
                $log .= $project['name'] . "\n-------\n";

                foreach ($project['children'] as $task) {
                    $log .= $task['nameLowerCase'] . "\n";

                    try {
                        foreach ($task['children'] as $subtask) {
                            $log .= '* ' . $subtask['name'] . "\n";
                        }
                    } catch (\Exception $e) {
                        dd($e, $task);
                    }
                    $log .= "\n";
                }
            }
        }

        return $log;
    }
}
