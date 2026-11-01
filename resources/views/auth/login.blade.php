@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card narrow">
    <h1>Masuk</h1>
    <p class="muted">Aplikasi pelaporan &amp; pemantauan indikasi kebakaran hutan dan lahan.</p>

    <form method="POST" action="{{ route('login.attempt') }}" class="form">
        @csrf
        <label>
            Username
            <input type="text" name="username" value="{{ old('username') }}" required autofocus>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <label class="inline">
            <input type="checkbox" name="remember" value="1"> Ingat saya
        </label>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
@endsection