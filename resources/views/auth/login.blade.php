<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stock Management - Login</title>
    <link rel="icon" href="{{ asset('img/Logo.png') }}" type="image/png">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.css') }}" rel="stylesheet">
    <link href="{{asset ('vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-header h1 {
            color: #4e73df;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .login-form .form-group {
            margin-bottom: 1.5rem;
        }
        .login-form label {
            font-weight: 600;
            color: #5a5c69;
        }
        .login-btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
            font-weight: 600;
            text-align: center;
            border-radius: 0.35rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .login-btn:hover {
            background-color: #2e59d9;
        }
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .text-danger {
            color: #e74a3b !important;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('img/Logoblu.png') }}" alt="Logo" style="width: 100px; margin-bottom: 1rem;">
            <h1>LOGIN</h1>
        </div>

        <form class="login-form" method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
</body>
</html>