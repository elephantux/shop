@extends('layouts.app')

@section('content')
    @auth
        <h2>{{ auth()->user()->name }}</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Выйти</button>
        </form>
    @endauth
@endsection
