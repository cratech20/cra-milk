@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Список клиентов</div>

                    <div class="card-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Название</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $client->name }}
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.divisions.index', $client) }}">Перейти к
                                            подразделениям</a><br>
                                        <a href="{{ route('clients.farms.index', $client) }}">Перейти к
                                            фермам</a><br>
                                        <a href="{{ route('devices.summary_table', $client) }}">Перейти к
                                            итоговой таблице по устройствам</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        Клиентов не найдено. Возможно, вы забыли дать им права "Клиент" на странице.
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
