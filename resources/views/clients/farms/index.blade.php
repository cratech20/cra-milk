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
                            @forelse($farms as $farm)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $farm->name }}
                                    </td>
                                    <td>
                                        <a href="{{ route('clients.farms.index', $client) }}">Удалить</a><br>
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

                        <h3>Создать ферму</h3>
                        <div class="col-md-6">
                            <form action="{{ route('clients.farms.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="x">Название</label>
                                    <input type="text" class="form-control" name="name" value="">
                                    <input type="hidden" name="user_id" value="{{ $client->id }}">
                                </div>
                                <button type="submit" class="btn btn-success">Добавить</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
