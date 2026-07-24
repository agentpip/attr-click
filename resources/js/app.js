import { Chart, BarController, BarElement, CategoryScale, Legend, LinearScale, Tooltip } from 'chart.js';

Chart.register(BarController, BarElement, CategoryScale, Legend, LinearScale, Tooltip);

document.querySelectorAll('[data-link-analytics]').forEach(async (element) => {
    const response = await fetch(element.dataset.endpoint, { headers: { Accept: 'application/json' } });

    if (!response.ok) return;

    const data = await response.json();
    element.querySelector('[data-total-clicks]').textContent = data.total_clicks.toLocaleString();

    new Chart(element.querySelector('[data-referrer-chart]'), {
        type: 'bar',
        data: {
            labels: data.top_referrers.map((referrer) => referrer.host),
            datasets: [{
                label: 'Scans',
                data: data.top_referrers.map((referrer) => referrer.clicks),
                backgroundColor: '#22d3ee',
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#a1a1aa' }, grid: { display: false } },
                y: { beginAtZero: true, ticks: { color: '#a1a1aa', precision: 0 }, grid: { color: '#27272a' } },
            },
        },
    });
});

document.querySelectorAll('[data-admin-stats]').forEach((element) => {
    const refresh = async () => {
        const response = await fetch(element.dataset.endpoint, { headers: { Accept: 'application/json' } });

        if (!response.ok) return;

        const stats = await response.json();

        Object.entries(stats).forEach(([key, value]) => {
            const target = element.querySelector(`[data-stat="${key}"]`);

            if (target) target.textContent = Number(value).toLocaleString();
        });
    };

    window.setInterval(refresh, 30_000);
});
