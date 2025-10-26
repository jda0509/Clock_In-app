@extends('layouts.staff')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/list.css') }}">
@endsection

@section('content')

<div class="staff-list_main">
    <h2 class="page_title">スタッフ一覧</h2>
    <table class="staff-list_table">
        <thead>
            <tr>
                <th class="title">名前</th>
                <th class="title">メールアドレス</th>
                <th class="title">月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staffs as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <a href="">
                        詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection