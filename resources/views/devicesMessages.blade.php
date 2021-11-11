<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Messages</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
    <table class="table">
        <thead>
            <th>Код коровы</th>
            <th>Номер (5ти значный)</th>
            <th>Внутренний номер</th>
            <th>Дата/время</th>
            <th>Значение</th>
            <th>Время с начала дойки</th>
        </thead>
        <tbody>
            @foreach ($messages as $message)
                <tr>
                    <td>{{ $message['c'] }}</td>
                    <td>{{ $message['code'] }}</td>
                    <td>{{ $message['internalId'] }}</td>
                    <td>{{ $message['dt'] }}</td>
                    <td>{{ $message['li'] }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- {{ $messages->links() }} --}}
</body>
</html>
