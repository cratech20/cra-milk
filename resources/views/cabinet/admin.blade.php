@extends('layouts.spa')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Личный кабинет</div>

                    <div class="card-body">
                        <strong>Правильные данные</strong><br>
                        <a href="{{ route('reports.liters') }}">Отчёт "Литры по коровам в
                            день"</a><br>
                        <a href="{{ route('reports.liters.hour') }}">Отчёт "Литры по коровам 2 раза в
                            день"</a><br>
                        <a href="{{ route('reports.liters.hour2') }}">Отчёт "Литры по коровам 3 раза в
                            день"</a><br>
                        <a href="{{ route('reports.liters.device') }}">Отчёт "Литры по устройствам в день"</a><br>
                        <strong>Неправильные данные</strong><br>
                        <a href="{{ route('reports.impulse') }}">Отчёт PowerBI импульсы по коровам</a><br>
                        <a href="{{ route('reports.impulse.device') }}">Отчёт PowerBI импульсы по устройствам</a><br>
                        <a href="{{ route('reports.mlk') }}">.mlk скачать</a><br>
                        <a href="https://functions.yandexcloud.net/d4eks53mf45uqn38bvn1">Последние сообщения с
                            устройств</a><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
