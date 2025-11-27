document.addEventListener("DOMContentLoaded", function () {
    // Chart එක අඳින්න ඕන තැන තියෙනවද කියලා බලනවා (Error එන එක නවත්තන්න)
    const chartElement = document.querySelector("#attendance-chart");

    if (chartElement) {
        var options = {
            series: [{
                name: 'Present Students',
                data: [45, 52, 38, 24, 33, 26, 65] // Backend එකෙන් එන දත්ත මෙතනට දාන්න පුළුවන්
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
            },
            colors: ['#3b82f6'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' },
                    formatter: function (value) {
                        return value;
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " Students"
                    }
                }
            }
        };

        var chart = new ApexCharts(chartElement, options);
        chart.render();
    }
});