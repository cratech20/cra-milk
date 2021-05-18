<table>
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