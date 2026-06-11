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
        foreColor: '#64748b',
    },
    grid: {
        borderColor: 'rgba(255,255,255,0.05)',
        strokeDashArray: 4,
    },
};

/* ================================================================
   1. REVENUE AREA CHART
   ================================================================ */
function initRevenueChart(labels, values) {
    const el = document.querySelector('#revenue-chart');
    if (!el) return;

    const chart = new ApexCharts(el, {
        ...CHART_DEFAULTS,
        chart: {
            ...CHART_DEFAULTS.chart,
            type: 'area',
            height: 288,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 80 },
                dynamicAnimation: { enabled: true, speed: 300 },
            },
        },
        series: [{ name: 'Revenue', data: values }],
        xaxis: {
            categories: labels,
            tickAmount: 8,
            labels: {
                rotate: -30,
                rotateAlways: false,
                style: { fontSize: '11px', colors: '#475569' },
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px', colors: '#475569' },
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
        stroke: { curve: 'smooth', width: 2.5, colors: ['#7c3aed'] },
        fill: {
            type: 'gradient',
            gradient: {
                type: 'vertical',
                shadeIntensity: 1,
                colorStops: [
                    { offset: 0,   color: '#7c3aed', opacity: 0.45 },
                    { offset: 60,  color: '#4f46e5', opacity: 0.15 },
                    { offset: 100, color: '#0a0d14', opacity: 0 },
                ],
            },
        },
        markers: {
            size: 0,
            hover: { size: 5, sizeOffset: 3 },
            colors: ['#7c3aed'],
            strokeColors: '#1e2130',
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
        grid: { ...CHART_DEFAULTS.grid, padding: { left: 10, right: 10 } },
    });

    chart.render();
}

/* ================================================================
   2. CUSTOMER SEGMENT DONUT CHART
   ================================================================ */
function initSegmentChart(labels, values) {
    const el = document.querySelector('#segment-chart');
    if (!el || !labels.length) return;

    const colors = labels.map((l) => SEGMENT_COLORS[l] ?? '#94a3b8');

    const chart = new ApexCharts(el, {
        ...CHART_DEFAULTS,
        chart: {
            ...CHART_DEFAULTS.chart,
            type: 'donut',
            height: 288,
            animations: { enabled: true, easing: 'easeinout', speed: 700 },
        },
        series: values,
        labels: labels,
        colors: colors,
        dataLabels: {
            enabled: true,
            formatter: (val) => val.toFixed(1) + '%',
            style: { fontSize: '11px', fontWeight: '600', colors: ['#fff'] },
            dropShadow: { blur: 4, opacity: 0.3 },
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '13px',
                            fontWeight: 600,
                            color: '#94a3b8',
                            formatter: (w) =>
                                new Intl.NumberFormat('id-ID').format(
                                    w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                ),
                        },
                        value: {
                            fontSize: '22px',
                            fontWeight: 700,
                            color: '#e2e8f0',
                            formatter: (val) => new Intl.NumberFormat('id-ID').format(val),
                        },
                    },
                },
            },
        },
        legend: { show: false },   // Using custom HTML legend in Blade
        stroke: { width: 0 },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: (val) =>
                    new Intl.NumberFormat('id-ID').format(val) + ' customers',
            },
        },
    });

    chart.render();
}

/* ================================================================
   3. KPI COUNTER ANIMATION
   ================================================================ */
function animateCounter(el) {
    const target  = parseInt(el.dataset.target, 10) || 0;
    const format  = el.dataset.format || 'currency';
    const duration = 1800;
    const startTime = performance.now();

    function formatValue(val) {
        if (format === 'number') {
            return new Intl.NumberFormat('id-ID').format(Math.round(val));
        }
        // Default: IDR currency
        return 'Rp\u00a0' + new Intl.NumberFormat('id-ID').format(Math.round(val));
    }

    function step(currentTime) {
        const elapsed  = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease-out cubic
        const eased    = 1 - Math.pow(1 - progress, 3);
        el.textContent = formatValue(target * eased);
        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
}

function initCounters() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.3 }
    );

    document.querySelectorAll('.counter').forEach((el) => observer.observe(el));
}

/* ================================================================
   4. MBA TABLE SEARCH
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

/* ================================================================
   5. COPY TO CLIPBOARD
   ================================================================ */
window.copyText = function (text) {
    navigator.clipboard.writeText(text).then(() => {
        // Brief visual feedback — create toast
        const toast = document.createElement('div');
        toast.textContent = 'Copied!';
        toast.className = [
            'fixed bottom-6 right-6 z-50 rounded-xl px-4 py-2 text-sm font-medium text-white',
            'bg-violet-600 shadow-lg shadow-violet-500/30',
            'transition-all duration-300',
        ].join(' ');
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
};

/* ================================================================
   6. INITIALISE EVERYTHING
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const data = window.DASHBOARD_DATA || {};

    // Charts
    initRevenueChart(
        data.revenue?.labels ?? [],
        data.revenue?.values ?? [],
    );
    initSegmentChart(
        data.segments?.labels ?? [],
        data.segments?.values ?? [],
    );

    // UX
    initCounters();
    initTableSearch();

    // Fade-up animation for KPI cards
    document.querySelectorAll('.kpi-card').forEach((card, i) => {
        card.style.animationDelay = `${i * 0.06}s`;
        card.classList.add('fade-up');
    });
});
