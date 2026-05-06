<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laryn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #0d47a1;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #0d47a1;
            box-shadow: 0 0 0 0.2rem rgba(13, 71, 161, 0.15);
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1565c0, #1976d2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 71, 161, 0.3);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer a {
            color: #0d47a1;
            text-decoration: none;
            font-size: 14px;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .form-footer p {
            margin: 10px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        .alert {
            border-radius: 5px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
        }

        .remember-me {
            margin-top: 15px;
        }

        .remember-me input[type="checkbox"] {
            margin-right: 8px;
        }

        .remember-me label {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-utensils"></i> Laryn</h1>
            <p>Sistem Inventory UMKM</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" value="1">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="form-footer">
                <a href="{{ route('password.request') }}">Lupa password?</a>
                <p>Belum punya akun? Silakan hubungi admin untuk akses akun.</p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
