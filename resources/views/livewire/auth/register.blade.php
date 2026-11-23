<x-layouts.auth.kiln title="Daftar">
    <div>
        <h1 class="auth-title">Daftar</h1>
        <p class="auth-sub">Buat akun untuk memantau dan menyiram area tanam anggrek secara otomatis.</p>
    </div>

    @if (session('status'))
        <p class="auth-status" role="status">{{ session('status') }}</p>
    @endif

    <form class="form" method="POST" action="{{ route('register.store') }}" novalidate>
        @csrf

        <div class="field">
            <label for="name">Nama lengkap</label>
            <input class="input" id="name" name="name" type="text" value="{{ old('name') }}"
                   required autofocus autocomplete="name" placeholder="Nama lengkap"
                   @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
            @error('name')
                <p class="field-error" id="name-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input class="input" id="email" name="email" type="email" value="{{ old('email') }}"
                   required autocomplete="email" placeholder="nama@email.com"
                   @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email')
                <p class="field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">Kata sandi</label>
            <div class="input-row">
                <input class="input" id="password" name="password" type="password"
                       required autocomplete="new-password" placeholder="Kata sandi"
                       aria-describedby="password-hint"
                       @error('password') aria-invalid="true" aria-describedby="password-hint password-error" @enderror>
                <button class="toggle-pw" type="button" data-toggle-password="password" aria-pressed="false">Tampilkan</button>
            </div>
            <p class="hint" id="password-hint">Sekurang-kurangnya 8 karakter.</p>
            @error('password')
                <p class="field-error" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Ulangi kata sandi</label>
            <div class="input-row">
                <input class="input" id="password_confirmation" name="password_confirmation" type="password"
                       required autocomplete="new-password" placeholder="Ulangi kata sandi">
                <button class="toggle-pw" type="button" data-toggle-password="password_confirmation" aria-pressed="false">Tampilkan</button>
            </div>
        </div>

        <button class="btn btn-accent" type="submit" data-test="register-user-button">Daftar</button>
    </form>

    <p class="auth-switch">Sudah memiliki akun? <a href="{{ route('login') }}">Masuk</a></p>
</x-layouts.auth.kiln>
