<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Sistema de Gestión de Docencia')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            min-height: 100vh;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .auth-card {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .auth-header {
            background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .auth-header img {
            height: 60px;
            margin-bottom: 15px;
        }
        
        .auth-header h4 {
            margin: 0 0 5px 0;
            font-weight: 700;
        }
        
        .auth-header p {
            margin: 0;
            opacity: 0.8;
            font-size: 14px;
        }
        
        .auth-body {
            padding: 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }
        
        .input-group {
            display: flex;
            margin-bottom: 20px;
        }
        
        .input-group-text {
            padding: 12px 15px;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #6b7280;
        }
        
        .form-control {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #d1d5db;
            border-left: none;
            border-radius: 0 8px 8px 0;
            font-size: 15px;
            outline: none;
        }
        
        .form-control:focus {
            border-color: #0B5E2E;
            box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.1);
        }
        
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0B5E2E 0%, #094525 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 94, 46, 0.3);
        }
        
        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.1);
            border-radius: 8px;
        }
        
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #0B5E2E;
        }
        
        .invalid-feedback {
            display: block;
            color: #dc2626;
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 15px;
            animation: shake 0.5s ease;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
        
        .auth-footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        
        .auth-footer a {
            color: #0B5E2E;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            color: #094525;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>