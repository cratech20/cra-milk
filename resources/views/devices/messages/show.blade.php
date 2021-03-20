<?php

use Carbon\Carbon;

?>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Сообщения устройства {{ $device->name }}</div>

                    <div class="card-body">
                        @foreach ($json as $row)
                            {{ Carbon::parse($row->event_datetime)->format('H:i:s d.m.Y') }}
                            : {{ print_r($row->payload, 1) }}<br>
                            {{--                            {{ Carbon::parse($row['t'])->format('H:i:s d.m.Y') }}: {{ print_r($row['d'], 1) }}<br>--}}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
