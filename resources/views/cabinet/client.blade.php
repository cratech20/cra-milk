@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Личный кабинет</div>

                    <div class="card-body">
                        <a href="{{ route('reports.liters') }}">Отчёт "Литры по коровам в
                            день"</a><br>
                        <a href="{{ route('reports.liters.hour') }}">Отчёт "Литры по коровам 2 раза в
                            день"</a><br>
                        <a href="{{ route('reports.liters.device') }}">Отчёт "Литры по устройствам в день"</a><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
