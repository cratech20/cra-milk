@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Список групп коров компании {{ $client->name }}</div>

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
                                <th>Название</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($cowGroups as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-outline-danger">Удалить</a>
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

                        <h3>Создать группу коров</h3>
                        <div class="col-md-6">
                            <form action="{{ route('clients.cows.groups.store') }}" method="POST">
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
