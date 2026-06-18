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
            dropShadow: {
                enabled: true,
                top: 8,
                left: 0,
                blur: 16,
                color: '#7c3aed',
                opacity: 0.15,
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 900,
                animateGradually: { enabled: true, delay: 100 },
                dynamicAnimation: { enabled: true, speed: 350 },
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
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 80 },
            },
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

    chart.render();
}

/* ================================================================
   3. KPI COUNTER ANIMATION
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
        // Default: IDR currency
        return 'Rp\u00a0' + new Intl.NumberFormat('id-ID').format(Math.round(val));
    }

    function step(currentTime) {
        const elapsed  = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease-out quart
        const eased    = 1 - Math.pow(1 - progress, 4);
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
        { threshold: 0.25 }
    );

    document.querySelectorAll('.counter').forEach((el) => observer.observe(el));
}

/* ================================================================
   4. MINI PROGRESS BAR ANIMATION
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
   5. MBA TABLE SEARCH
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
   6. COPY TO CLIPBOARD
   ================================================================ */
window.copyText = function (text) {
    navigator.clipboard.writeText(text).then(() => {
        // Remove existing toasts
        document.querySelectorAll('.toast').forEach(t => t.remove());

        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            ID berhasil disalin!
        `;
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
   7. FADE-UP ANIMATION ON SCROLL
   ================================================================ */
function initFadeUp() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.08 }
    );

    document.querySelectorAll('.dash-card, section > div').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.5s cubic-bezier(0.16,1,0.3,1) ${i * 0.04}s, transform 0.5s cubic-bezier(0.16,1,0.3,1) ${i * 0.04}s`;
        observer.observe(el);
    });
}

/* ================================================================
   8. INITIALISE EVERYTHING
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
    initProgressBars();
    initTableSearch();

    // KPI card fade-up
    document.querySelectorAll('.kpi-card').forEach((card, i) => {
        card.style.animationDelay = `${i * 0.07}s`;
        card.classList.add('fade-up');
    });
});
