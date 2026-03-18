<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
        Artisan::call('wb:fetch');
        $output = Artisan::output();

        return redirect()->back()->with('message', 'Данные обновлены: ' . $output);
    }
}