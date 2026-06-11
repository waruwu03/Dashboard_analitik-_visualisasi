@extends('layouts.app')

@section('content')

{{-- ============================================================ --}}
{{-- SECTION 1 · KPI Cards                                         --}}
{{-- ============================================================ --}}
<section id="kpi-section" class="scroll-mt-20">

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-white">Key Performance Indicators</h2>
        <p class="mt-0.5 text-sm text-slate-400">Aggregated from all <strong class="text-slate-300">delivered</strong> orders in the dataset</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Revenue --}}
        <article class="kpi-card group" style="--accent: 139, 92, 246">
            <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #7c3aed, #4f46e5)">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-slate-400">Total Revenue</p>
            <p class="mt-1.5 text-3xl font-bold text-white counter" data-target="{{ (int) $totalRevenue }}">Rp 0</p>
            <p class="mt-2 text-xs text-slate-500">From completed orders</p>
            <div class="kpi-glow" style="background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 70%)"></div>
        </article>

        {{-- Orders --}}
        <article class="kpi-card group" style="--accent: 59, 130, 246">
            <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #2563eb, #0891b2)">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-slate-400">Total Orders</p>
            <p class="mt-1.5 text-3xl font-bold text-white counter" data-target="{{ $totalOrders }}" data-format="number">0</p>
            <p class="mt-2 text-xs text-slate-500">Delivered orders</p>
            <div class="kpi-glow" style="background: radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%)"></div>
        </article>

        {{-- Active Customers --}}
        <article class="kpi-card group" style="--accent: 16, 185, 129">
            <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #059669, #0d9488)">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-slate-400">Active Customers</p>
            <p class="mt-1.5 text-3xl font-bold text-white counter" data-target="{{ $activeCustomers }}" data-format="number">0</p>
            <p class="mt-2 text-xs text-slate-500">Unique buyers</p>
            <div class="kpi-glow" style="background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, transparent 70%)"></div>
        </article>

        {{-- Average Order Value --}}
        <article class="kpi-card group" style="--accent: 245, 158, 11">
            <div class="kpi-icon-wrap" style="background: linear-gradient(135deg, #d97706, #ea580c)">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="mt-4 text-xs font-semibold uppercase tracking-widest text-slate-400">Avg. Order Value</p>
            <p class="mt-1.5 text-3xl font-bold text-white counter" data-target="{{ (int) $avgOrderValue }}">Rp 0</p>
            <p class="mt-2 text-xs text-slate-500">Revenue per order</p>
            <div class="kpi-glow" style="background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%)"></div>
        </article>

    </div>
</section>

{{-- ============================================================ --}}
{{-- SECTION 2 · Revenue Trend + Customer Segments                 --}}
{{-- ============================================================ --}}
<section class="grid gap-6 xl:grid-cols-[1.6fr,1fr]">

    {{-- Revenue Area Chart --}}
    <div id="revenue-section" class="dash-card scroll-mt-20">
        <div class="mb-5 flex items-start justify-between">
            <div>
                <h2 class="text-base font-semibold text-white">Monthly Revenue Trend</h2>
                <p class="mt-0.5 text-xs text-slate-400">Area chart of completed order revenue over time</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-violet-500/20 bg-violet-500/10 px-2.5 py-1 text-xs font-medium text-violet-300">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Revenue
            </span>
        </div>
        <div id="revenue-chart" class="h-72 w-full"></div>
    </div>

    {{-- Customer Segments Donut --}}
    <div id="segment-section" class="dash-card scroll-mt-20">
        <div class="mb-5">
            <h2 class="text-base font-semibold text-white">Customer Segments</h2>
            <p class="mt-0.5 text-xs text-slate-400">K-Means RFM clustering · 4 clusters</p>
        </div>
        <div id="segment-chart" class="h-72 w-full"></div>

        @if($segmentDistribution->isEmpty())
            <div class="mt-4 rounded-xl border border-amber-500/20 bg-amber-500/10 p-3 text-center text-xs text-amber-400">
                No segment data yet. Run <code class="font-mono">php artisan data-mining:run</code>
            </div>
        @else
            <div class="mt-4 grid grid-cols-2 gap-2">
                @php
                    $segColors = ['Champions' => '#f59e0b', 'Loyal' => '#6366f1', 'At Risk' => '#f97316', 'Hibernating' => '#ef4444'];
                    $totalSeg  = $segmentDistribution->sum('total');
                @endphp
                @foreach($segmentDistribution as $seg)
                    @php $color = $segColors[$seg->segment_label] ?? '#94a3b8'; @endphp
                    <div class="flex items-center gap-2 rounded-lg bg-white/5 px-3 py-2">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-sm" style="background:{{ $color }}"></span>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-medium text-slate-300">{{ $seg->segment_label }}</p>
                            <p class="text-[10px] text-slate-500">{{ number_format($seg->total) }}
                                ({{ $totalSeg > 0 ? round($seg->total / $totalSeg * 100, 1) : 0 }}%)
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</section>

{{-- ============================================================ --}}
{{-- SECTION 3 · Market Basket Analysis Table                      --}}
{{-- ============================================================ --}}
<section id="mba-section" class="dash-card scroll-mt-20">

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-white">Market Basket Analysis</h2>
            <p class="mt-0.5 text-xs text-slate-400">Top 10 products · Apriori association rules (confidence ≥ 0.50)</p>
        </div>
        <!-- Search -->
        <div class="relative w-full sm:w-72">
            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input id="mba-search"
                   type="search"
                   placeholder="Search product ID…"
                   class="w-full rounded-xl border border-white/10 bg-white/5 py-2.5 pl-9 pr-4 text-sm text-slate-200 placeholder-slate-500 outline-none focus:border-violet-500/50 focus:bg-violet-500/5 focus:ring-2 focus:ring-violet-500/20 transition-all" />
        </div>
    </div>

    @if($recommendations->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/10 py-16 text-center">
            <svg class="mb-3 h-10 w-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="text-sm font-medium text-slate-400">No recommendations yet</p>
            <p class="mt-1 text-xs text-slate-600">Run <code class="font-mono">php artisan data-mining:run</code> to generate MBA results</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-white/5">
            <table class="min-w-full text-sm" id="mba-table">
                <thead>
                    <tr class="border-b border-white/5 bg-white/[0.03]">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Product ID</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Recommended With</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Confidence</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Rules</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5" id="mba-tbody">
                    @foreach($recommendations as $i => $rec)
                        @php
                            $conf = (float) $rec->max_confidence;
                            $confClass = $conf >= 0.8 ? 'badge-conf-high' : ($conf >= 0.65 ? 'badge-conf-mid' : 'badge-conf-low');
                        @endphp
                        <tr class="table-row-hover transition-colors duration-150">
                            <td class="px-5 py-4 text-xs text-slate-600">{{ $i + 1 }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <code class="product-id-cell rounded-md bg-white/5 px-2 py-1 text-xs font-mono text-violet-300 border border-violet-500/20">
                                        {{ Str::limit($rec->product_id, 12) }}
                                    </code>
                                    <button onclick="copyText('{{ $rec->product_id }}')"
                                            title="Copy full ID"
                                            class="copy-btn rounded p-1 text-slate-600 hover:text-violet-400 transition-colors">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach(explode(', ', $rec->recommended_products) as $rp)
                                        <code class="rounded-md bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 text-[11px] font-mono text-indigo-300">
                                            {{ Str::limit(trim($rp), 12) }}
                                        </code>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="confidence-badge {{ $confClass }}">
                                    {{ number_format($conf, 2) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-white/5 px-2 py-1 text-xs text-slate-400">
                                    {{ $rec->rec_count }} rule{{ $rec->rec_count > 1 ? 's' : '' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p id="mba-no-results" class="hidden mt-4 text-center text-sm text-slate-500">No products match your search.</p>
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
