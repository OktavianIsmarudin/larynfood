<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Laryn</title>
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

        .forgot-password-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .forgot-password-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .forgot-password-header h1 {
            color: #0d47a1;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .forgot-password-header p {
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

        .btn-submit {
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

        .btn-submit:hover {
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

        .alert {
            border-radius: 5px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .alert ul {
            margin: 10px 0 0 0;
            padding-left: 20px;
        }

        .alert li {
            font-size: 13px;
        }

        .info-text {
            background-color: #e3f2fd;
            border-left: 4px solid #0d47a1;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #0d47a1;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-header">
            <h1><i class="fas fa-utensils"></i> Laryn</h1>
            <p>Reset Password</p>
        </div>

        <div class="info-text">
            <i class="fas fa-info-circle"></i> Masukkan email dan password baru Anda untuk mereset akun.
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Error:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('password.reset') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-submit">
                <i class="fas fa-key"></i> Reset Password
            </button>

            <div class="form-footer">
                <a href="{{ route('login') }}">Kembali ke login</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
