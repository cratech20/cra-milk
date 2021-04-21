@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Пользователей</div>

                    <div class="card-body">

                        <p class="float-right">
                            <a href="{{ route('users.roles.index') }}" class="btn btn-primary">Назначить роли</a>
                            <a href="{{ route('users.create') }}" class="btn btn-success">Зарегистрировать
                                пользователя</a>
                        </p>
                        @if(@isset($users))

                            <table class="table table-striped table-bordered">

                                <thead>
                                <tr>
                                    <th>Имя</th>
                                    <th>Действие</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            {{ $user->name }} ({{ $user->email }})
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-outline-danger">Заблокировать</a>
                                            <a href="#" class="btn btn-outline-danger">Удалить</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            Ничего не найдено
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
