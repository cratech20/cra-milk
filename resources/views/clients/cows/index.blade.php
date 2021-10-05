@extends('layouts.app')

@section('content')
    <script type="text/javascript">
        function change_cow_group_click() {
            let confirmed = confirm('Вы действительно хотите поменять группу у выбранных коров?');
            if (confirmed) {
                document.getElementById("form_action").value = "change_cow_group";
                document.getElementById("item_id").value = document.getElementById("cow-groups").value;
                document.getElementById("change_form").submit()
            }
        }
    </script>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Список подразделений компании {{ $client->name }}</div>

                    <div class="card-body">

                        <p>
                            <a href="{{ route('clients.cows.linking') }}" class="btn btn-success">Обновить
                                группировку и
                                наименование коров</a>
                        </p>

                        <p>Изменить группу на:
                            <select id="cow-groups">
                                @foreach($client->cowGroups as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-success" onclick="change_cow_group_click()">Сохранить</button>
                        </p>

                        <form action="{{ route('clients.cows.groups.change') }}" method="POST" id="change_form">
                            @csrf
                            <input type="hidden" name="action" id="form_action">
                            <input type="hidden" name="item_id" id="item_id">

                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>☑</th>
                                    <th>#</th>
                                    <th>Группа</th>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Внутренний номер</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($cows as $item)
                                    <tr>
                                        <td><input type="checkbox" name="devices[]" value="{{ $item->id }}"></td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->group->calculated_name ?? '' }}
                                        </td>
                                        <td>
                                            {{ $item->cow_id }}
                                        </td>
                                        <td>
                                            {{ $item->calculated_name }}
                                        </td>
                                        <td>
                                            {{ $item->internal_id }}
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            Коров не найдено. Нажмите обновить.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
