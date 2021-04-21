@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Регистрация пользователей</div>

                    <div class="card-body">
                        <form action="{{ route('users.store') }}">
                            {{ csrf_field() }}
                            Введите ИНН
                            <input type="text" class="form-control" name="inn" required="required" autocomplete="off">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
