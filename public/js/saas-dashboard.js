/**
 * SaaS Super Admin Command Center Dashboard Logic
 * Modular external script for charts, tooltips, and tab interactions
 */
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap 5 Tooltips safely
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Revenue Trend Line Chart
    const revCanvas = document.getElementById('revenueChart');
    if (revCanvas && typeof Chart !== 'undefined') {
        const trendDataRaw = revCanvas.getAttribute('data-trend') || (window.saasDashboardConfig && JSON.stringify(window.saasDashboardConfig.revenueTrend));
        if (trendDataRaw) {
            try {
                const trendData = JSON.parse(trendDataRaw);
                const months = trendData.map(item => item.month);
                const revenues = trendData.map(item => item.revenue);

                new Chart(revCanvas, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Monthly Revenue',
                            data: revenues,
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.05)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3.5,
                            pointBackgroundColor: '#4e73df',
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#2e59d9'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return 'Revenue: ' + (context.parsed.y).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.04)' }
                            }
                        }
                    }
                });
            } catch (err) {
                console.warn('Failed to parse revenue chart data:', err);
            }
        }
    }

    // Subscription Plan Distribution Doughnut Chart
    const planCanvas = document.getElementById('planChart');
    if (planCanvas && typeof Chart !== 'undefined') {
        const planDataRaw = planCanvas.getAttribute('data-plans') || (window.saasDashboardConfig && JSON.stringify(window.saasDashboardConfig.planDistribution));
        if (planDataRaw) {
            try {
                const planData = JSON.parse(planDataRaw);
                const chart = new Chart(planCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: planData.map(p => p.name),
                        datasets: [{
                            data: planData.map(p => p.salons_count || p.count || 0),
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#20c997'],
                            hoverOffset: 4,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });

                const legendContainer = document.getElementById('planLegend');
                if (legendContainer) {
                    legendContainer.innerHTML = '';
                    planData.forEach((p, i) => {
                        const color = chart.data.datasets[0].backgroundColor[i % chart.data.datasets[0].backgroundColor.length];
                        legendContainer.innerHTML += `<span class="me-3 mb-1 d-inline-flex align-items-center small text-muted"><i class="fas fa-circle me-1" style="color: ${color}; font-size: 0.65rem;"></i> ${p.name} (${p.salons_count || p.count || 0})</span>`;
                    });
                }
            } catch (err) {
                console.warn('Failed to parse plan chart data:', err);
            }
        }
    }
});
