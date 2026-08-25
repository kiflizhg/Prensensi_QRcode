<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir</title>
    <style>
        body { 
            font-family: system-ui, -apple-system, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: #f3f4f6; 
            margin: 0; 
        }
        .container { 
            text-align: center; 
            background: white; 
            padding: 2.5rem; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            max-width: 400px; 
            width: 90%; 
        }
        h1 { 
            color: #1f2937; 
            font-size: 1.5rem; 
            margin-bottom: 0.5rem; 
        }
        p { 
            color: #4b5563; 
            margin-bottom: 1.5rem; 
            line-height: 1.5;
        }
        .btn { 
            display: inline-block; 
            background-color: #3b82f6; 
            color: white; 
            padding: 0.75rem 1.5rem; 
            text-decoration: none; 
            border-radius: 6px; 
            transition: background-color 0.2s; 
            border: none; 
            font-size: 1rem; 
            font-weight: 500;
            cursor: pointer; 
        }
        .btn:hover { 
            background-color: #2563eb; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sesi Anda telah berakhir</h1>
        <p>Maaf, sesi Anda telah kadaluarsa karena tidak ada aktivitas untuk beberapa saat. Silakan login kembali untuk melanjutkan.</p>
        <button class="btn" onclick="window.location.href = '{{ route('login') }}';">Login Kembali</button>
    </div>
</body>
</html>
