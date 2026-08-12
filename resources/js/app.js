import * as bootstrap from 'bootstrap';
import Chart from 'chart.js/auto';

window.bootstrap = bootstrap;

// 儀表板圖表：其他頁面沒有這些畫布，直接跳過
const trendCanvas = document.getElementById('revenueTrendChart');
if (trendCanvas) {
    new Chart(trendCanvas, {
        type: 'line',
        data: {
            labels: JSON.parse(trendCanvas.dataset.labels),
            datasets: [{
                label: trendCanvas.dataset.label,
                data: JSON.parse(trendCanvas.dataset.values),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, .12)',
                borderWidth: 2,
                pointRadius: 2,
                pointHitRadius: 12,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.3,
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, ticks: { callback: (value) => '$' + Number(value).toLocaleString() } },
                x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
            },
        },
    });
}

const statusCanvas = document.getElementById('statusChart');
if (statusCanvas) {
    new Chart(statusCanvas, {
        type: 'bar',
        data: {
            labels: JSON.parse(statusCanvas.dataset.labels),
            datasets: [{
                data: JSON.parse(statusCanvas.dataset.values),
                backgroundColor: JSON.parse(statusCanvas.dataset.colors),
                borderRadius: 4,
                maxBarThickness: 44,
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { grid: { display: false } },
            },
        },
    });
}

// 輸入密碼時，大貓咪舉手遮住眼睛
document.querySelectorAll('input[type="password"]').forEach((input) => {
    input.addEventListener('focus', () => document.body.classList.add('cat-hiding'));
    input.addEventListener('blur', () => document.body.classList.remove('cat-hiding'));
});

// 彩蛋：貓咪的瞳孔跟著滑鼠移動
document.addEventListener('mousemove', (event) => {
    document.querySelectorAll('.cat-eye').forEach((eye) => {
        const pupil = eye.querySelector('.cat-pupil');
        const rect = eye.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        const angle = Math.atan2(event.clientY - centerY, event.clientX - centerX);
        const distance = Math.min(3, Math.hypot(event.clientX - centerX, event.clientY - centerY) / 40);

        pupil.style.transform = `translate(${Math.cos(angle) * distance}px, ${Math.sin(angle) * distance}px)`;
    });
});
