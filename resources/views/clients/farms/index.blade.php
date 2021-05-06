@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Список ферм компании {{ $client->name }}</div>

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
                            @forelse($divisions as $division)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $division->name }}
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.farm.index', [$client, $division]) }}">Перейти к
                                            устройствам</a><br>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        Ферм не найдено. Создайте их.
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
