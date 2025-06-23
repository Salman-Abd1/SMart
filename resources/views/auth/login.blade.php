<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SMart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>

        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px;
        }
        .login-container {
            width: 100%; max-width: 420px; background: white; border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 100%);
            color: white; padding: 30px; text-align: center;
        }
        .logo { font-size: 32px; margin-bottom: 10px; }
        .login-title { font-size: 24px; font-weight: 600; margin-bottom: 5px; }
        .login-subtitle { font-size: 14px; opacity: 0.9; }
        .login-form { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        .input-group { position: relative; margin-bottom: 15px; }
        .input-group i {
            position: absolute; left: 15px; top: 50%;
            transform: translateY(-50%); color: #7a7a7a;
        }
        .form-control {
            width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #e0e0e0;
            border-radius: 8px; font-size: 15px; transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #4A90E2; box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2); outline: none;
        }
        .form-label {
            display: block; margin-bottom: 8px; font-weight: 500; color: #555; font-size: 14px;
        }
        .remember-forgot {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 20px; font-size: 14px;
        }
        .remember-me {
            display: flex; align-items: center; gap: 8px;
        }
        .btn {
            width: 100%; padding: 14px; border: none; border-radius: 8px;
            font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 100%);
            color: white; box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
        }
        .btn-primary:active { transform: translateY(0); }
        .login-footer {
            text-align: center; padding: 20px; border-top: 1px solid #f0f0f0;
            font-size: 14px; color: #666;
        }
        .alert {
            padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-danger {
            background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;
        }
        @media (max-width: 480px) {
            .login-container { border-radius: 12px; }
            .login-header, .login-form { padding: 25px 20px; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-container { animation: fadeIn 0.5s ease-out; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo"><i class="fas fa-boxes"></i></div>
            <h1 class="login-title">StockMaster</h1>
            <p class="login-subtitle">Sistem Manajemen Stok Barang</p>
        </div>

        <div class="login-form">
            @if(session('status'))
                <div class="alert alert-danger">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">Email atau Password salah.</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
        </div>

        <div class="login-footer">
            Hubungi admin untuk mendapatkan akses
        </div>
    </div>
</body>
</html>
