(() => {

    const API_URL = 'https://app.faps.my.id/api/pengaduan/last-eight-week';
    const API_TOKEN = 'MY_SUPER_SECRET_TOKEN';

    const ENDPOINTS = {
        weekly:  'https://app.faps.my.id/api/pengaduan/last-eight-week',
        weeklyComplete: 'https://app.faps.my.id/api/pengaduan/last-eight-week-complete',
        monthly: 'https://app.faps.my.id/api/pengaduan/last-six-month',
        monthlyComplete: 'https://app.faps.my.id/api/pengaduan/last-six-month-complete',
        category: 'https://app.faps.my.id/api/pengaduan/category',
        type: 'https://app.faps.my.id/api/pengaduan/type',
        kecamatan: 'https://app.faps.my.id/api/pengaduan/kecamatan',
        kelurahan: 'https://app.faps.my.id/api/pengaduan/kelurahan'
    };

    /* ======================================================
    HELPER: fetch with Bearer Token
    ====================================================== */
    async function fetchWithToken(url) {
    const response = await fetch(url, {
        headers: {
        'Authorization': `Bearer ${API_TOKEN}`,
        'Accept': 'application/json'
        }
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch ${url} (${response.status})`);
    }

    return response.json();
    }    

    /* ======================================================
    CHART: Line Chart (Mingguan)
    ====================================================== */
    function renderWeeklyLineChart(canvasId, data) {
    const labels = data.map(item => item.label_minggu);
    const values = data.map(item => Number(item.total_laporan));

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
        labels,
        datasets: [{
            label: 'Jumlah Laporan Masuk (8 Minggu Terakhir)',
            data: values,
            tension: 0.35,
            fill: true,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
        },
        options: {
        responsive: true,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
            callbacks: {
                label: ctx => `${ctx.raw} laporan`
            }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            title: {
                display: true,
                text: 'Jumlah Laporan'
            }
            },
            x: {
            title: {
                display: true,
                text: 'Periode Mingguan'
            }
            }
        }
        }
    });
    }

    function renderWeeklyCompleteLineChart(canvasId, data) {
    const labels = data.map(item => item.label_minggu);
    const values = data.map(item => Number(item.total_laporan));

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
        labels,
        datasets: [{
            label: 'Jumlah Laporan Selesai (8 Minggu Terakhir)',
            data: values,
            tension: 0.35,
            fill: true,
            backgroundColor: 'rgba(87, 235, 54, 0.2)',
            borderColor: 'rgb(99, 235, 54)',
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
        },
        options: {
        responsive: true,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
            callbacks: {
                label: ctx => `${ctx.raw} laporan`
            }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            title: {
                display: true,
                text: 'Jumlah Laporan'
            }
            },
            x: {
            title: {
                display: true,
                text: 'Periode Mingguan'
            }
            }
        }
        }
    });
    }


    /* ======================================================
    CHART: Bar Chart (Bulanan)
    ====================================================== */
    function renderMonthlyBarChart(canvasId, data) {
    const labels = data.map(item => item.label_bulan);
    const values = data.map(item => Number(item.total_laporan));

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
        labels,
        datasets: [{
            label: 'Jumlah Laporan Masuk (6 Bulan Terakhir)',
            data: values,
            backgroundColor: 'rgba(255, 159, 64, 0.7)', // ORANGE
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
        },
        options: {
        responsive: true,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
            callbacks: {
                label: ctx => `${ctx.raw} laporan`
            }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            title: {
                display: true,
                text: 'Jumlah Laporan'
            }
            },
            x: {
            title: {
                display: true,
                text: 'Periode Bulanan'
            }
            }
        }
        }
    });
    }

    function renderMonthlyCompletedBarChart(canvasId, data) {
    const labels = data.map(item => item.label_bulan);
    const values = data.map(item => Number(item.total_laporan));

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
        labels,
        datasets: [{
            label: 'Jumlah Laporan Selesai (6 Bulan Terakhir)',
            data: values,
            backgroundColor: 'rgba(64, 255, 147, 0.7)', // ORANGE
            borderColor: 'rgb(47, 250, 166)',
            borderWidth: 1,
            borderRadius: 6
        }]
        },
        options: {
        responsive: true,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
            callbacks: {
                label: ctx => `${ctx.raw} laporan`
            }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            title: {
                display: true,
                text: 'Jumlah Laporan'
            }
            },
            x: {
            title: {
                display: true,
                text: 'Periode Bulanan'
            }
            }
        }
        }
    });
    }    

    /* ======================================================
    CHART: Pie Chart (Category)
    ====================================================== */    
    function renderStatusPieChart(canvasId, data) {
    const labels = data.map(item => item.status.toUpperCase());
    const values = data.map(item => Number(item.total));
    const sum = values.reduce((acc, curr) => acc + curr, 0);
    console.log(sum)

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'pie',
        data: {
        labels,
        datasets: [{
            data: values,
            backgroundColor: [
            '#f1c40f', // diverifikasi (kuning)
            '#3498db', // diproses (biru)
            '#2ecc71', // selesai (hijau)
            '#e74c3c'  // ditolak (merah)
            ],
            borderWidth: 1
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            title: {
                display: true,
                text: 'Laporan berdasarkan Status',
                align: 'center',
                padding: {
                    top: 4,
                    bottom: 12
                },
                font: {
                    size: 16,
                    weight: '600'
                }
            },     
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.raw} laporan (${((ctx.raw / sum) * 100).toFixed(2)}%)`
                }
            }
        }
        }
    });
    }


    /* ======================================================
    CHART: Doughtnut (Type)
    ====================================================== */        
    function renderTipeLaporanChart(canvasId, data) {
    const labels = data.map(item => item.tipe_laporan);
    const values = data.map(item => Number(item.total_laporan));
    const sum = values.reduce((acc, curr) => acc + curr, 0);

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
        labels,
        datasets: [{
            data: values,
            backgroundColor: [
            '#1abc9c', // Air
            '#8e44ad', // Tanah / Limbah B3
            '#3498db', // Udara
            '#2ecc71', // RTH
            '#f39c12', // Persampahan
            '#e74c3c'  // Perusakan Lingkungan
            ],
            borderWidth: 1
        }]
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            title: {
                display: true,
                text: 'Laporan berdasarkan Tipe Laporan',
                align: 'center',
                padding: {
                    top: 4,
                    bottom: 8
                },
                font: {
                    size: 16,
                    weight: '600'
                }
            },            
            legend: {
                position: 'right'
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.raw} laporan (${((ctx.raw / sum) * 100).toFixed(2)}%)`
                }
            }
        }
        }
    });
    }

    /* ======================================================
    CHART: Bar Chart (Kecamatan)
    ====================================================== */    
    // function renderKecamatanBarChart(canvasId, data) {

    // // // Fixed ordered list of all kecamatan (x-axis)
    // // const kecamatanList = [
    // //     'Andir','Antapani','Arcamanik','Astanaanyar','Babakan Ciparay',
    // //     'Bandung Kidul','Bandung Kulon','Bandung Wetan','Batununggal',
    // //     'Bojongloa Kaler','Bojongloa Kidul','Buahbatu','Cibeunying Kaler',
    // //     'Cibeunying Kidul','Cibiru','Cicendo','Cidadap','Cinambo','Coblong',
    // //     'Gedebage','Kiaracondong','Lengkong','Mandalajati','Panyileukan',
    // //     'Rancasari','Regol','Sukajadi','Sukasari','Sumur Bandung','Ujungberung'
    // // ];

    // // // Map API response -> { kecamatan: total }
    // // const valueMap = {};
    // // data.forEach(item => {
    // //     if (!item.kecamatan) return;
    // //     valueMap[item.kecamatan.trim()] = Number(item.total_laporan) || 0;
    // // });

    // // // Build dataset in fixed order
    // // const values = kecamatanList.map(nm => valueMap[nm] || 0);

    // const ctx = document.getElementById(canvasId).getContext('2d');
    // const labels = data.map(item => item.kecamatan);
    // const values = data.map(item => Number(item.total_laporan));

    // new Chart(ctx, {
    //     type: 'bar',
    //     data: {
    //     labels: labels,
    //     datasets: [{
    //         label: 'Jumlah Laporan',
    //         data: values,
    //         backgroundColor: 'rgba(33,150,243,0.6)',
    //         borderColor: 'rgba(25,118,210,1)',
    //         borderWidth: 1
    //     }]
    //     },
    //     options: {
    //     responsive: true,
    //     maintainAspectRatio: false,
    //     devicePixelRatio: window.devicePixelRatio || 1,
    //     plugins: {
    //         legend: {
    //         display: false
    //         },
    //         tooltip: {
    //         callbacks: {
    //             label: ctx => `Jumlah Laporan: ${ctx.raw}`
    //         }
    //         }
    //     },
    //     scales: {
    //         x: {
    //         ticks: {
    //             autoSkip: false,
    //             maxRotation: 60,
    //             minRotation: 45,
    //             font: {
    //             size: 10
    //             }
    //         }
    //         },
    //         y: {
    //         beginAtZero: true,
    //         ticks: {
    //             precision: 0
    //         }
    //         }
    //     }
    //     }
    // });
    // }
    function renderKecamatanBarChart(canvasId, data) {
    const labels = data.map(item => item.kecamatan);
    const values = data.map(item => Number(item.total_laporan));

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
        labels: labels.reverse(),
        datasets: [{
            label: 'Jumlah Laporan Masuk',
            data: values.reverse(),
            tension: 0.35,
            fill: true,
            backgroundColor: 'rgba(214, 235, 54, 0.2)',
            borderColor: 'rgb(126, 131, 48)',
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
        },
        options: {
        responsive: true,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
            callbacks: {
                label: ctx => `${ctx.raw} laporan`
            }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            title: {
                display: true,
                text: 'Jumlah Laporan'
            }
            },
        }
        }
    });
    }


    /* ======================================================
    CHART: Bar Chart (Kelurahan)
    ====================================================== */    
    function renderKelurahanBarChart(canvasId, data) {
    const labels = data.map(item => `${item.kelurahan}, ${item.kecamatan}`);
    const values = data.map(item => Number(item.total_pengaduan));

    const ctx = document.getElementById(canvasId).getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
        labels: labels.reverse(),
        datasets: [{
            label: 'Jumlah Laporan Masuk',
            data: values.reverse(),
            tension: 0.35,
            fill: true,
            backgroundColor: 'rgba(214, 235, 54, 0.2)',
            borderColor: 'rgb(126, 131, 48)',
            borderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
        },
        options: {
        responsive: true,
        devicePixelRatio: window.devicePixelRatio || 1,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => `${ctx.raw} laporan`
                }
            }
        },
        scales: {
            y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            title: {
                display: true,
                text: 'Jumlah Laporan'
            }
            },
        }
        }
    });
    }    

    /* ======================================================
    INIT: Load charts on page load
    ====================================================== */
    async function initCharts() {
    try {
        const weeklyData  = await fetchWithToken(ENDPOINTS.weekly);
        const weeklyCompleteData  = await fetchWithToken(ENDPOINTS.weeklyComplete);
        const monthlyData = await fetchWithToken(ENDPOINTS.monthly);
        const monthlyCompleteData = await fetchWithToken(ENDPOINTS.monthlyComplete);
        const statusData = await fetchWithToken(ENDPOINTS.category);
        const tipeLaporanData = await fetchWithToken(ENDPOINTS.type);
        const kecamatanData = await fetchWithToken(ENDPOINTS.kecamatan);
        const kelurahanData = await fetchWithToken(ENDPOINTS.kelurahan);


        renderWeeklyLineChart('chartLaporanMingguan', weeklyData);
        renderWeeklyCompleteLineChart('chartLaporanSelesaiMingguan', weeklyCompleteData);
        renderMonthlyBarChart('chartLaporanBulanan', monthlyData);
        renderMonthlyCompletedBarChart('chartLaporanSelesaiBulanan', monthlyCompleteData);
        renderStatusPieChart('chartLaporanStatus', statusData);
        renderTipeLaporanChart('chartTipeLaporan', tipeLaporanData);
        renderKecamatanBarChart('chartPengaduanKecamatan', kecamatanData);
        renderKelurahanBarChart('chartPengaduanKelurahan', kelurahanData);

    } catch (error) {
        console.error('Gagal memuat chart:', error);
    }
    }

    /* ======================================================
    RUN ON PAGE LOAD
    ====================================================== */
    document.addEventListener('DOMContentLoaded', initCharts);

})();