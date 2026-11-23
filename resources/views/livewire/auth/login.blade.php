<x-layouts.auth.kiln title="Masuk">
    <div>
        <h1 class="auth-title">Masuk</h1>
        <p class="auth-sub">Gunakan email dan kata sandi akun Anda untuk melihat kondisi area tanam.</p>
    </div>

    @if (session('status'))
        <p class="auth-status" role="status">{{ session('status') }}</p>
    @endif

    <form class="form" method="POST" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}"
                   required autofocus autocomplete="email" placeholder="nama@email.com"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')
                <p class="field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <div class="field-top">
                <label for="password">Kata sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Lupa kata sandi?</a>
                @endif
            </div>
            <div class="input-row">
                <input class="input" id="password" name="password" type="password"
                       required autocomplete="current-password" placeholder="Kata sandi"
                       @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                <button class="toggle-pw" type="button" data-toggle-password="password" aria-pressed="false">Tampilkan</button>
            </div>
            @error('password')
                <p class="field-error" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <label class="check">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            Ingat perangkat ini
        </label>

        <button class="btn btn-accent" type="submit" data-test="login-button">Masuk</button>
    </form>

    @if (Route::has('register'))
        <p class="auth-switch">Belum memiliki akun? <a href="{{ route('register') }}">Daftar</a></p>
    @endif
</x-layouts.auth.kiln>
