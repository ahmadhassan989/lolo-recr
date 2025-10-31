document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('recruiterChart');
    const dataset = window.recruiterPerformance || { labels: [], hires: [], conversions: [] };

    if (!ctx || dataset.labels.length === 0) {
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataset.labels,
            datasets: [
                {
                    label: 'Hired',
                    data: dataset.hires,
                    backgroundColor: '#1e293b',
                    borderRadius: 6,
                    barThickness: 'flex',
                    maxBarThickness: 40,
                },
                {
                    label: 'Conversion %',
                    data: dataset.conversions,
                    backgroundColor: '#16a34a',
                    borderRadius: 6,
                    barThickness: 'flex',
                    maxBarThickness: 40,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label(context) {
                            const label = context.dataset.label || '';
                            const value = context.parsed.y;
                            return label === 'Conversion %'
                                ? `${label}: ${value}%`
                                : `${label}: ${value}`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });
});
