@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Личный кабинет</div>

                    <div class="card-body">
                        <a href="{{ route('reports.liters') }}">Отчёт PowerBI литры</a><br>
                        <a href="{{ route('reports.impulse') }}">Отчёт PowerBI импульсы</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
