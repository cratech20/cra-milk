@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ $title }}</div>

                    <div class="card-body">

                        @role('admin')
                        <p>
                            <a href="{{ route('devices.create') }}" class="btn btn-success">Добавить устройство</a>
                        </p>
                        @endrole

                        @if(!$devices->isEmpty())

                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Название</th>
                                    <th>Серийный номер</th>
                                    <th>ID</th>
                                    <th>Дата добавления</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($devices as $key => $device)
                                    <tr>
                                        <td>{{ ($key + 1) }}</td>
                                        <td>{{ $device->name }}</td>
                                        <td>{{ $device->serial_number }}</td>
                                        <td>{{ $device->device_id }}</td>
                                        <td>{{ $device->created_at }}</td>
                                        <td>
                                            <ul class="ul-links">
                                                <li><a href="{{ route('devices.messages', $device) }}">Смотреть
                                                        сообщения</a></li>
                                                <li><a href="#">Показать пароль</a></li>
                                                <li><a href="#">Редактировать</a></li>
                                                <li><a href="{{ route('devices.destroy', $device) }}">Удалить</a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else
                            <p>Список пуст</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
