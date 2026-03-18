@extends('app')

@section('title', 'Главная')

@section('content')
    <h1>Данные из тестового API</h1>

    <div class="list-group">
        <a href="{{ route('data.sales') }}" class="list-group-item list-group-item-action">Продажи (Sales)</a>
        <a href="{{ route('data.orders') }}" class="list-group-item list-group-item-action">Заказы (Orders)</a>
        <a href="{{ route('data.stocks') }}" class="list-group-item list-group-item-action">Склады (Stocks)</a>
        <a href="{{ route('data.incomes') }}" class="list-group-item list-group-item-action">Доходы (Incomes)</a>
    </div>
@endsection