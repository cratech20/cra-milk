@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Регистрация пользователей</div>

                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            {{ csrf_field() }}
                            ИНН
                            <p>
                                <input type="number" class="form-control" name="inn"
                                       autocomplete="off" value="{{ $data['data']['inn'] ?? '' }}">
                            </p>
                            Название
                            <p>
                                <input type="text" class="form-control" name="name" required="required"
                                       autocomplete="off" value="{{ $data['value'] ?? '' }}">
                            </p>
                            E-mail
                            <p>
                                <input type="text" class="form-control" name="email" required="required"
                                       autocomplete="off">
                            </p>
                            Пароль
                            <p>
                                <input type="text" class="form-control" name="password" required="required"
                                       autocomplete="off">
                            </p>
                            <input type="submit" class="btn btn-success" value="Зарегистрировать">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
