@extends('layouts.app')

@section('content')
    <script type="text/javascript">
        function change_farm_click() {
            let confirmed = confirm('Вы действительно хотите поменять ферму у выбранных устройтсв?');
            if (confirmed) {
                document.getElementById("form_action").value = "change_farm";
                document.getElementById("item_id").value = document.getElementById("farms").value;
                document.getElementById("change_form").submit()
            }
        }

        function change_division_click() {
            let confirmed = confirm('Вы действительно хотите поменять подразделение у выбранных устройтсв?');
            if (confirmed) {
                document.getElementById("form_action").value = "change_division";
                document.getElementById("item_id").value = document.getElementById("divisions").value;
                document.getElementById("change_form").submit()
            }
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
                            <a href="{{ route('clients.divisions.index', $client) }}" class="btn btn-outline-success">Добавить
                                подразделение</a>
                            <a href="{{ route('clients.farms.index', $client) }}" class="btn btn-outline-success">Добавить
                                ферму</a>
                            <a href="{{ route('devices.create') }}" class="btn btn-outline-success">Добавить
                                устройство</a>
                            <a href="#" class="btn btn-outline-primary">Управление
                                коровами</a>
                        </p>
                        @endrole

                        <p>
                            Сначала выберите галочками устройства, над которыми хотите произвести действия ниже.
                        </p>

                        <p>Изменить подразделение на:
                            <select id="divisions">
                                @foreach($client->divisions as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-success" onclick="change_division_click()">Сохранить</button>
                        </p>

                        <p>Изменить ферму на:
                            <select id="farms">
                                @foreach($client->farms as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button id="save_farm" class="btn btn-success" onclick="change_farm_click()">Сохранить
                            </button>
                        </p>

                        @if(!$devices->isEmpty())
                            <form action="{{ route('devices.clients.change') }}" method="POST" id="change_form">
                                @csrf
                                <input type="hidden" name="action" id="form_action">
                                <input type="hidden" name="item_id" id="item_id">

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
                                            <td><input type="checkbox" name="devices[]" value="{{ $device->id }}"></td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $device->division->name ?? '' }}</td>
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
