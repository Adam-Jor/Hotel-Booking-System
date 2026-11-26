import './bootstrap';
import Chart from 'chart.js/auto';

window.Chart = Chart;

const initRevenueChart = () => {
    const canvas = document.getElementById('revenueChart');
    if (!canvas || !window.Chart) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const idr = new Intl.NumberFormat('id-ID');
    const compact = (value) => value >= 1e9 ? (value / 1e9).toFixed(1) + ' M'
        : value >= 1e6 ? (value / 1e6).toFixed(1) + ' jt'
        : value >= 1e3 ? (value / 1e3).toFixed(0) + ' rb'
        : value;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Revenue',
                data: values,
                backgroundColor: '#0194F3',
                hoverBackgroundColor: '#0183d9',
                borderRadius: 6,
                maxBarThickness: 48,
            }],
        },
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
