/**
 * POS Sales Analytics Scripts
 * Handles Date Presets & ApexCharts Initialization
 */

(function () {
    'use strict';

    // -------------------------------------------------------------
    // Quick Date Range Filter Presets Handler
    // -------------------------------------------------------------
    function initDateFilterPresets() {
        function formatDate(d) {
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const filterForm = document.getElementById('analyticsDateFilterForm');

        if (!startDateInput || !endDateInput) return;

        const currentStart = startDateInput.value;
        const currentEnd = endDateInput.value;

        document.querySelectorAll('.filter-preset-btn').forEach(btn => {
            const range = btn.dataset.range;
            let s = new Date();
            let e = new Date();

            if (range === 'today') {
                // s & e are today
            } else if (range === 'yesterday') {
                s.setDate(s.getDate() - 1);
                e.setDate(e.getDate() - 1);
            } else if (range === 'last7') {
                s.setDate(s.getDate() - 6);
            } else if (range === 'thisMonth') {
                s = new Date(s.getFullYear(), s.getMonth(), 1);
            } else if (range === 'last30') {
                s.setDate(s.getDate() - 30);
            }

            if (formatDate(s) === currentStart && formatDate(e) === currentEnd) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', function () {
                let startObj = new Date();
                let endObj = new Date();

                if (range === 'today') {
                    // start & end are today
                } else if (range === 'yesterday') {
                    startObj.setDate(startObj.getDate() - 1);
                    endObj.setDate(endObj.getDate() - 1);
                } else if (range === 'last7') {
                    startObj.setDate(startObj.getDate() - 6);
                } else if (range === 'thisMonth') {
                    startObj = new Date(startObj.getFullYear(), startObj.getMonth(), 1);
                } else if (range === 'last30') {
                    startObj.setDate(startObj.getDate() - 30);
                }

                startDateInput.value = formatDate(startObj);
                endDateInput.value = formatDate(endObj);
                if (filterForm) {
                    filterForm.submit();
                }
            });
        });
    }

    // -------------------------------------------------------------
    // Charts Initializer
    // -------------------------------------------------------------
    function initAnalyticsCharts() {
        if (typeof ApexCharts === 'undefined') {
            setTimeout(initAnalyticsCharts, 80);
            return;
        }

        if (window.__posAnalyticsInitialized) {
            return;
        }
        window.__posAnalyticsInitialized = true;

        const config = window.posAnalyticsConfig || {};
        const currencySymbol = config.currencySymbol || '$';

        const dailyData = config.dailyData || {};
        const weeklyData = config.weeklyData || {};
        const monthlyData = config.monthlyData || {};
        const hourlyData = config.hourlyData || {};

        // --- 1. Revenue Trends Chart (Area Chart) ---
        let revenueChart = null;

        function renderRevenueChart(data, type) {
            const chartEl = document.querySelector("#revenueChart");
            if (!chartEl) return;

            if (revenueChart) {
                try { revenueChart.destroy(); } catch (e) {}
            }
            chartEl.innerHTML = '';

            const dates = Object.keys(data);
            const values = Object.values(data);

            const options = {
                chart: {
                    type: 'area',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    sparkline: { enabled: false }
                },
                series: [{
                    name: 'Revenue',
                    data: values.map((val, idx) => ({
                        x: dates[idx],
                        y: val
                    }))
                }],
                xaxis: {
                    type: type === 'day' ? 'datetime' : 'category',
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px', fontWeight: 500 }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return currencySymbol + val.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                        },
                        style: { colors: '#64748b', fontSize: '11px' }
                    }
                },
                colors: ['#0d6efd'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                stroke: { width: 2.5, curve: 'smooth' },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 });
                        }
                    }
                }
            };

            if (revenueChart) {
                revenueChart.destroy();
            }
            revenueChart = new ApexCharts(chartEl, options);
            revenueChart.render();
        }

        renderRevenueChart(dailyData, 'day');

        window.updateRevenueChart = function (period, btnEl) {
            document.querySelectorAll('.segmented-btn-group button').forEach(btn => {
                if (btn && btn.classList) btn.classList.remove('active');
            });
            if (btnEl && btnEl.classList) {
                btnEl.classList.add('active');
            } else if (window.event && window.event.target) {
                const target = window.event.target.closest('button');
                if (target && target.classList) target.classList.add('active');
            }

            if (period === 'day') {
                renderRevenueChart(dailyData, 'day');
            } else if (period === 'week') {
                renderRevenueChart(weeklyData, 'category');
            } else if (period === 'month') {
                renderRevenueChart(monthlyData, 'category');
            }
        };

        // --- 2. Payment Distribution Donut Chart ---
        let distChart = null;
        const distEl = document.querySelector("#paymentDistributionChart");
        if (distEl && config.paymentAnalytics) {
            distEl.innerHTML = '';
            const pa = config.paymentAnalytics;
            const paymentDistributionOptions = {
                series: [
                    pa.paidAmount || 0,
                    pa.netRefunded || 0,
                    pa.tipAmount || 0,
                    pa.unpaidAmount || 0,
                    pa.discountAmount || 0,
                    pa.voidedAmount || 0
                ],
                chart: {
                    type: 'donut',
                    height: 230,
                    fontFamily: 'inherit'
                },
                labels: ['Paid Revenue', 'Net Refunded', 'Tips', 'Unpaid', 'Discounts', 'Voided'],
                colors: ['#10b981', '#ef4444', '#06b6d4', '#f59e0b', '#8b5cf6', '#94a3b8'],
                legend: { show: false },
                dataLabels: { enabled: false },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 });
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 600, color: '#64748b' },
                                value: {
                                    fontSize: '15px',
                                    fontWeight: 800,
                                    formatter: function (val) {
                                        return currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 0 });
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Value',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                    color: '#64748b',
                                    formatter: function (w) {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return currencySymbol + sum.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                                    }
                                }
                            }
                        }
                    }
                }
            };

            distChart = new ApexCharts(distEl, paymentDistributionOptions);
            distChart.render();
        }

        // --- 3. Category Sales Donut Chart ---
        let catChart = null;
        const catEl = document.querySelector("#categoryChart");
        if (catEl && Array.isArray(config.categoryData)) {
            catEl.innerHTML = '';
            const categoryLabels = config.categoryData.map(item => item.name);
            const categorySeries = config.categoryData.map(item => item.total_revenue);

            const categoryOptions = {
                chart: {
                    type: 'donut',
                    height: 220,
                    fontFamily: 'inherit'
                },
                series: categorySeries,
                labels: categoryLabels,
                colors: config.categoryColors || ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                legend: { show: false },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '72%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', fontWeight: 600, color: '#64748b' },
                                value: {
                                    fontSize: '15px',
                                    fontWeight: 800,
                                    formatter: function (val) {
                                        return currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 0 });
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Category Total',
                                    fontSize: '11px',
                                    fontWeight: 600,
                                    color: '#64748b',
                                    formatter: function (w) {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return currencySymbol + sum.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                                    }
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 });
                        }
                    }
                }
            };

            catChart = new ApexCharts(catEl, categoryOptions);
            catChart.render();
        }

        // --- 4. Hourly Sales Bar Chart ---
        let hrChart = null;
        const hrEl = document.querySelector("#hourlyChart");
        if (hrEl) {
            hrEl.innerHTML = '';
            const hours = Array.from({ length: 24 }, (_, i) => i);
            const hourlyValues = hours.map(h => hourlyData[h] || 0);

            const hourlyOptions = {
                chart: {
                    type: 'bar',
                    height: 250,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{
                    name: 'Sales Volume',
                    data: hourlyValues
                }],
                xaxis: {
                    categories: hours.map(h => `${h}:00`),
                    labels: {
                        style: { colors: '#64748b', fontSize: '10px' },
                        rotate: 0,
                        formatter: function (val) {
                            const h = parseInt(val, 10);
                            return (h % 2 === 0) ? `${h}:00` : '';
                        }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return currencySymbol + val.toLocaleString(undefined, { minimumFractionDigits: 0 });
                        },
                        style: { colors: '#64748b', fontSize: '10px' }
                    }
                },
                colors: ['#10b981'],
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: '55%',
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function (val) {
                            return currencySymbol + Number(val).toLocaleString(undefined, { minimumFractionDigits: 2 });
                        }
                    }
                }
            };

            hrChart = new ApexCharts(hrEl, hourlyOptions);
            hrChart.render();
        }

        // --- 5. Payment Method Tender Bar Chart ---
        let pmChart = null;
        const pmEl = document.querySelector("#paymentMethodChart");
        if (pmEl && config.paymentMethodData) {
            pmEl.innerHTML = '';
            const pmd = config.paymentMethodData;
            const pmLabels = ['Cash', 'Card', 'Online', 'Other'];
            const pmAmounts = [pmd.cash || 0, pmd.card || 0, pmd.online || 0, pmd.other || 0];
            const pmColors = ['#10b981', '#0d6efd', '#0dcaf0', '#6f42c1'];

            const paymentMethodChartOptions = {
                chart: {
                    type: 'bar',
                    height: 240,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                series: [{ name: 'Collected', data: pmAmounts }],
                xaxis: {
                    categories: pmLabels,
                    labels: { style: { fontSize: '12px', fontWeight: 600, colors: '#334155' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: v => currencySymbol + Number(v).toLocaleString(undefined, { minimumFractionDigits: 0 }),
                        style: { colors: '#64748b', fontSize: '10px' }
                    }
                },
                colors: pmColors,
                plotOptions: {
                    bar: {
                        distributed: true,
                        borderRadius: 6,
                        columnWidth: '50%',
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: v => currencySymbol + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2 }),
                    offsetY: -20,
                    style: { fontSize: '11px', fontWeight: 700, colors: ['#0f172a'] }
                },
                legend: { show: false },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: v => currencySymbol + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2 })
                    }
                }
            };

            pmChart = new ApexCharts(pmEl, paymentMethodChartOptions);
            pmChart.render();
        }

        // --- 6. Void Reasons Horizontal Bar Chart ---
        let vrChart = null;
        const vrEl = document.querySelector("#voidReasonsChart");
        if (vrEl && Array.isArray(config.voidReasonsData) && config.voidReasonsData.length > 0) {
            vrEl.innerHTML = '';
            const voidReasonsOptions = {
                series: [{
                    name: 'Void Count',
                    data: config.voidReasonsCounts || []
                }],
                chart: {
                    type: 'bar',
                    height: 140,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '65%',
                        distributed: true,
                        borderRadius: 4,
                    }
                },
                colors: ['#f59e0b', '#fbbf24', '#fcd34d', '#fde68a'],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: config.voidReasonsLabels || [],
                    labels: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { fontSize: '11px', colors: '#64748b', fontWeight: 500 }
                    }
                },
                grid: { show: false },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: val => val + (val === 1 ? ' void' : ' voids')
                    }
                }
            };

            vrChart = new ApexCharts(vrEl, voidReasonsOptions);
            vrChart.render();
        }

        // Resize event listener for responsive reflow
        window.addEventListener('resize', function () {
            if (revenueChart) revenueChart.updateOptions({ chart: { width: '100%' } });
            if (distChart) distChart.updateOptions({ chart: { width: '100%' } });
            if (catChart) catChart.updateOptions({ chart: { width: '100%' } });
            if (hrChart) hrChart.updateOptions({ chart: { width: '100%' } });
            if (pmChart) pmChart.updateOptions({ chart: { width: '100%' } });
        });
    }

    // Initialize once DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initDateFilterPresets();
            initAnalyticsCharts();
        });
    } else {
        initDateFilterPresets();
        initAnalyticsCharts();
    }
})();
