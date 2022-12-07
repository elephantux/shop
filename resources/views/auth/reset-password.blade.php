@extends('layouts.auth')

@section('title', 'Восстановление пароля')

@section('content')
    <x-forms.auth title="Восстановление пароля" action="{{ route('password-reset.handle') }}" method="POST">
        <input type="hidden" name="token" value="{{ $token }}"/>

        <x-forms.text-input type="email" name="email" value="{{ old('email') }}" placeholder="Эл. почта" required :is-error="$errors->has('email')"/>
        @error('email')
        <x-forms.error>{{ $message }}</x-forms.error>
        @enderror

        <x-forms.text-input type="password" name="password" placeholder="Пароль" required :is-error="$errors->has('password')"/>
        @error('password')
        <x-forms.error>{{ $message }}</x-forms.error>
        @enderror

        <x-forms.text-input type="password" name="password_confirmation" placeholder="Повторите пароль" required :is-error="$errors->has('password_confirmation')"/>
        @error('password_confirmation')
        <x-forms.error>{{ $message }}</x-forms.error>
        @enderror

        <x-forms.primary-button>Сохранить</x-forms.primary-button>

    </x-forms.auth>
@endsection
