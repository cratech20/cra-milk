@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Отчеты</div>
                    <div class="card-body">
                        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
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
                                $('#example').DataTable();
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
