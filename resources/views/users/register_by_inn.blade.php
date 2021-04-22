@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Регистрация пользователей</div>

                    <div class="card-body">
                        <form action="{{ route('users.registration.create') }}" method="GET">
                            Введите ИНН
                            <p>
                                <input type="number" class="form-control" name="inn" required="required"
                                       autocomplete="off">
                            </p>
                            <input type="submit" class="btn btn-success" value="Далее">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
