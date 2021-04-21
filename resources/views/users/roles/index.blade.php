@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Роли пользователей</div>

                    <div class="card-body">

                        <p>
                            * - админ - admin<br>
                            * - производство - production<br>
                            * - сервисник - service technician<br>
                            * - монтажник - fitter<br>
                            * - техподдержка - support<br>
                            * - менеджер от нас - manager<br>
                            * - предприятие - client<br>
                            * - разработчик - developer<br>
                            * - наш работник - employee
                        </p>

                        @if(@isset($users))
                            <form action="{{ route('users.roles.update') }}" method="POST">
                                {{ csrf_field() }}
                                <button class="btn btn-success mb-3" type="submit">Сохранить</button>

                                <table class="table table-striped table-bordered">

                                    <thead>
                                    <tr>
                                        <th>Имя</th>
                                        @foreach($roles as $role)
                                            <th>{{ $role->name }}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                {{ $user->name }} ({{ $user->email }})
                                            </td>
                                            @foreach($roles as $role)
                                                <td><input type="checkbox"
                                                           name="users[{{ $user->id }}][{{ $role->name }}]"
                                                           {{ ($user->hasRole($role->name)) ? 'checked' : ''}}
                                                           value="1"></td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        @else
                            Ничего не найдено
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
