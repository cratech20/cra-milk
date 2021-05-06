@extends('layouts.app')

@section('content')
    <script type="text/javascript">
        function change_farm_click() {
            document.getElementById("form_action").value = "change_farm";
        }
    </script>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ $title }}</div>

                    <div class="card-body">

                        @role('admin')
                        <p>
                            <a href="{{ route('devices.create') }}" class="btn btn-outline-success">Добавить
                                подразделение</a>
                            <a href="{{ route('devices.create') }}" class="btn btn-outline-success">Добавить ферму</a>
                            <a href="{{ route('devices.create') }}" class="btn btn-outline-success">Добавить
                                устройство</a>
                            <a href="{{ route('devices.create') }}" class="btn btn-outline-primary">Управление
                                коровами</a>
                        </p>
                        @endrole

                        <p>
                            Сначала выберите галочками устройства, над которыми хотите произвести действия ниже.
                        </p>
                        <p>Изменить подразделение:
                            <select name="" id="">
                                <option value="">Выбрать</option>
                                <option value="">Основное подразделение</option>
                            </select>
                            <button class="btn btn-success" onclick="change_farm_click()">Сохранить</button>
                        </p>

                        <p>Изменить ферму:
                            <select name="" id="">
                                <option value="">Выбрать</option>
                                <option value="">Основная ферма</option>
                            </select>
                            <button id="save_farm" class="btn btn-success" onclick="change_farm_click()">Сохранить
                            </button>
                        </p>

                        @if(!$devices->isEmpty())
                            <form action="" method="POST">
                                <input type="hidden" name="action" id="form_action">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>☑</th>
                                        <th>#</th>
                                        <th>Подразделение</th>
                                        <th>Ферма</th>
                                        <th>Устройство</th>
                                        <th>Серийный номер</th>
                                        <th>ID</th>
                                        <th>Дата добавления</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($devices as $key => $device)
                                        <tr>
                                            <td><input type="checkbox" name="{{ $device->id }}"></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $device->farm->division->name ?? '' }}</td>
                                            <td>{{ $device->farm->name ?? '' }}</td>
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
                                                    <li><a href="{{ route('devices.destroy', $device) }}">Удалить</a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        @else
                            <p>Список пуст</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
