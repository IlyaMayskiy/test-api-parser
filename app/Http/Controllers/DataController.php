<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class DataController extends Controller
{
    public function index()
    {
        return view('data.index');
    }

    public function show($entity)
    {
        $map = [
            'sales'   => ['model' => Sale::class,   'title' => 'Продажи'],
            'orders'  => ['model' => Order::class,  'title' => 'Заказы'],
            'stocks'  => ['model' => Stock::class,  'title' => 'Склады'],
            'incomes' => ['model' => Income::class, 'title' => 'Доходы'],
        ];

        $modelClass = $map[$entity]['model'];
        $title = $map[$entity]['title'];

        $items = $modelClass::paginate(20);

        return view('data.items', [
            'entity' => $entity,
            'title'  => $title,
            'items'  => $items,
        ]);
    }

    public function fetch()
    {
        set_time_limit(60);

        $baseUri = "http://109.73.206.144:6969/";
        $apiKey = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';

        $messages = [];
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'sales', Sale::class, true);
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'orders', Order::class, true);
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'incomes', Income::class, true);
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'stocks', Stock::class, false);

        return redirect()->back()->with('message', implode(' | ', $messages));
    }

    public function fetchEntity($entity)
    {
        set_time_limit(60);

        $baseUri = "http://109.73.206.144:6969/";
        $apiKey = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';

        $map = [
            'sales'   => ['model' => Sale::class,   'useDateTo' => true],
            'orders'  => ['model' => Order::class,  'useDateTo' => true],
            'incomes' => ['model' => Income::class, 'useDateTo' => true],
            'stocks'  => ['model' => Stock::class,  'useDateTo' => false],
        ];

        if (!isset($map[$entity])) {
            return redirect()->back()->with('error', 'Неизвестная сущность');
        }

        $modelClass = $map[$entity]['model'];
        $useDateTo = $map[$entity]['useDateTo'];

        $message = $this->fetchEndpoint($baseUri, $apiKey, $entity, $modelClass, $useDateTo);

        return redirect()->back()->with('message', $message);
    }

    private function fetchEndpoint($baseUri, $apiKey, $endpoint, $modelClass, $useDateTo)
    {
        $page = 1;
        $limit = 50;
        $total = 0;

        if ($endpoint === 'stocks') {
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
                'key'      => $apiKey,
            ];

            if ($useDateTo) {
                $params['dateTo'] = $dateTo;
            }

            $url = rtrim($baseUri, '/') . '/api/' . $endpoint;

            try {
                $response = Http::timeout(10)->get($url, $params);
            } catch (ConnectionException $e) {
                return "{$endpoint}: Загружено {$total} записей";
            }

            if ($response->failed()) {
                return "{$endpoint}: Загружено {$total} записей";
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

        return "{$endpoint}: Загружено {$total} записей";
    }
}