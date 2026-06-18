@extends('layouts.app')

@section('content')

{{-- ============================================================ --}}
{{-- SECTION 1 · KPI Cards                                         --}}
{{-- ============================================================ --}}
<section id="kpi-section" style="scroll-margin-top: 5rem;">

    <!-- Section Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
        <div>
            <h2 style="font-size:1.125rem; font-weight:800; color:#f1f5f9; letter-spacing:-0.02em;">Key Performance Indicators</h2>
            <p style="font-size:0.75rem; color:#64748b; margin-top:0.2rem;">
                Agregat dari semua pesanan <strong style="color:#94a3b8;">terkirim</strong> dalam dataset
            </p>
        </div>
        <div style="display:flex; align-items:center; gap:0.5rem; background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:0.625rem; padding:0.375rem 0.875rem;">
            <span style="width:6px; height:6px; border-radius:50%; background:#34d399; box-shadow: 0 0 8px rgba(52,211,153,0.5);"></span>
            <span style="font-size:0.7rem; font-weight:600; color:#34d399;">Data Live</span>
        </div>
    </div>

    <!-- KPI Grid -->
    <div style="display:grid; gap:1rem; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">

        {{-- Revenue --}}
        <article class="kpi-card fade-up fade-up-1" style="--accent: 139, 92, 246;">
            <div class="kpi-glow" style="background: radial-gradient(ellipse at top left, rgba(139,92,246,0.12) 0%, transparent 65%);"></div>
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-badge stat-badge-up" style="font-size:0.65rem;">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Delivered
                </div>
            </div>
            <p class="kpi-label" style="margin-top:1.25rem;">Total Pendapatan</p>
            <p class="kpi-value counter" data-target="{{ (int) $totalRevenue }}" style="margin-top:0.375rem;">Rp 0</p>
            <p class="kpi-sub">Dari pesanan yang selesai</p>
            <div class="mini-progress" style="margin-top:0.875rem;">
                <div class="mini-progress-fill" style="width:78%; background: linear-gradient(90deg, #7c3aed, #4f46e5);"></div>
            </div>
        </article>

        {{-- Orders --}}
        <article class="kpi-card fade-up fade-up-2" style="--accent: 59, 130, 246;">
            <div class="kpi-glow" style="background: radial-gradient(ellipse at top left, rgba(59,130,246,0.12) 0%, transparent 65%);"></div>
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="stat-badge stat-badge-up" style="font-size:0.65rem;">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Aktif
                </div>
            </div>
            <p class="kpi-label" style="margin-top:1.25rem;">Total Pesanan</p>
            <p class="kpi-value counter" data-target="{{ $totalOrders }}" data-format="number" style="margin-top:0.375rem;">0</p>
            <p class="kpi-sub">Pesanan terkirim</p>
            <div class="mini-progress" style="margin-top:0.875rem;">
                <div class="mini-progress-fill" style="width:92%; background: linear-gradient(90deg, #2563eb, #0891b2);"></div>
            </div>
        </article>

        {{-- Active Customers --}}
        @php
            $maxPossible = max($totalOrders, 1);
            $customerPct = min(100, round($activeCustomers / $maxPossible * 100));
            $avgPct = min(100, round($avgOrderValue / 3000 * 100));
        @endphp
        <article class="kpi-card fade-up fade-up-3" style="--accent: 16, 185, 129;">
            <div class="kpi-glow" style="background: radial-gradient(ellipse at top left, rgba(16,185,129,0.12) 0%, transparent 65%);"></div>
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #059669 0%, #0d9488 100%);">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="stat-badge stat-badge-up" style="font-size:0.65rem;">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Unik
                </div>
            </div>
            <p class="kpi-label" style="margin-top:1.25rem;">Pelanggan Aktif</p>
            <p class="kpi-value counter" data-target="{{ $activeCustomers }}" data-format="number" style="margin-top:0.375rem;">0</p>
            <p class="kpi-sub">Pembeli unik terverifikasi</p>
            <div class="mini-progress" style="margin-top:0.875rem;">
                <div class="mini-progress-fill" style="width:{{ $customerPct }}%; background: linear-gradient(90deg, #059669, #0d9488);"></div>
            </div>
        </article>

        {{-- Average Order Value --}}
        <article class="kpi-card fade-up fade-up-4" style="--accent: 245, 158, 11;">
            <div class="kpi-glow" style="background: radial-gradient(ellipse at top left, rgba(245,158,11,0.12) 0%, transparent 65%);"></div>
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #d97706 0%, #ea580c 100%);">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="stat-badge stat-badge-up" style="font-size:0.65rem;">
                    <svg style="width:10px;height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Per order
                </div>
            </div>
            <p class="kpi-label" style="margin-top:1.25rem;">Rata-rata Nilai Pesanan</p>
            <p class="kpi-value counter" data-target="{{ (int) $avgOrderValue }}" style="margin-top:0.375rem;">Rp 0</p>
            <p class="kpi-sub">Pendapatan per pesanan</p>
            <div class="mini-progress" style="margin-top:0.875rem;">
                <div class="mini-progress-fill" style="width:{{ min(100, $avgPct) }}%; background: linear-gradient(90deg, #d97706, #ea580c);"></div>
            </div>
        </article>

    </div>
</section>

{{-- ============================================================ --}}
{{-- SECTION 2 · Revenue Trend + Customer Segments                 --}}
{{-- ============================================================ --}}
<section style="display:flex; flex-direction:column; gap:1.25rem;" id="charts-section">

    <!-- Revenue Area Chart -->
    <div id="revenue-section" class="dash-card fade-up" style="scroll-margin-top: 5rem;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
            <div>
                <h2 class="section-title">Tren Pendapatan Bulanan</h2>
                <p class="section-subtitle">Grafik area pendapatan pesanan selesai dari waktu ke waktu</p>
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem; border-radius:0.625rem; border:1px solid rgba(124,58,237,0.25); background:rgba(124,58,237,0.1); padding:0.3rem 0.875rem;">
                <svg style="width:12px; height:12px; color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span style="font-size:0.7rem; font-weight:600; color:#a78bfa;">Revenue Trend</span>
            </div>
        </div>
        <div id="revenue-chart" style="height:18rem; width:100%;"></div>
    </div>

    <!-- Bottom row: Segments + Quick Stats — 2 cols on large screens -->
    <div style="display:grid; gap:1.25rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">

        {{-- Customer Segments Donut --}}
        <div id="segment-section" class="dash-card fade-up" style="scroll-margin-top: 5rem;">
            <div style="margin-bottom:1.25rem;">
                <h2 class="section-title">Segmen Pelanggan</h2>
                <p class="section-subtitle">Clustering K-Means RFM · 4 kluster</p>
            </div>
            <div id="segment-chart" style="height:18rem; width:100%;"></div>

            @if($segmentDistribution->isEmpty())
                <div style="margin-top:1rem; border-radius:0.875rem; border:1px solid rgba(245,158,11,0.2); background:rgba(245,158,11,0.08); padding:1rem; text-align:center;">
                    <p style="font-size:0.75rem; color:#fbbf24; font-weight:500; margin-bottom:0.375rem;">Belum ada data segmen</p>
                    <code class="cmd-block" style="margin:0 auto; display:inline-flex;">php artisan data-mining:run</code>
                </div>
            @else
                @php
                    $segColors = [
                        'Champions'   => '#f59e0b',
                        'Loyal'       => '#6366f1',
                        'At Risk'     => '#f97316',
                        'Hibernating' => '#ef4444'
                    ];
                    $totalSeg = $segmentDistribution->sum('total');
                @endphp
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.625rem; margin-top:1rem;">
                    @foreach($segmentDistribution as $seg)
                        @php $color = $segColors[$seg->segment_label] ?? '#94a3b8'; @endphp
                        <div class="legend-item">
                            <span class="legend-dot" style="background:{{ $color }};"></span>
                            <div style="min-width:0;">
                                <p style="font-size:0.75rem; font-weight:600; color:#cbd5e1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $seg->segment_label }}</p>
                                <p style="font-size:0.65rem; color:#64748b; margin-top:0.1rem;">
                                    {{ number_format($seg->total) }} · {{ $totalSeg > 0 ? round($seg->total / $totalSeg * 100, 1) : 0 }}%
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Quick Stats Panel --}}
        <div class="dash-card fade-up">
            <div style="margin-bottom:1.25rem;">
                <h2 class="section-title">Statistik Cepat</h2>
                <p class="section-subtitle">Ringkasan metrik utama dataset</p>
            </div>

            <div style="display:flex; flex-direction:column; gap:0.875rem;">

                <!-- Stat row: Revenue / order -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.875rem; background:rgba(124,58,237,0.06); border:1px solid rgba(124,58,237,0.12); border-radius:0.875rem;">
                    <div style="display:flex; align-items:center; gap:0.625rem;">
                        <div style="width:32px; height:32px; border-radius:0.625rem; background:rgba(124,58,237,0.15); display:flex; align-items:center; justify-content:center;">
                            <svg style="width:15px; height:15px; color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:600; color:#94a3b8;">AOV</p>
                            <p style="font-size:0.65rem; color:#64748b;">Avg. Order Value</p>
                        </div>
                    </div>
                    <p style="font-size:0.9rem; font-weight:700; color:#c4b5fd; font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($avgOrderValue, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Stat row: Total Orders -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.875rem; background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.12); border-radius:0.875rem;">
                    <div style="display:flex; align-items:center; gap:0.625rem;">
                        <div style="width:32px; height:32px; border-radius:0.625rem; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center;">
                            <svg style="width:15px; height:15px; color:#60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:600; color:#94a3b8;">Total Orders</p>
                            <p style="font-size:0.65rem; color:#64748b;">Pesanan terkirim</p>
                        </div>
                    </div>
                    <p style="font-size:0.9rem; font-weight:700; color:#93c5fd; font-variant-numeric:tabular-nums;">
                        {{ number_format($totalOrders) }}
                    </p>
                </div>

                <!-- Stat row: Customers -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.875rem; background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.12); border-radius:0.875rem;">
                    <div style="display:flex; align-items:center; gap:0.625rem;">
                        <div style="width:32px; height:32px; border-radius:0.625rem; background:rgba(16,185,129,0.15); display:flex; align-items:center; justify-content:center;">
                            <svg style="width:15px; height:15px; color:#34d399;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:600; color:#94a3b8;">Active Customers</p>
                            <p style="font-size:0.65rem; color:#64748b;">Pembeli unik</p>
                        </div>
                    </div>
                    <p style="font-size:0.9rem; font-weight:700; color:#6ee7b7; font-variant-numeric:tabular-nums;">
                        {{ number_format($activeCustomers) }}
                    </p>
                </div>

                <!-- Stat row: Revenue total -->
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.875rem; background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.12); border-radius:0.875rem;">
                    <div style="display:flex; align-items:center; gap:0.625rem;">
                        <div style="width:32px; height:32px; border-radius:0.625rem; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center;">
                            <svg style="width:15px; height:15px; color:#fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <p style="font-size:0.7rem; font-weight:600; color:#94a3b8;">Total Revenue</p>
                            <p style="font-size:0.65rem; color:#64748b;">Semua transaksi selesai</p>
                        </div>
                    </div>
                    <p style="font-size:0.875rem; font-weight:700; color:#fcd34d; font-variant-numeric:tabular-nums;">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- ============================================================ --}}
{{-- SECTION 3 · Market Basket Analysis Table                      --}}
{{-- ============================================================ --}}
<section id="mba-section" class="dash-card fade-up" style="scroll-margin-top: 5rem;">

    <!-- Header -->
    <div style="display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.25rem;">
        <div>
            <h2 class="section-title">Market Basket Analysis</h2>
            <p class="section-subtitle">Top 10 produk · Aturan asosiasi Apriori (confidence ≥ 0.50)</p>
        </div>
        <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
            <!-- Algorithm badge -->
            <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.5rem; border:1px solid rgba(99,102,241,0.25); background:rgba(99,102,241,0.1); padding:0.3rem 0.75rem; font-size:0.7rem; font-weight:600; color:#a5b4fc;">
                <svg style="width:10px; height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Apriori Algorithm
            </span>
            <!-- Search -->
            <div style="position:relative; min-width:200px; flex:1; max-width:280px;">
                <svg style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); width:14px; height:14px; color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="mba-search"
                       type="search"
                       placeholder="Cari product ID…"
                       class="search-input" />
            </div>
        </div>
    </div>

    @if($recommendations->isEmpty())
        <div class="empty-state">
            <div style="width:56px; height:56px; border-radius:1rem; background:rgba(255,255,255,0.06); display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                <svg style="width:28px; height:28px; color:#475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="empty-title">Belum ada rekomendasi</p>
            <p class="empty-desc">Jalankan data mining untuk menghasilkan hasil MBA</p>
            <code class="cmd-block">php artisan data-mining:run</code>
        </div>
    @else
        <div style="border-radius:1rem; border:1px solid rgba(255,255,255,0.05); overflow:hidden;">
            <table class="dash-table" id="mba-table">
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>Product ID</th>
                        <th>Direkomendasikan Bersama</th>
                        <th>Confidence</th>
                        <th>Rules</th>
                    </tr>
                </thead>
                <tbody id="mba-tbody">
                    @foreach($recommendations as $i => $rec)
                        @php
                            $conf = (float) $rec->max_confidence;
                            $confClass = $conf >= 0.8 ? 'badge-conf-high' : ($conf >= 0.65 ? 'badge-conf-mid' : 'badge-conf-low');
                        @endphp
                        <tr class="table-row-hover">
                            <td>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600; font-variant-numeric:tabular-nums;">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <code class="product-id-cell">{{ Str::limit($rec->product_id, 14) }}</code>
                                    <button onclick="copyText('{{ $rec->product_id }}')"
                                            title="Salin ID lengkap"
                                            class="copy-btn"
                                            style="color:#334155;">
                                        <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; flex-wrap:wrap; gap:0.375rem;">
                                    @foreach(explode(', ', $rec->recommended_products) as $rp)
                                        <code class="rec-chip">{{ Str::limit(trim($rp), 14) }}</code>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="confidence-badge {{ $confClass }}">
                                    {{ number_format($conf * 100, 1) }}%
                                </span>
                            </td>
                            <td>
                                <span style="display:inline-flex; align-items:center; gap:0.375rem; border-radius:0.5rem; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.06); padding:0.25rem 0.625rem; font-size:0.7rem; color:#475569; font-weight:500;">
                                    <svg style="width:10px; height:10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    {{ $rec->rec_count }} rule{{ $rec->rec_count > 1 ? 's' : '' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p id="mba-no-results" class="hidden" style="margin-top:1rem; text-align:center; font-size:0.875rem; color:#64748b;">
            Tidak ada produk yang cocok dengan pencarian.
        </p>
    @endif

</section>

{{-- ============================================================ --}}
{{-- Inline JS — pass Blade data to dashboard.js                   --}}
{{-- ============================================================ --}}
@push('scripts')
<script>
    window.DASHBOARD_DATA = {
        revenue: {
            labels:  @json($monthlyRevenue->pluck('month')),
            values:  @json($monthlyRevenue->pluck('revenue')),
        },
        segments: {
            labels: @json($segmentDistribution->pluck('segment_label')),
            values: @json($segmentDistribution->pluck('total')),
        },
    };
</script>
@endpush

@endsection
