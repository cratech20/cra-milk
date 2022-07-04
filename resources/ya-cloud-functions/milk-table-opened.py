# coding=utf-8
## Cloud Function. milk-table-opened - Раскрытые сообщения
import os
import logging
import psycopg2
import psycopg2.errors
from datetime import datetime as dt
import json
import base64

logger = logging.getLogger()
logger.setLevel(logging.INFO)

verboseLogging = eval(os.environ['VERBOSE_LOG']) ## Convert to bool

if  verboseLogging:
    logger.info('Loading msgHandler function')

def getConnString():
    """
    Extract env variables to connect to DB and return a db string
    Raise an error if the env variables are not set
    :return: string
    """
    db_hostname = os.environ['DB_HOSTNAME']
    db_port =  os.environ['DB_PORT']
    db_name = os.environ['DB_NAME']
    db_user = os.environ['DB_USER']
    db_password = os.environ['DB_PASSWORD']
    db_connection_string = f"host='{db_hostname}' port='{db_port}'  dbname='{db_name}' user='{db_user}' password='{db_password}'  sslmode='require'"
    return db_connection_string

def td(string):
    return f"<td>{str(string)}</td>"

def nco(arr, key, default = ""):
    try:
        return arr[key]
    except Exception:
        return default

def msgHandler(event = None, context = None):
    statusCode = 500 ## Error response by default

    connection_string = getConnString()
    
    if  verboseLogging:
        logger.info(f'Connecting: {connection_string}')

    conn = psycopg2.connect(connection_string)

    cursor = conn.cursor()

    sql = "select * from iot_events where true order by event_datetime DESC limit 100;"

    if  verboseLogging:     
        logger.info(f'Exec: {sql}')

    try:
        cursor.execute(sql)

        records = cursor.fetchall()

        htmlBody = """
        <!DOCTYPE html>
            <html>
            <head>
            <meta charset="utf-8">
            <title>Список из БД</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
            <style type="text/css">
                tr.group,
                tr.group:hover {
                    background-color: #ddd !important;
                }
            </style>
            </head>
            <body>
            <div class="container-fluid">
        """

        htmlBody += f"Всего строк: {str(cursor.rowcount)}"

        htmlBody += '<table id="example" class="table table-striped table-bordered">'

        htmlBody += """
        <thead>
            <tr>     
                <th>Устройство</th>
                <th>Корова</th>
                <th>Время сервер</th>
                <th>Время девайс</th>
                <th>Литры</th>
                <th>Импульсы</th>
                <th>Вес</th>
                <th>Батарея</th>
                <th>№ сообщения</th>
                <th>Ошибка</th>
                <th>MAC-Адрес</th>
                <th>Прошивка</th>
                <th>Сообщение</th>
            </tr>
        </thead>
        <tbody>
        """

        for row in records:
            device_time = dt.fromtimestamp(int(nco(row[3], "t", 0))).strftime('%Y-%m-%d %H:%M:%S')
            tr = ""
            tr += td(nco(row[3], "p"))
            tr += td(nco(row[3], "c"))
            tr += td(row[2])
            tr += td(device_time)
            tr += td(nco(row[3], "y"))
            tr += td(nco(row[3], "i"))
            tr += td(nco(row[3], "h"))
            tr += td(nco(row[3], "b"))
            tr += td(nco(row[3], "n"))
            tr += td(nco(row[3], "e"))
            tr += td(nco(row[3], "a"))
            tr += td(nco(row[3], "v"))
            tr += td(row[3])
            htmlBody += f"<tr>{tr}</tr>"

        htmlBody += "</tbody></table></div>"

        htmlBody += """
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"
                                type="text/javascript"></script>
                        <script type="text/javascript">
                            $(document).ready(function () {

                                var groupColumn = 0;

                                var table = $('#example').DataTable({
                                    "columnDefs": [
                                        {"visible": false, "targets": groupColumn}
                                    ],
                                    "order": [[groupColumn, 'asc']],
                                    "displayLength": 300,
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

                                // Order by the grouping
                                $('#example tbody').on('click', 'tr.group', function () {
                                    var currentOrder = table.order()[0];
                                    if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
                                        table.order([groupColumn, 'desc']).draw();
                                    } else {
                                        table.order([groupColumn, 'asc']).draw();
                                    }
                                });
                            });
                        </script>
        """

        statusCode = 200
    except psycopg2.errors.UndefinedTable as error: ## table not exist - create and repeate insert
        conn.rollback()
        logger.error( error)
    except Exception as error:
        logger.error( error)
    conn.commit() # <- We MUST commit to reflect the inserted data
    cursor.close()
    conn.close()

    return {
        'statusCode': statusCode,
        'headers': {
            'Content-Type': 'text/html'
        },
        'body': htmlBody,
        'isBase64Encoded': False
    }