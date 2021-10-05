@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Редактирование</div>

                    <div class="card-body">
                        <form action="{{ route('clients.cows.edit', $cows->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="x">Внутренний номер</label>
                                <input type="number" class="form-control" name="internal_code" step="0.0001"
                                       value="{{ $cows->internal_code }}">
                            </div>
                           
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
