document.addEventListener('DOMContentLoaded', () => {
    const chartsData = window.dashboardChartsData || {};

    const candidatesConfig = chartsData.candidates || { labels: [], data: [] };
    const candidatesCtx = document.getElementById('candidatesChart');

    if (candidatesCtx && candidatesConfig.labels.length) {
        new Chart(candidatesCtx, {
            type: 'line',
            data: {
                labels: candidatesConfig.labels,
                datasets: [
                    {
                        label: 'New Candidates',
                        data: candidatesConfig.data,
                        borderColor: '#1e293b',
                        backgroundColor: 'rgba(30, 41, 59, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
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
    }

    const interviewConfig = chartsData.interviews || {};
    const interviewLabels = Object.keys(interviewConfig);
    const interviewValues = Object.values(interviewConfig);
    const interviewCtx = document.getElementById('interviewChart');

    if (interviewCtx && interviewLabels.length) {
        const palette = {
            pending: '#f59e0b',
            passed: '#22c55e',
            failed: '#ef4444',
        };

        new Chart(interviewCtx, {
            type: 'doughnut',
            data: {
                labels: interviewLabels.map((label) => label.charAt(0).toUpperCase() + label.slice(1)),
                datasets: [
                    {
                        data: interviewValues,
                        backgroundColor: interviewLabels.map((label) => palette[label] || '#6366f1'),
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    }
});
