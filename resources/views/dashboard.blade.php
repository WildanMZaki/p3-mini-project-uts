@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1>Halo, {{ $user->name }}</h1>
<p class="muted">Anda masuk sebagai <strong>{{ $user->role->label() }}</strong>.</p>
@endsection