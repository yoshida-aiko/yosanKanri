@extends('layouts.app')

@section('content')
<table>
<thead>
    <th>ID</th>
    <th>VersionNo</th>
    <th>RegistUserId</th>
    <th>RegistDate</th>
    <th>LimitDate</th>
    <th>Title</th>
    <th>Contents</th>
</thead>
<tbody>
@foreach($BulletinBoards as $BulletinBoard)
<tr>
    <td>{{$BulletinBoard->id}}</td>
    <td>{{$BulletinBoard->VersionNo}}</td>
    <td>{{$BulletinBoard->user->name}}</td>
    <td>{{$BulletinBoard->RegistDate}}</td>
    <td>{{$BulletinBoard->LimitDate}}</td>
    <td>{{$BulletinBoard->Title}}</td>
    <td>{{$BulletinBoard->Contents}}</td>
</tr>
@endforeach
</tbody>

</table>

@endsection