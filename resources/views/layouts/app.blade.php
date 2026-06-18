<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="E-Commerce Intelligence Dashboard — Analitik dataset Olist Brazil dengan segmentasi K-Means dan Market Basket Analysis.">
    <title>Olist Intelligence Dashboard</title>

    <!-- Google Fonts: Inter + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.0/dist/apexcharts.min.js"></script>

    <!-- Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <!-- Animated background mesh -->
    <div class="bg-mesh"></div>

    <!-- Subtle grid overlay -->
    <div class="grid-overlay"></div>

    <div class="app-layout">

        <!-- ===================== SIDEBAR ===================== -->
        <aside id="sidebar" class="glass-sidebar sidebar-container">

            <!-- Brand -->
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <svg style="width:20px; height:20px; color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="sidebar-brand-label">E-Commerce</p>
                    <p class="sidebar-brand-title">Intelligence</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="sidebar-nav">

                <p class="sidebar-section-label">Analytics</p>

                <a href="#kpi-section" class="sidebar-link active">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Overview KPI</span>
                </a>

                <a href="#revenue-section" class="sidebar-link">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    <span>Revenue Trend</span>
                </a>

                <a href="#segment-section" class="sidebar-link">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Segmen Pelanggan</span>
                </a>

                <a href="#mba-section" class="sidebar-link">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Market Basket</span>
                </a>

                <div class="sidebar-divider"></div>

                <p class="sidebar-section-label">Data Mining</p>

                <button id="run-mining-btn" class="sidebar-link sidebar-btn" type="button">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Jalankan Mining</span>
                </button>

                <button id="guide-mining-btn" class="sidebar-link sidebar-btn" type="button">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Panduan Mining</span>
                </button>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="sidebar-footer-content">
                    <div class="status-dot status-dot-live"></div>
                    <div>
                        <p class="sidebar-footer-title">Dataset Olist</p>
                        <p class="sidebar-footer-sub">Brazil · 2016–2018</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Sidebar overlay (mobile) -->
        <div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

        <!-- ===================== MAIN CONTENT ===================== -->
        <div class="main-wrapper">

            <!-- Top Header -->
            <header class="glass-header top-header">
                <!-- Mobile menu toggle -->
                <button onclick="toggleSidebar()" class="mobile-menu-btn" aria-label="Toggle sidebar">
                    <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- Page title (desktop) -->
                <div class="header-title-block">
                    <div class="header-title-accent"></div>
                    <div>
                        <h1 class="header-title">Dashboard Overview</h1>
                        <p class="header-subtitle">E-Commerce Brasil · Dataset Olist</p>
                    </div>
                </div>

                <!-- Right side -->
                <div class="header-right">
                    <!-- Live clock -->
                    <div class="header-clock">
                        <svg style="width:12px; height:12px; color:#334155; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="live-clock" class="clock-text"></span>
                    </div>

                    <!-- Dataset badge -->
                    <span class="dataset-badge">
                        <span class="dataset-badge-dot"></span>
                        Olist 2016–2018
                    </span>
                </div>
            </header>

            <!-- Page content -->
            <main class="main-content">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="app-footer">
                <p class="dash-footer-text">
                    E-Commerce Intelligence Dashboard
                </p>
                <p class="dash-footer-text">
                    Laravel · Python (K-Means &amp; Apriori) · ApexCharts
                </p>
            </footer>
        </div>
    </div>

    <!-- ===================== DATA MINING GUIDE MODAL ===================== -->
    <div id="mining-guide-modal" class="modal-overlay" style="display:none;">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon">
                        <svg style="width:20px; height:20px; color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="modal-title">Panduan Data Mining</h3>
                        <p class="modal-subtitle">Cara menjalankan proses K-Means & Apriori</p>
                    </div>
                </div>
                <button id="close-guide-modal" class="modal-close-btn" aria-label="Close">
                    <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <!-- Method 1 -->
                <div class="guide-card">
                    <div class="guide-card-header">
                        <span class="guide-step-badge">1</span>
                        <h4 class="guide-card-title">Via Dashboard (Recommended)</h4>
                    </div>
                    <p class="guide-card-desc">
                        Klik tombol <strong style="color:#c4b5fd;">"Jalankan Mining"</strong> di sidebar menu bagian <em>Data Mining</em>.
                        Proses akan berjalan otomatis di background dan halaman akan dimuat ulang setelah selesai.
                    </p>
                    <div class="guide-badge-row">
                        <span class="guide-badge guide-badge-green">
                            <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Tidak perlu terminal
                        </span>
                        <span class="guide-badge guide-badge-blue">
                            <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Auto refresh
                        </span>
                    </div>
                </div>

                <!-- Method 2 -->
                <div class="guide-card">
                    <div class="guide-card-header">
                        <span class="guide-step-badge">2</span>
                        <h4 class="guide-card-title">Via Terminal (Artisan CLI)</h4>
                    </div>
                    <p class="guide-card-desc">Buka terminal di folder project dan jalankan:</p>
                    <code class="guide-code-block">php artisan data-mining:run</code>
                    <p class="guide-card-desc" style="margin-top:0.75rem;">
                        Command ini menjalankan Python script yang melakukan:
                    </p>
                    <div class="guide-steps-list">
                        <div class="guide-step-item">
                            <span class="guide-step-number">①</span>
                            <span>Membaca data orders, order_items, customers dari database</span>
                        </div>
                        <div class="guide-step-item">
                            <span class="guide-step-number">②</span>
                            <span><strong>K-Means RFM</strong> — Menghitung Recency, Frequency, Monetary dan clustering pelanggan ke 4 segmen</span>
                        </div>
                        <div class="guide-step-item">
                            <span class="guide-step-number">③</span>
                            <span><strong>Apriori MBA</strong> — Menemukan pola belanja dan menghasilkan aturan asosiasi produk</span>
                        </div>
                        <div class="guide-step-item">
                            <span class="guide-step-number">④</span>
                            <span>Hasil disimpan ke tabel <code style="color:#a78bfa; background:rgba(139,92,246,0.1); padding:0.1rem 0.4rem; border-radius:0.25rem; font-size:0.7rem;">customer_segments</code> dan <code style="color:#a78bfa; background:rgba(139,92,246,0.1); padding:0.1rem 0.4rem; border-radius:0.25rem; font-size:0.7rem;">product_recommendations</code></span>
                        </div>
                    </div>
                </div>

                <!-- Method 3 -->
                <div class="guide-card">
                    <div class="guide-card-header">
                        <span class="guide-step-badge">3</span>
                        <h4 class="guide-card-title">Via Python Langsung</h4>
                    </div>
                    <p class="guide-card-desc">Jalankan script Python secara langsung:</p>
                    <code class="guide-code-block">python app/DataMining/data_mining_engine.py</code>
                </div>

                <!-- Prerequisites -->
                <div class="guide-card guide-card-warning">
                    <div class="guide-card-header">
                        <svg style="width:18px;height:18px; color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h4 class="guide-card-title" style="color:#fbbf24;">Prasyarat</h4>
                    </div>
                    <div class="guide-steps-list">
                        <div class="guide-step-item">
                            <span class="guide-step-number" style="background:rgba(245,158,11,0.15); color:#fbbf24;">✓</span>
                            <span>Python 3.8+ terinstall (<code style="color:#fbbf24; font-size:0.7rem;">python --version</code>)</span>
                        </div>
                        <div class="guide-step-item">
                            <span class="guide-step-number" style="background:rgba(245,158,11,0.15); color:#fbbf24;">✓</span>
                            <span>Install dependensi: <code class="guide-code-block" style="margin:0.375rem 0 0;">pip install -r requirements.txt</code></span>
                        </div>
                        <div class="guide-step-item">
                            <span class="guide-step-number" style="background:rgba(245,158,11,0.15); color:#fbbf24;">✓</span>
                            <span>Database MySQL sudah di-seed dengan data Olist</span>
                        </div>
                        <div class="guide-step-item">
                            <span class="guide-step-number" style="background:rgba(245,158,11,0.15); color:#fbbf24;">✓</span>
                            <span>File <code style="color:#fbbf24; font-size:0.7rem;">.env</code> sudah dikonfigurasi (DB_HOST, DB_PORT, dll)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== DATA MINING PROGRESS MODAL ===================== -->
    <div id="mining-progress-modal" class="modal-overlay" style="display:none;">
        <div class="modal-container modal-container-sm">
            <div class="modal-body" style="text-align:center; padding:2.5rem 2rem;">
                <div class="mining-spinner"></div>
                <h3 class="modal-title" style="margin-top:1.5rem;">Menjalankan Data Mining…</h3>
                <p class="modal-subtitle" style="margin-top:0.5rem;">
                    Proses K-Means clustering & Apriori MBA sedang berjalan.<br>
                    Mohon tunggu, ini dapat memakan waktu beberapa menit.
                </p>
                <div class="mining-progress-steps" id="mining-steps">
                    <div class="mining-step active" id="step-load">
                        <div class="mining-step-dot"></div>
                        <span>Memuat data dari database…</span>
                    </div>
                    <div class="mining-step" id="step-kmeans">
                        <div class="mining-step-dot"></div>
                        <span>K-Means RFM Segmentation…</span>
                    </div>
                    <div class="mining-step" id="step-apriori">
                        <div class="mining-step-dot"></div>
                        <span>Apriori Market Basket Analysis…</span>
                    </div>
                    <div class="mining-step" id="step-save">
                        <div class="mining-step-dot"></div>
                        <span>Menyimpan hasil…</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Live clock
        function updateClock() {
            const el = document.getElementById('live-clock');
            if (el) {
                el.textContent = new Date().toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            }
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('sidebar-open');
            overlay.classList.toggle('overlay-visible');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('sidebar-open');
            document.getElementById('sidebar-overlay').classList.remove('overlay-visible');
        }

        // Sidebar active link on click
        document.querySelectorAll('.sidebar-link[href^="#"]').forEach(link => {
            link.addEventListener('click', function () {
                document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                // Close sidebar on mobile after clicking
                if (window.innerWidth < 1024) closeSidebar();
            });
        });

        // Sidebar active link on scroll (IntersectionObserver)
        const sectionMap = {
            'kpi-section':     document.querySelector('a[href="#kpi-section"]'),
            'revenue-section': document.querySelector('a[href="#revenue-section"]'),
            'segment-section': document.querySelector('a[href="#segment-section"]'),
            'mba-section':     document.querySelector('a[href="#mba-section"]'),
        };

        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && sectionMap[entry.target.id]) {
                    document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                    sectionMap[entry.target.id].classList.add('active');
                }
            });
        }, { threshold: 0.4 });

        document.querySelectorAll('section[id]').forEach(s => sectionObserver.observe(s));

        // ===================== DATA MINING GUIDE MODAL =====================
        const guideModal = document.getElementById('mining-guide-modal');
        const guideBtn = document.getElementById('guide-mining-btn');
        const closeGuideBtn = document.getElementById('close-guide-modal');

        guideBtn?.addEventListener('click', () => {
            guideModal.style.display = 'flex';
            setTimeout(() => guideModal.classList.add('modal-visible'), 10);
        });

        closeGuideBtn?.addEventListener('click', closeGuide);
        guideModal?.addEventListener('click', (e) => {
            if (e.target === guideModal) closeGuide();
        });

        function closeGuide() {
            guideModal.classList.remove('modal-visible');
            setTimeout(() => { guideModal.style.display = 'none'; }, 300);
        }

        // ===================== RUN DATA MINING =====================
        const runBtn = document.getElementById('run-mining-btn');
        const progressModal = document.getElementById('mining-progress-modal');

        runBtn?.addEventListener('click', async () => {
            // Show progress modal
            progressModal.style.display = 'flex';
            setTimeout(() => progressModal.classList.add('modal-visible'), 10);

            // Animate steps
            const steps = ['step-load', 'step-kmeans', 'step-apriori', 'step-save'];
            let currentStep = 0;
            const stepInterval = setInterval(() => {
                if (currentStep < steps.length) {
                    document.getElementById(steps[currentStep])?.classList.add('active');
                    currentStep++;
                }
            }, 3000);

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch('/data-mining/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (response.status === 429) {
                    showToast('⚠️ ' + data.message, 'error');
                    clearInterval(stepInterval);
                    closeProgress();
                    return;
                }

                if (data.success) {
                    showToast('✅ ' + data.message, 'success');
                    
                    // Start polling status
                    const pollInterval = setInterval(async () => {
                        try {
                            const statRes = await fetch('/data-mining/status');
                            const statData = await statRes.json();
                            
                            if (statData.status === 'completed') {
                                clearInterval(pollInterval);
                                clearInterval(stepInterval);
                                steps.forEach(s => document.getElementById(s)?.classList.add('active', 'done'));
                                showToast('✅ Data mining selesai! Memuat ulang…', 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else if (statData.status === 'failed') {
                                clearInterval(pollInterval);
                                clearInterval(stepInterval);
                                showToast('❌ Data mining gagal. Periksa log.', 'error');
                                closeProgress();
                            }
                        } catch (err) {
                            console.error('Polling error', err);
                        }
                    }, 5000);

                } else {
                    clearInterval(stepInterval);
                    showToast('❌ ' + (data.message || 'Data mining gagal'), 'error');
                    closeProgress();
                }
            } catch (err) {
                clearInterval(stepInterval);
                showToast('❌ Koneksi error: ' + err.message, 'error');
                closeProgress();
            }
        });

        function closeProgress() {
            progressModal.classList.remove('modal-visible');
            setTimeout(() => {
                progressModal.style.display = 'none';
                // Reset steps
                document.querySelectorAll('.mining-step').forEach(s => s.classList.remove('active', 'done'));
            }, 300);
        }

        function showToast(message, type = 'success') {
            document.querySelectorAll('.toast').forEach(t => t.remove());
            const toast = document.createElement('div');
            toast.className = 'toast' + (type === 'error' ? ' toast-error' : '');
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(8px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>

    @stack('scripts')
</body>
</html>
