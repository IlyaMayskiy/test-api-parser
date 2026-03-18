@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <h1>Данные из тестового API</h1>

    <div class="list-group">
        <a href="{{ route('data.show', 'sales') }}" class="list-group-item list-group-item-action">Продажи (Sales)</a>
        <a href="{{ route('data.show', 'orders') }}" class="list-group-item list-group-item-action">Заказы (Orders)</a>
        <a href="{{ route('data.show', 'stocks') }}" class="list-group-item list-group-item-action">Склады (Stocks)</a>
        <a href="{{ route('data.show', 'incomes') }}" class="list-group-item list-group-item-action">Доходы (Incomes)</a>
        <a href="{{ route('data.fetch') }}" class="btn btn-primary" onclick="return confirm('Обновить все данные? \nЭто займет время')">Обновить все данные</a>
    </div>
@endsection