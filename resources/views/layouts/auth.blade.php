<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Freight Diary | Intelligent Logistics Platform</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite('resources/css/app.css')
    <style>
        body {
            background-color: #0a1f13;
            background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        @yield('content')
    </div>

</body>
</html>