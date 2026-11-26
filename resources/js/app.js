import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

const initRevenueChart = () => {
    const canvas = document.getElementById('revenueChart');
    if (!canvas || !window.Chart) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const type = canvas.dataset.type === 'line' ? 'line' : 'bar';
    const existing = window.Chart.getChart(canvas);
    if (existing) {
        existing.destroy();
    }
    const idr = new Intl.NumberFormat('id-ID');
    const compact = (value) => value >= 1e9 ? (value / 1e9).toFixed(1) + ' M'
        : value >= 1e6 ? (value / 1e6).toFixed(1) + ' jt'
        : value >= 1e3 ? (value / 1e3).toFixed(0) + ' rb'
        : value;

    const dataset = type === 'line'
        ? {
            label: 'Revenue',
            data: values,
            borderColor: '#0194F3',
            backgroundColor: 'rgba(1, 148, 243, 0.12)',
            borderWidth: 2,
            fill: true,
            tension: 0.35,
            pointRadius: 2,
            pointHoverRadius: 4,
        }
        : {
            label: 'Revenue',
            data: values,
            backgroundColor: '#0194F3',
            hoverBackgroundColor: '#0183d9',
            borderRadius: 6,
            maxBarThickness: 48,
        };

    new Chart(canvas, {
        type,
        data: { labels, datasets: [dataset] },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => 'Rp ' + idr.format(ctx.parsed.y),
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#6b7280' } },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#6b7280', callback: (value) => compact(value) },
                    grid: { color: 'rgba(0,0,0,0.06)' },
                },
            },
        },
    });
};

document.addEventListener('DOMContentLoaded', initRevenueChart);
document.addEventListener('livewire:navigated', initRevenueChart);
