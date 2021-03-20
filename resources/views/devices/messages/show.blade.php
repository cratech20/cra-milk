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
                        <table class="table table-bordered table-striped">
                            @foreach ($json as $row)
                                <tr>
                                    <td>
                                        {{ Carbon::parse($row->event_datetime)->format('H:i:s d.m.Y') }}
                                    </td>
                                    <td>
                                        {{ print_r($row->payload, 1) }}<br>
                                        {{--                            {{ Carbon::parse($row['t'])->format('H:i:s d.m.Y') }}: {{ print_r($row['d'], 1) }}<br>--}}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
