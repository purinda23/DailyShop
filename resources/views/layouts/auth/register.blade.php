<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - DailyShop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #e3f2fd; font-family: 'Poppins', sans-serif; height: 100vh; display: flex; align-items: center; }
        .register-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 450px; padding: 40px; background: white; }
        .icon-box { background-color: #00a8e8; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 24px; }
        .btn-register { background-color: #00a8e8; border: none; border-radius: 10px; padding: 12px; font-weight: bold; color: white; transition: 0.3s; }
        .btn-register:hover { background-color: #0087ba; transform: translateY(-2px); }
        .form-control { border-radius: 10px; background-color: #f8f9fa; border: 1px solid #eee; padding: 10px 15px; }
        
        /* Tambahan CSS khusus agar kelengkungan input-group tetap rapi */
        .input-group .form-control { border-top-right-radius: 0; border-bottom-right-radius: 0; }
        .input-group .btn { border-top-right-radius: 10px; border-bottom-right-radius: 10px; border: 1px solid #eee; border-left: none; background-color: #f8f9fa; color: #6c757d; }
    </style>
</head>

<body>
<div class="container d-flex justify-content-center">
    <div class="register-card">
        <div class="icon-box shadow-sm">
            <i class="bi bi-person-plus"></i>
        </div>
        
        <h3 class="text-center fw-bold text-dark mb-1">Daftar Akun</h3>
        <p class="text-center text-muted small mb-4">Buat akun untuk mulai berbelanja kebutuhan harian</p>

        <form action="/register" method="POST">
            @csrf
            <div class="mb-3">
                <label class="small fw-bold mb-1">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="mb-3">
                <label class="small fw-bold mb-1">Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@example.com" required>
            </div>

            <div class="mb-3">
                <label class="small fw-bold mb-1">Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Buat password minimal 6 karakter" required>
                    <button class="btn" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-register w-100 mb-3">Daftar Sekarang</button>

            <p class="text-center small">Sudah punya akun? <a href="/login" class="fw-bold text-decoration-none text-primary">Masuk di sini</a></p>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePasswordButton.addEventListener('click', function () {
            // Tukar tipe input antara 'password' dan 'text'
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Tukar ikon mata
            if (type === 'text') {
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye'); // Mata terbuka
            } else {
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash'); // Mata dicoret
            }
        });
    });
</script>
</body>
</html>