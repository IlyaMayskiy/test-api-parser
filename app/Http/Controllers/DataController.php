<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;
use Illuminate\Support\Facades\Http;

class DataController extends Controller
{

    public function index()
    {
        return view('data.index');
    }


    public function sales()
    {
        $items = Sale::paginate(20);
        return view('data.sales', compact('items'));
    }

    public function orders()
    {
        $items = Order::paginate(20);
        return view('data.orders', compact('items'));
    }

    public function stocks()
    {
        $items = Stock::paginate(20);
        return view('data.stocks', compact('items'));
    }

    public function incomes()
    {
        $items = Income::paginate(20);
        return view('data.incomes', compact('items'));
    }

    public function fetch()
    {
        set_time_limit(300);

        $baseUri = "http://109.73.206.144:6969/";
        $apiKey = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';

        if (!$apiKey) {
            return redirect()->back()->with('error', 'Не указан API ключ в .env');
        }

        $messages = [];

        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'sales', Sale::class, true);
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'orders', Order::class, true);
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'incomes', Income::class, true);
        $messages[] = $this->fetchEndpoint($baseUri, $apiKey, 'stocks', Stock::class, false);

        return redirect()->back()->with('message', implode(' | ', $messages));
    }

    private function fetchEndpoint($baseUri, $apiKey, $endpoint, $modelClass, $useDateTo)
    {
        $page = 1;
        $limit = 500;
        $dateFrom = now()->subDays(30)->toDateString();
        $dateTo = now()->toDateString();
        $total = 0;

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
            $response = Http::get($url, $params);

            if ($response->failed()) {
                return "Ошибка {$endpoint}: " . $response->body();
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