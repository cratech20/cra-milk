<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td>{{$item->id}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->email}}</td>
        </tr>
    @endforeach
    </tbody>
</table>