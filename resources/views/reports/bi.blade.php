@php
    $stickyCol = $stickyCol ?? 0;
    $downloadRoute = Illuminate\Support\Facades\Route::currentRouteName();
@endphp

@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <style type="text/css">
        tr.group,
        tr.group:hover {
            background-color: #ddd !important;
        }

        .sticky-column {
            position: sticky;
            position: -webkit-sticky;
            left: 0;
            z-index: 1;
            background: #f9f9f9;
            width: 100px; /* How can I make this dynamic? */
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Отчеты</div>
                    <div class="card-body">

                        <p>
                            <a href="{{ route('clients.cows.linking') }}" class="btn btn-success">Обновить
                                группировку и
                                наименование коров</a>

                            <a href="{{ route($downloadRoute, ['download' => true]) }}" class="btn btn-success">Скачать
                                отчет</a>

                            <a href="/" class="btn btn-success">Назад</a>
                        </p>

                        <table id="example" class="display bg-white" style="width:100%">
                            <thead>
                            <tr>
                                <th></th>
                                @foreach($data['head'] as $th)
                                    <th>{{ $th }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($data['body'] ?? [] as $cowRow)
                                    <tr>
                                        <td></td>
                                        @foreach($cowRow as $key => $td)
                                            <td class="{{ $key === $stickyCol ? 'sticky-column' : '' }}">{{ $td }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"
                                type="text/javascript"></script>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $.noConflict();

                                var groupColumn = {!! $groupColumn ?? 'false' !!};
                                var orderColumn = groupColumn ? groupColumn : 0;

                                var settings = {
                                    "order": [[orderColumn, 'asc']],
                                    "displayLength": 300,
                                    "drawCallback": function (settings) {
                                        var api = this.api();
                                        var rows = api.rows({page: 'current'}).nodes();
                                        var last = null;

                                        if (!groupColumn) {
                                            return 0;
                                        }

                                        api.column(groupColumn, {page: 'current'}).data().each(function (group, i) {
                                            if (last !== group) {
                                                $(rows).eq(i).before(
                                                    '<tr class="group"><td colspan="100%">' + group + '</td></tr>'
                                                );

                                                last = group;
                                            }
                                        });
                                    }
                                };

                                if (groupColumn) {
                                    settings.columnDefs = [
                                        {"visible": false, "targets": groupColumn}
                                    ];
                                }

                                var table = $('#example').DataTable(settings);

                                // Order by the grouping
                                $('#example tbody').on('click', 'tr.group', function () {
                                    var currentOrder = table.order()[0];
                                    if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
                                        table.order([groupColumn, 'desc']).draw();
                                    } else {
                                        table.order([groupColumn, 'asc']).draw();
                                    }
                                });

                                table.on('order.dt search.dt', function () {
                                    table.column(0, {
                                        search: 'applied',
                                        order: 'applied'
                                    }).nodes().each(function (cell, i) {
                                        cell.innerHTML = i + 1;
                                    });
                                }).draw();
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
