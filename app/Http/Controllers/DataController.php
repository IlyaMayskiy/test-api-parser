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

    public function sales()
    {
        $items = Sale::paginate(20);
        return view('data.items', [
            'entity' => 'sales',
            'title' => 'Продажи',
            'items' => $items
        ]);
    }

    public function orders()
    {
        $items = Order::paginate(20);
        return view('data.items', [
            'entity' => 'orders',
            'title' => 'Заказы',
            'items' => $items
        ]);
    }

    public function stocks()
    {
        $items = Stock::paginate(20);
        return view('data.items', [
            'entity' => 'stocks',
            'title' => 'Склады',
            'items' => $items
        ]);
    }

    public function incomes()
    {
        $items = Income::paginate(20);
        return view('data.items', [
            'entity' => 'incomes',
            'title' => 'Доходы',
            'items' => $items
        ]);
    }

    public function fetch()
    {
        set_time_limit(300);

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
        set_time_limit(300);

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
        $limit = 100;
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
                return "{$endpoint}: прервано по таймауту (загружено {$total} записей)";
            }

            if ($response->failed()) {
                return "{$endpoint}: ошибка HTTP, загружено {$total} записей";
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

        return "{$endpoint}: загружено {$total} записей";
    }
}