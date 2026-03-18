@extends('app')

@section('title', $title ?? 'Просмотр данных')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>{{ $title ?? 'Данные' }}</h1>
        <a href="{{ route('data.fetch.entity', $entity) }}" class="btn btn-outline-primary" onclick="return confirm('Загрузить?')"> Загрузить {{ $title }}?</a>
        <a href="{{ route('data.index') }}" class="btn btn-secondary">На главную</a>
    </div>

    @if($items->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID записи</th>
                        <th>Данные</th>
                        <th>Дата создания</th>
                        <th>Дата обновления</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id ?? '—' }}</td>
                        <td>
                            <pre class="mb-0" style="max-height: 200px; overflow-y: auto;">{{ json_encode($item->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->updated_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($items->hasPages())
        <nav aria-label="Пагинация">
            <ul class="pagination justify-content-center">
                @if ($items->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Назад</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $items->previousPageUrl() }}" rel="prev">Назад</a></li>
                @endif

                @php
                    $current = $items->currentPage();
                    $last = $items->lastPage();
                    $start = max($current - 1, 1);
                    $end = min($current + 1, $last);
                @endphp

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $items->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endfor

                @if ($items->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $items->nextPageUrl() }}" rel="next">Вперёд</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Вперёд</span></li>
                @endif
            </ul>
        </nav>
        @endif
    @endif
@endsection