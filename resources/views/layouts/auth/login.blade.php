<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DailyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #e3f2fd; font-family: 'Poppins', sans-serif; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 450px; padding: 40px; background: white; }
        .icon-box { background-color: #00a8e8; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 24px; }
        .btn-login { background-color: #00a8e8; border: none; border-radius: 10px; padding: 12px; font-weight: bold; color: white; transition: 0.3s; }
        .btn-login:hover { background-color: #0087ba; transform: translateY(-2px); }
        .form-control, .form-select { border-radius: 10px; background-color: #f8f9fa; border: 1px solid #eee; padding: 12px 15px; }
        .divider { display: flex; align-items: center; text-align: center; color: #ccc; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #eee; }
        .divider span { padding: 0 10px; font-size: 12px; font-weight: bold; }

        /* Kustomisasi CSS agar kelengkungan input-group password menyatu sempurna */
        .input-group .form-control.border-start-0 { border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
        .input-group .btn-toggle-pass { border-top-right-radius: 10px !important; border-bottom-right-radius: 10px !important; border: 1px solid #eee; border-left: none; background-color: #f8f9fa; color: #6c757d; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="login-card">
        <div class="icon-box shadow-sm">
            <i class="bi bi-box-arrow-in-right"></i>
        </div>
        
        <h3 class="text-center fw-bold text-dark mb-1">Selamat Datang Kembali</h3>
        <p class="text-center text-muted small mb-4">Masuk ke akun Anda untuk melanjutkan</p>

        {{-- Menampilkan Alert Notifikasi --}}
        @if(session('error'))
            <div class="alert alert-danger py-2 small rounded-3">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success py-2 small rounded-3">{{ session('success') }}</div>
        @endif

        {{-- Alert Khusus jika validasi input pengetikan email error --}}
        @if($errors->any())
            <div class="alert alert-danger py-2 small rounded-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="mb-3">
                <label class="small fw-bold mb-1">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="nama@example.com" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="small fw-bold mb-1">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-lock text-primary"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="Masukkan password" required>
                    <button class="btn btn-toggle-pass" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya</label>
                </div>
                <a href="#" class="text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#modalLupaPassword">Lupa password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">Masuk</button>

            <div class="divider">
                <span>ATAU</span>
            </div>

            <p class="text-center small">Belum punya akun? <a href="/register" class="fw-bold text-decoration-none text-primary">Daftar sekarang</a></p>
        </form>
    </div>
</div>

{{-- MODAL LUPA PASSWORD --}}
<div class="modal fade" id="modalLupaPassword" tabindex="-1" aria-labelledby="modalLupaPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalLupaPasswordLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/lupa_password" method="POST">
                @csrf
                <div class="modal-body py-3">
                    <p class="text-muted small mb-3">Silakan masukkan email terdaftar Anda. Kami akan mengirimkan link khusus ke email tersebut untuk memperbarui password Anda.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Akun</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email yang terdaftar" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">Kirim Link Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePasswordButton.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'text') {
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });
    });
</script>

</body>
</html>