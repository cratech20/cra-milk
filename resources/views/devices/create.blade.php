@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Добавить устройство</div>

                    <div class="card-body">
                        <form action="{{ route('devices.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="x">Название</label>
                                <input type="text" class="form-control" name="name" value="">
                            </div>
                            <div class="form-group">
                                <label for="x">ID в Я.Облако *</label>
                                <input type="text" class="form-control" name="device_id" value="">
                            </div>
                            <div class="form-group">
                                <label for="x">Пароль в Я.Облако</label>
                                <input type="text" class="form-control" name="password" value="">
                            </div>
                            <div class="form-group">
                                <label for="x">Серийный номер *</label>
                                <input type="text" class="form-control" name="serial_number" value="">
                            </div>
                            <button type="submit" class="btn btn-success">Добавить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
