@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <section class="grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase text-slate-500">Revenue</p>
            <p class="mt-4 text-4xl font-semibold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500">Total revenue from completed orders</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase text-slate-500">Orders</p>
            <p class="mt-4 text-4xl font-semibold text-slate-900">{{ number_format($totalOrders, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500">Completed orders count</p>
        </article>

        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase text-slate-500">Active Customers</p>
            <p class="mt-4 text-4xl font-semibold text-slate-900">{{ number_format($activeCustomers, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500">Unique customers with purchases</p>
        </article>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.5fr,1fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Monthly Revenue Trend</h2>
                    <p class="text-sm text-slate-500">Revenue evolution for completed orders</p>
                </div>
            </div>
            <div id="revenue-chart" class="h-[360px]"></div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-slate-900">Customer Segments</h2>
                <p class="text-sm text-slate-500">Distribution from K-Means segment labels</p>
            </div>
            <div id="segment-chart" class="h-[360px]"></div>
        </div>
    </section>

    <section class="grid gap-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Product Recommendations</h2>
                    <p class="text-sm text-slate-500">Top 10 products with recommended co-purchase items</p>
                </div>
                <input id="searchInput" type="search" placeholder="Search product id..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100 lg:w-80" />
            </div>
            <div class="overflow-hidden rounded-3xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-900">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Product ID</th>
                            <th class="px-6 py-4 font-semibold">Recommended Products</th>
                            <th class="px-6 py-4 font-semibold">Confidence</th>
                        </tr>
                    </thead>
                    <tbody id="recommendationTable" class="divide-y divide-slate-200 bg-white">
                        @foreach($recommendations as $recommendation)
                            <tr class="group hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $recommendation->product_id }}</td>
                                <td class="px-6 py-4">{{ $recommendation->recommended_products }}</td>
                                <td class="px-6 py-4">{{ number_format($recommendation->max_confidence, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
    const revenueData = @json($monthlyRevenue->pluck('revenue'));
    const revenueLabels = @json($monthlyRevenue->pluck('month'));
    const segmentLabels = @json($segmentDistribution->pluck('segment_label'));
    const segmentValues = @json($segmentDistribution->pluck('total'));

    document.addEventListener('DOMContentLoaded', function () {
        const revenueChart = new ApexCharts(document.querySelector('#revenue-chart'), {
            chart: { type: 'area', height: 360, toolbar: { show: false }, zoom: { enabled: false } },
            series: [{ name: 'Revenue', data: revenueData }],
            xaxis: { categories: revenueLabels, labels: { rotate: -45 } },
            yaxis: { labels: { formatter: value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value) } },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100, 100] } },
            tooltip: { y: { formatter: value => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value) } }
        });

        const segmentChart = new ApexCharts(document.querySelector('#segment-chart'), {
            chart: { type: 'donut', height: 360 },
            series: segmentValues,
            labels: segmentLabels,
            legend: { position: 'bottom' },
            responsive: [{ breakpoint: 768, options: { chart: { width: '100%' }, legend: { position: 'bottom' } } }]
        });

        revenueChart.render();
        segmentChart.render();

        const searchInput = document.querySelector('#searchInput');
        const rows = Array.from(document.querySelectorAll('#recommendationTable tr'));

        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            rows.forEach(row => {
                const cellText = row.textContent.toLowerCase();
                row.style.display = cellText.includes(term) ? '' : 'none';
            });
        });
    });
</script>
@endsection
