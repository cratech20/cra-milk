@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Добавить устройство</div>

                    <div class="card-body">
                        <form action="{{ route('devices.update', $device->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="x">Название</label>
                                <input type="text" class="form-control" name="name" value="{{ $device->name }}">
                            </div>
                            <div class="form-group">
                                <label for="x">ID в Я.Облако *</label>
                                <input type="text" class="form-control" name="device_id"
                                       value="{{ $device->device_id }}">
                            </div>
                            <div class="form-group">
                                <label for="x">Пароль в Я.Облако</label>
                                <input type="text" class="form-control" name="password" value="{{ $device->password }}">
                            </div>
                            <div class="form-group">
                                <label for="x">Серийный номер *</label>
                                <input type="text" class="form-control" name="serial_number"
                                       value="{{ $device->serial_number }}">
                            </div>
                            <div class="form-group">
                                <label for="x">Вес импульса</label>
                                <input type="number" class="form-control" name="weight" step="0.0001"
                                       value="{{ $device->weight }}">
                            </div>
                            <div class="form-group">
                                <label for="x">Дата применения веса импульса по МСК (+3:00)</label>
                                <input type="date" class="form-control" name="weight_set_at"
                                       value="{{ \Carbon\Carbon::parse($device->weight_set_at)->format('Y-m-d') }}">
                            </div>
                            <button type="submit" class="btn btn-success">Добавить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
