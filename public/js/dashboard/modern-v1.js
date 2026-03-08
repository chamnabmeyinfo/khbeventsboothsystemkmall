class AdvancedDashboardCharts {
    constructor(data) {
        this.data = data;
        this.init();
    }

    init() {
        this.renderBoothStatus();
        this.renderTrends();
        this.renderRevenue();
    }

    renderBoothStatus() {
        const ctx = document.getElementById('boothStatusChartAdvanced');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: this.data.boothStatus.labels,
                datasets: [{
                    data: this.data.boothStatus.values,
                    backgroundColor: [
                        '#10b981', // Available
                        '#f59e0b', // Reserved
                        '#3b82f6', // Confirmed
                        '#6366f1', // Paid
                        '#94a3b8'  // Hidden/Others
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 12,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12, weight: '600', family: 'Inter' }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: '700' },
                        bodyFont: { size: 13 }
                    }
                },
                cutout: '72%'
            }
        });
    }

    renderTrends() {
        const ctx = document.getElementById('bookingTrendsChartAdvanced');
        if (!ctx) return;

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.data.trends.dates,
                datasets: [{
                    label: 'New Bookings',
                    data: this.data.trends.counts,
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradient,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { font: { weight: '600' }, color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' }, color: '#64748b' }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    renderRevenue() {
        const ctx = document.getElementById('revenueTrendsChartAdvanced');
        if (!ctx) return;

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.data.trends.dates,
                datasets: [{
                    label: 'Revenue ($)',
                    data: this.data.trends.revenue,
                    borderColor: '#10b981',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradient,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { 
                            font: { weight: '600' }, 
                            color: '#64748b',
                            callback: (value) => '$' + value.toLocaleString()
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: '600' }, color: '#64748b' }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
}

window.AdvancedDashboardCharts = AdvancedDashboardCharts;
