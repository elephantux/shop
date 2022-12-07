@extends('layouts.auth')

@section('title', 'Восстановление пароля')

@section('content')
    <x-forms.auth title="Восстановление пароля" action="{{ route('forgot.handle') }}" method="POST">

        <x-forms.text-input type="email" name="email" value="{{ old('email') }}" placeholder="Эл. почта" required :is-error="$errors->has('email')"/>
        @error('email')
        <x-forms.error>{{ $message }}</x-forms.error>
        @enderror

        <x-forms.primary-button>Отправить</x-forms.primary-button>

        <x-slot:links>
            <div class="space-y-3 mt-5">
                <div class="text-xxs md:text-xs">
                    <a href="{{ route('login') }}" class="text-white hover:text-white/70 font-bold">Вход</a>
                </div>
                <div class="text-xxs md:text-xs">
                    <a href="{{ route('signup') }}" class="text-white hover:text-white/70 font-bold">Регистрация</a>
                </div>
            </div>
        </x-slot:links>

    </x-forms.auth>
@endsection
