<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="E-Commerce Intelligence Dashboard — Olist Brazilian dataset analytics with K-Means segmentation and Market Basket Analysis.">
    <title>Olist Intelligence Dashboard</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Animated gradient noise background -->
    <div class="fixed inset-0 -z-10 bg-dash-base">
        <div class="absolute inset-0 bg-gradient-to-br from-violet-950/30 via-dash-base to-indigo-950/20"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] rounded-full bg-violet-700/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] rounded-full bg-indigo-700/10 blur-3xl"></div>
    </div>

    <div class="flex min-h-screen">
        <!-- ===================== SIDEBAR ===================== -->
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-white/5 bg-dash-surface/80 backdrop-blur-xl transition-transform duration-300 lg:translate-x-0 -translate-x-full">

            <!-- Brand -->
            <div class="flex h-16 items-center gap-3 border-b border-white/5 px-5">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg shadow-violet-500/30">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-violet-400">E-Commerce</p>
                    <p class="text-sm font-bold text-white">Intelligence</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 space-y-1 overflow-y-auto p-3 pt-4">
                <p class="px-3 pb-2 pt-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Analytics</p>

                <a href="#kpi-section"
                   class="sidebar-link active group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all">
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Overview
                </a>

                <a href="#revenue-section"
                   class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all">
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    Revenue Trend
                </a>

                <a href="#segment-section"
                   class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all">
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Customer Segments
                </a>

                <a href="#mba-section"
                   class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all">
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Market Basket
                </a>

                <div class="mt-4 border-t border-white/5 pt-4">
                    <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-widest text-slate-500">System</p>
                    <a href="#" id="run-mining-btn"
                       class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all">
                        <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Run Data Mining
                    </a>
                </div>
            </nav>

            <!-- Footer -->
            <div class="border-t border-white/5 p-4">
                <div class="flex items-center gap-3">
                    <div class="h-2 w-2 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/50 animate-pulse"></div>
                    <p class="text-xs text-slate-400">Olist Dataset · Live</p>
                </div>
            </div>
        </aside>

        <!-- Sidebar overlay (mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-black/60 backdrop-blur-sm hidden lg:hidden" onclick="closeSidebar()"></div>

        <!-- ===================== MAIN CONTENT ===================== -->
        <div class="flex flex-1 flex-col lg:pl-64">

            <!-- Top Header -->
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-white/5 bg-dash-surface/80 px-4 backdrop-blur-xl sm:px-6">
                <!-- Mobile menu toggle -->
                <button onclick="toggleSidebar()" class="rounded-lg p-2 text-slate-400 hover:bg-white/5 hover:text-white lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="hidden lg:block">
                    <h1 class="text-sm font-semibold text-white">Dashboard Overview</h1>
                    <p class="text-xs text-slate-400">Brazilian E-Commerce · Olist Dataset</p>
                </div>

                <div class="flex items-center gap-4 ml-auto">
                    <!-- Live clock -->
                    <span id="live-clock" class="hidden text-xs font-mono text-slate-400 sm:block"></span>

                    <!-- Dataset badge -->
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-violet-500/30 bg-violet-500/10 px-3 py-1 text-xs font-medium text-violet-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
                        Olist 2016–2018
                    </span>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 space-y-8 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-white/5 px-6 py-4 text-center text-xs text-slate-600">
                E-Commerce Intelligence Dashboard · Built with Laravel + Python (K-Means &amp; Apriori)
            </footer>
        </div>
    </div>

    <script>
        // Live clock
        function updateClock() {
            const el = document.getElementById('live-clock');
            if (el) {
                el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.add('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }

        // Sidebar active link
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
