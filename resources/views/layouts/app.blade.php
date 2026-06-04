<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olist Intelligence Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="min-h-screen bg-slate-100 text-slate-900">
        <header class="border-b border-slate-200 bg-white shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-sky-700">E-Commerce Intelligence</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-900">Olist BI Dashboard</h1>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
            @yield('content')
        </main>
    </div>
</body>
</html>
