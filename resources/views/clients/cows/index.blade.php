@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Список подразделений компании {{ $client->name }}</div>

                    <div class="card-body">

                        <p>
                            <a href="{{ route('clients.cows.linking') }}" class="btn btn-success">Обновить
                                группировку и
                                наименование коров</a>
                        </p>

                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($cows as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->cow_id }}
                                    </td>
                                    <td>
                                        {{ $item->calculated_name }}
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        Коров не найдено. Нажмите обновить.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
