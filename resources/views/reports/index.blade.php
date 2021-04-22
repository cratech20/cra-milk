@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Отчеты</div>
                    <div class="card-body">
                        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                        <script type="text/javascript">
                            google.charts.load('current', {'packages': ['corechart']});
                            google.charts.setOnLoadCallback(drawChart);

                            function drawChart() {
                                // var data = google.visualization.arrayToDataTable([
                                //     ['Director (Year)', 'Rotten Tomatoes', 'IMDB', '44'],
                                //     ['Alfred Hitchcock (1935)', 8.4, 7.9, 0],
                                //     ['Ralph Thomas (1959)', 6.9, 6.5, 1],
                                //     ['Don Sharp (1978)', 6.5, 6.4, 2],
                                //     ['James Hawes (2008)', 4.4, 6.2, 3]
                                // ]);

                                var json = '{!! $data !!}';

                                var data = google.visualization.arrayToDataTable(JSON.parse(json));

                                var options = {
                                    title: 'Литров всего за месяц',
                                    vAxis: {title: 'Литров в день'},
                                    isStacked: true
                                };

                                var chart = new google.visualization.SteppedAreaChart(document.getElementById('chart_div'));

                                chart.draw(data, options);
                            }
                        </script>

                        <div id="chart_div" style="width: 900px; height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
