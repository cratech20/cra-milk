@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <style type="text/css">
        tr.group,
        tr.group:hover {
            background-color: #ddd !important;
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
                        </p>

                        <table id="example" class="display bg-white" style="width:100%">
                            <thead>
                            <tr>
                                @foreach($data['head'] as $th)
                                    <th>{{ $th }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data['body'] as $cowRow)
                                <tr>
                                    @foreach($cowRow as $td)
                                        <td>{{ $td }}</td>
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

                                var groupColumn = 2;

                                $('#example').DataTable({
                                    "columnDefs": [
                                        {"visible": false, "targets": groupColumn}
                                    ],
                                    "order": [[groupColumn, 'asc']],
                                    "displayLength": 50,
                                    "drawCallback": function (settings) {
                                        var api = this.api();
                                        var rows = api.rows({page: 'current'}).nodes();
                                        var last = null;

                                        api.column(groupColumn, {page: 'current'}).data().each(function (group, i) {
                                            if (last !== group) {
                                                $(rows).eq(i).before(
                                                    '<tr class="group"><td colspan="100%">' + group + '</td></tr>'
                                                );

                                                last = group;
                                            }
                                        });
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
