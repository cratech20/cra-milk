@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Личный кабинет</div>

                    <div class="card-body">
                        <a href="{{ route('reports.liters') }}">Отчёт PowerBI литры по коровам</a><br>
                        <a href="{{ route('reports.liters.hour') }}">Отчёт PowerBI литры по коровам 2 раза в
                            день</a><br>
                        <a href="{{ route('reports.liters.device') }}">Отчёт PowerBI литры по устройствам</a><br>
                        <a href="{{ route('reports.impulse') }}">Отчёт PowerBI импульсы по коровам</a><br>
                        <a href="{{ route('reports.impulse.device') }}">Отчёт PowerBI импульсы по устройствам</a><br>
                        <a href="https://functions.yandexcloud.net/d4eks53mf45uqn38bvn1">Последние сообщения с
                            устройств</a><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
