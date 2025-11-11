import Chart from 'chart.js/auto';

const dashboardEl = document.getElementById('dashboard-data');

// Parse JSON from data attributes
const dashboardData = {
    months: JSON.parse(dashboardEl.dataset.months),
    monthlyCollections: JSON.parse(dashboardEl.dataset.monthlyCollections),
    monthlyExpenses: JSON.parse(dashboardEl.dataset.monthlyExpenses),
    categoryNames: JSON.parse(dashboardEl.dataset.categoryNames),
    categoryValues: JSON.parse(dashboardEl.dataset.categoryValues),
};

document.addEventListener("DOMContentLoaded", () => {
    const trendCtx = document.getElementById('trendChart');
    const catCtx = document.getElementById('categoryChart');

    if (trendCtx && dashboardData) { // use dashboardData, not window.dashboardData
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: dashboardData.months,
                datasets: [
                    {
                        label: 'Collections',
                        data: dashboardData.monthlyCollections,
                        borderColor: '#198754',
                        borderWidth: 2,
                        tension: 0.4,
                    },
                    {
                        label: 'Expenses',
                        data: dashboardData.monthlyExpenses,
                        borderColor: '#dc3545',
                        borderWidth: 2,
                        tension: 0.4,
                    },
                ]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    if (catCtx && dashboardData) { // use dashboardData
        new Chart(catCtx, {
            type: 'pie',
            data: {
                labels: dashboardData.categoryNames,
                datasets: [{
                    data: dashboardData.categoryValues,
                    backgroundColor: [
                        '#0d6efd', '#198754', '#ffc107', '#dc3545',
                        '#6f42c1', '#20c997', '#fd7e14', '#0dcaf0'
                    ],
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
