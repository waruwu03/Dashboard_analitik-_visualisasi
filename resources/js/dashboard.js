/**
 * dashboard.js — Modular dashboard initialisation
 * Expects window.DASHBOARD_DATA to be set by the Blade template.
 */

/* ================================================================
   CHART COLOUR PALETTES
   ================================================================ */
const SEGMENT_COLORS = {
    Champions:   '#f59e0b',
    Loyal:       '#6366f1',
    'At Risk':   '#f97316',
    Hibernating: '#ef4444',
};

const CHART_DEFAULTS = {
    chart: {
        background: 'transparent',
        fontFamily: "'Inter', ui-sans-serif, sans-serif",
        foreColor: '#475569',
    },
    grid: {
        borderColor: 'rgba(255,255,255,0.04)',
        strokeDashArray: 4,
    },
};

// Global Chart Instances
let revenueChartInstance = null;
let segmentChartInstance = null;
let categoriesChartInstance = null;
let leafletMapInstance = null;
let leafletHeatLayer = null;

/* ================================================================
   1. REVENUE AREA CHART
   ================================================================ */
function initRevenueChart(labels, values) {
    const el = document.querySelector('#revenue-chart');
    if (!el) return;

    if (revenueChartInstance) {
        revenueChartInstance.updateOptions({ xaxis: { categories: labels } });
        revenueChartInstance.updateSeries([{ data: values }]);
        return;
    }

    revenueChartInstance = new ApexCharts(el, {
        ...CHART_DEFAULTS,
        chart: {
            ...CHART_DEFAULTS.chart,
            type: 'area',
            height: 288,
            toolbar: { show: false },
            zoom: { enabled: false },
            dropShadow: {
                enabled: true,
                top: 8,
                left: 0,
                blur: 16,
                color: '#7c3aed',
                opacity: 0.15,
            },
        },
        series: [{ name: 'Pendapatan', data: values }],
        xaxis: {
            categories: labels,
            tickAmount: 8,
            labels: {
                rotate: -30,
                rotateAlways: false,
                style: { fontSize: '10px', colors: '#64748b', fontWeight: 500 },
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { fontSize: '10px', colors: '#64748b' },
                formatter: (val) =>
                    new Intl.NumberFormat('id-ID', {
                        notation: 'compact',
                        compactDisplay: 'short',
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 1,
                    }).format(val),
            },
        },
        stroke: {
            curve: 'smooth',
            width: 2.5,
            colors: ['#7c3aed'],
        },
        fill: {
            type: 'gradient',
            gradient: {
                type: 'vertical',
                shadeIntensity: 1,
                colorStops: [
                    { offset: 0,   color: '#7c3aed', opacity: 0.5 },
                    { offset: 50,  color: '#4f46e5', opacity: 0.15 },
                    { offset: 100, color: '#080b12', opacity: 0 },
                ],
            },
        },
        markers: {
            size: 0,
            hover: { size: 6, sizeOffset: 3 },
            colors: ['#7c3aed'],
            strokeColors: '#1a2035',
            strokeWidth: 2,
        },
        tooltip: {
            theme: 'dark',
            x: { format: 'yyyy-MM' },
            y: {
                formatter: (val) =>
                    new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        maximumFractionDigits: 0,
                    }).format(val),
            },
        },
        grid: {
            ...CHART_DEFAULTS.grid,
            padding: { left: 8, right: 8, top: 0, bottom: 0 },
        },
    });

    revenueChartInstance.render();
}

/* ================================================================
   2. CUSTOMER SEGMENT DONUT CHART
   ================================================================ */
function initSegmentChart(labels, values) {
    const el = document.querySelector('#segment-chart');
    if (!el || !labels.length) return;

    const colors = labels.map((l) => SEGMENT_COLORS[l] ?? '#94a3b8');

    if (segmentChartInstance) {
        segmentChartInstance.updateOptions({ labels: labels, colors: colors });
        segmentChartInstance.updateSeries(values);
        return;
    }

    segmentChartInstance = new ApexCharts(el, {
        ...CHART_DEFAULTS,
        chart: {
            ...CHART_DEFAULTS.chart,
            type: 'donut',
            height: 288,
        },
        series: values,
        labels: labels,
        colors: colors,
        dataLabels: {
            enabled: true,
            formatter: (val) => val.toFixed(1) + '%',
            style: {
                fontSize: '11px',
                fontWeight: '700',
                colors: ['rgba(255,255,255,0.85)'],
            },
            dropShadow: { blur: 4, opacity: 0.4 },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '12px',
                            fontWeight: 600,
                            color: '#475569',
                            formatter: (w) =>
                                new Intl.NumberFormat('id-ID').format(
                                    w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                ),
                        },
                        value: {
                            fontSize: '22px',
                            fontWeight: 800,
                            color: '#f1f5f9',
                            formatter: (val) => new Intl.NumberFormat('id-ID').format(val),
                        },
                    },
                },
            },
        },
        legend: { show: false },
        stroke: { width: 2, colors: ['rgba(8,11,18,0.9)'] },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: (val) =>
                    new Intl.NumberFormat('id-ID').format(val) + ' pelanggan',
            },
        },
    });

    segmentChartInstance.render();
}

/* ================================================================
   3. TOP PRODUCT CATEGORIES BAR CHART
   ================================================================ */
function initCategoriesChart(labels, values) {
    const el = document.querySelector('#categories-chart');
    if (!el) return;

    if (categoriesChartInstance) {
        categoriesChartInstance.updateOptions({ xaxis: { categories: labels } });
        categoriesChartInstance.updateSeries([{ data: values }]);
        return;
    }

    categoriesChartInstance = new ApexCharts(el, {
        ...CHART_DEFAULTS,
        chart: {
            ...CHART_DEFAULTS.chart,
            type: 'bar',
            height: 288,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4,
                barHeight: '60%',
                distributed: true,
            }
        },
        colors: ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#84cc16', '#6366f1', '#f97316'],
        series: [{ name: 'Produk Terjual', data: values }],
        xaxis: {
            categories: labels,
            labels: {
                style: { fontSize: '10px', colors: '#64748b' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#cbd5e1', fontWeight: 500 },
                formatter: (val) => {
                    if (!val) return val;
                    return val.length > 15 ? val.substring(0, 15) + '...' : val;
                }
            }
        },
        legend: { show: false },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: (val) => new Intl.NumberFormat('id-ID').format(val) + ' pcs'
            }
        },
        grid: {
            ...CHART_DEFAULTS.grid,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } },
        }
    });

    categoriesChartInstance.render();
}

/* ================================================================
   4. LEAFLET GEOMAP (HEATMAP)
   ================================================================ */
function initGeomap(geoData) {
    const el = document.getElementById('geomap');
    if (!el || !geoData || geoData.length === 0) return;

    // Default center to Brazil
    if (!leafletMapInstance) {
        leafletMapInstance = L.map('geomap', {
            center: [-14.2350, -51.9253],
            zoom: 4,
            zoomControl: false,
            attributionControl: false
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            subdomains: 'abcd',
            maxZoom: 10
        }).addTo(leafletMapInstance);
        
        L.control.zoom({ position: 'topright' }).addTo(leafletMapInstance);
    }

    if (leafletHeatLayer) {
        leafletMapInstance.removeLayer(leafletHeatLayer);
    }

    // Prepare heatmap points: [lat, lng, intensity]
    // Intensity is normalized based on weight
    const maxWeight = Math.max(...geoData.map(d => d.weight));
    const heatPoints = geoData.map(d => [
        parseFloat(d.lat), 
        parseFloat(d.lng), 
        (d.weight / maxWeight) * 1.5 // scale intensity
    ]);

    leafletHeatLayer = L.heatLayer(heatPoints, {
        radius: 12,
        blur: 15,
        maxZoom: 6,
        gradient: {
            0.2: 'blue', 
            0.4: 'cyan', 
            0.6: 'lime', 
            0.8: 'yellow', 
            1.0: 'red'
        }
    }).addTo(leafletMapInstance);
}

/* ================================================================
   5. DATE FILTER (AJAX)
   ================================================================ */
function initDateFilter() {
    const btn = document.getElementById('btn-apply-filter');
    const startInput = document.getElementById('filter-start-date');
    const endInput = document.getElementById('filter-end-date');

    if (!btn || !startInput || !endInput) return;

    btn.addEventListener('click', async () => {
        const start = startInput.value;
        const end = endInput.value;

        if (!start || !end) {
            window.copyText ? copyText('Pilih rentang tanggal terlebih dahulu') : alert('Pilih rentang tanggal');
            return;
        }

        btn.textContent = 'Memuat...';
        btn.disabled = true;

        try {
            const url = `/?start_date=${start}&end_date=${end}`;
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!res.ok) throw new Error('Failed to fetch data');
            const data = await res.json();

            // Update KPIs (requires classes and data-targets to be in HTML, which they are)
            document.querySelector('[data-target]').closest('.kpi-card').parentElement.querySelectorAll('.kpi-value').forEach((el, index) => {
                const values = [data.totalRevenue, data.totalOrders, data.activeCustomers, data.avgOrderValue];
                el.dataset.target = values[index];
                el.textContent = '0'; // reset for re-animation
            });
            initCounters(); // Re-trigger animation

            // Update Charts
            if(data.monthlyRevenue) initRevenueChart(data.monthlyRevenue.map(r => r.month), data.monthlyRevenue.map(r => r.revenue));
            if(data.topCategories) initCategoriesChart(data.topCategories.map(c => c.category), data.topCategories.map(c => c.total_sold));
            // Note: Geomap and segments usually don't need re-rendering for slight date changes, but we could.

        } catch (err) {
            console.error(err);
        } finally {
            btn.textContent = 'Filter';
            btn.disabled = false;
        }
    });
}

/* ================================================================
   6. EXPORT CSV (MBA TABLE)
   ================================================================ */
function initExportCSV() {
    const btn = document.getElementById('btn-export-csv');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const table = document.getElementById('mba-table');
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            for (let j = 0; j < cols.length; j++) {
                // Clean text to avoid extra spaces and line breaks
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                row.push('"' + data + '"');
            }
            csv.push(row.join(','));
        }

        const csvString = csv.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "Market_Basket_Analysis.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
}

/* ================================================================
   7. KPI COUNTER ANIMATION
   ================================================================ */
function animateCounter(el) {
    const target   = parseInt(el.dataset.target, 10) || 0;
    const format   = el.dataset.format || 'currency';
    const duration = 2000;
    const startTime = performance.now();

    function formatValue(val) {
        if (format === 'number') {
            return new Intl.NumberFormat('id-ID').format(Math.round(val));
        }
        return 'Rp\u00a0' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(val));
    }

    function step(currentTime) {
        const elapsed  = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 4);
        el.textContent = formatValue(target * eased);
        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}

function initCounters() {
    document.querySelectorAll('.counter').forEach((el) => {
        animateCounter(el);
    });
}

/* ================================================================
   8. MINI PROGRESS BAR ANIMATION
   ================================================================ */
function initProgressBars() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const fills = entry.target.querySelectorAll('.mini-progress-fill');
                    fills.forEach((fill) => {
                        const targetW = fill.style.width;
                        fill.style.width = '0%';
                        setTimeout(() => {
                            fill.style.width = targetW;
                        }, 100);
                    });
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.3 }
    );

    document.querySelectorAll('.kpi-card').forEach((card) => observer.observe(card));
}

/* ================================================================
   9. MBA TABLE SEARCH
   ================================================================ */
function initTableSearch() {
    const input    = document.getElementById('mba-search');
    const tbody    = document.getElementById('mba-tbody');
    const noResult = document.getElementById('mba-no-results');

    if (!input || !tbody) return;

    input.addEventListener('input', () => {
        const term = input.value.trim().toLowerCase();
        let visible = 0;

        Array.from(tbody.querySelectorAll('tr')).forEach((row) => {
            const match = row.textContent.toLowerCase().includes(term);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (noResult) {
            noResult.classList.toggle('hidden', visible > 0 || term === '');
        }
    });
}

window.copyText = function (text) {
    navigator.clipboard.writeText(text).then(() => {
        document.querySelectorAll('.toast').forEach(t => t.remove());
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> ID berhasil disalin!`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(8px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    });
};

/* ================================================================
   10. INITIALISE EVERYTHING
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const data = window.DASHBOARD_DATA || {};

    // Initialize Charts
    initRevenueChart(data.revenue?.labels ?? [], data.revenue?.values ?? []);
    initSegmentChart(data.segments?.labels ?? [], data.segments?.values ?? []);
    initCategoriesChart(data.topCategories?.labels ?? [], data.topCategories?.values ?? []);
    initGeomap(data.geomapData ?? []);

    // Initialize UX
    initCounters();
    initProgressBars();
    initTableSearch();
    initDateFilter();
    initExportCSV();

    // KPI card fade-up
    document.querySelectorAll('.kpi-card').forEach((card, i) => {
        card.style.animationDelay = `${i * 0.07}s`;
        card.classList.add('fade-up');
    });
});
