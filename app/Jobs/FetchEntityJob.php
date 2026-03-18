<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class FetchEntityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $entity;
    protected string $baseUri;
    protected string $apiKey;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
        $this->baseUri = env('API_BASE_URI', 'http://109.73.206.144:6969/');
        $this->apiKey = env('API_KEY');
    }

    public function handle(): void
    {
        $map = [
            'sales'   => ['model' => Sale::class,   'useDateTo' => true],
            'orders'  => ['model' => Order::class,  'useDateTo' => true],
            'incomes' => ['model' => Income::class, 'useDateTo' => true],
            'stocks'  => ['model' => Stock::class,  'useDateTo' => false],
        ];

        if (!isset($map[$this->entity])) {
            return;
        }

        $modelClass = $map[$this->entity]['model'];
        $useDateTo = $map[$this->entity]['useDateTo'];

        $this->fetchEndpoint($modelClass, $useDateTo);
    }

    private function fetchEndpoint($modelClass, $useDateTo): void
    {
        $page = 1;
        $limit = 50;
        $total = 0;

        if ($this->entity === 'stocks') {
            $dateFrom = now()->toDateString();
        } else {
            $dateFrom = now()->subDays(30)->toDateString();
        }
        $dateTo = now()->toDateString();

        do {
            $params = [
                'dateFrom' => $dateFrom,
                'page'     => $page,
                'limit'    => $limit,
                'key'      => $this->apiKey,
            ];

            if ($useDateTo) {
                $params['dateTo'] = $dateTo;
            }

            $url = rtrim($this->baseUri, '/') . '/api/' . $this->entity;

            try {
                $response = Http::timeout(10)->get($url, $params);
            } catch (ConnectionException $e) {
                break;
            }

            if ($response->failed()) {
                break;
            }

            $json = $response->json();
            $items = $json['data'] ?? [];

            if (empty($items)) {
                break;
            }

            foreach ($items as $item) {
                $externalId = $item['id'] ?? null;
                if ($externalId) {
                    $modelClass::updateOrCreate(
                        ['external_id' => $externalId],
                        ['payload' => $item]
                    );
                } else {
                    $modelClass::create(['payload' => $item]);
                }
                $total++;
            }

            $page++;
            $lastPage = $json['meta']['last_page'] ?? null;
            if ($lastPage && $page > $lastPage) {
                break;
            }

        } while (!empty($items));

    }
}