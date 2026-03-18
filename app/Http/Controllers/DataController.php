<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use App\Jobs\FetchEntityJob;

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

        if (!isset($map[$entity])) {
            abort(404);
        }

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
        $entities = ['sales', 'orders', 'incomes', 'stocks'];
        foreach ($entities as $entity) {
            FetchEntityJob::dispatch($entity);
        }

        return redirect()->back()->with('message', 'Обновляется...');
    }

    public function fetchEntity($entity)
    {
        $allowed = ['sales', 'orders', 'stocks', 'incomes'];
        FetchEntityJob::dispatch($entity);
        return redirect()->back()->with('message', "{$entity} обновляется...");
    }
}