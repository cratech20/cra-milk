@extends('layouts.app')

@section('content')
    <script type="text/javascript">
        function change_client_click() {
            let confirmed = confirm('Вы действительно хотите поменять клиента у выбранных устройтсв?');
            if (confirmed) {
                document.getElementById("client_id").value = document.getElementById("clients").value;
                document.getElementById("change_client").submit()
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
                            <a href="{{ route('devices.create') }}" class="btn btn-success">Добавить устройство</a>
                        </p>
                        @endrole

                        <p>Изменить клиента на:
                            <select name="clients" id="clients">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-success" onclick="change_client_click()">Сохранить</button>
                        </p>

                        @if(!$devices->isEmpty())

                            <form action="{{ route('devices.clients.change') }}" method="POST" id="change_client">
                                @csrf
                                <input type="hidden" name="action" id="form_action" value="change_client">
                                <input type="hidden" name="user_id" id="client_id">

                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>☑</th>
                                        <th>#</th>
                                        <th>Клиент</th>
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
                                            <td><input type="checkbox" name="devices[]" value="{{ $device->id }}"></td>
                                            <td>{{ ($key + 1) }}</td>
                                            <td>{{ $device->user->name ?? '' }}</td>
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
