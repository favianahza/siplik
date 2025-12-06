function initCharts() {
  const token = 'MY_SUPER_SECRET_TOKEN'; // or from your config/session

  const harianCanvas  = document.getElementById('chartHarian');
  const bulananCanvas = document.getElementById('chartBulanan');

  if (!harianCanvas && !bulananCanvas) return;

  /* ===========================
     Helper: date range (last 5 days)
     =========================== */
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() - 4); // last 5 hari (today-4 .. today)

  /* ===========================
     Helper: month mapping (EN → index → ID)
     =========================== */
  const enMonthToIndex = {
    January: 0, February: 1, March: 2, April: 3, May: 4, June: 5,
    July: 6, August: 7, September: 8, October: 9, November: 10, December: 11
  };

  const bulanID = [
    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
  ];

  const now = new Date();
  const last5MonthIndexes = [];
  for (let i = 0; i < 5; i++) {
    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
    last5MonthIndexes.push(d.getMonth()); // 0–11
  }

  /* ===========================
     1) Laporan Masuk Harian (Last 5 days)
     =========================== */
if (harianCanvas) {
  const apiFiveDay = 'https://app.faps.my.id/api/pengaduan/fiveday';

  $.ajax({
    url: apiFiveDay,
    method: 'GET',
    dataType: 'json',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
    data: {},
    timeout: 30000,
    success: function (res) {
      const arr = Array.isArray(res) ? res : [];

      // Map API result by tanggal: { "2025-12-02": 1, ... }
      const mapByDate = {};
      arr.forEach(item => {
        mapByDate[item.tanggal] = item.total_laporan || 0;
      });

      // Build last 5 days from startDate .. today (inclusive)
      const labelsHarian = [];
      const dataHarian   = [];

      const loopDate = new Date(startDate.getTime());
      while (loopDate <= today) {
        // YYYY-MM-DD key
        const yyyy = loopDate.getFullYear();
        const mm   = String(loopDate.getMonth() + 1).padStart(2, '0');
        const dd   = String(loopDate.getDate()).padStart(2, '0');
        const key  = `${yyyy}-${mm}-${dd}`;

        // DD-MM-YYYY label
        labelsHarian.push(`${dd}-${mm}-${yyyy}`);

        // Use value from API or 0 if missing
        dataHarian.push(mapByDate[key] || 0);

        loopDate.setDate(loopDate.getDate() + 1);
      }

      const ctx = harianCanvas.getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: labelsHarian,
          datasets: [{
            label: 'Total Laporan Perhari',
            data: dataHarian,
            borderColor: '#2e7d32',
            backgroundColor: 'rgba(46,125,50,0.2)',
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: '#1b5e20',
            lineTension: 0.2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              ticks: {
                autoSkip: false,      // show all 5 dates
                maxRotation: 0,
                minRotation: 0
              }
            },
            y: {
              ticks: { beginAtZero: true, precision: 0 }
            }
          }
        }
      });
    },
    error: function (xhr, status, err) {
      console.error('Error /fiveday:', status, err);
    }
  });
}

  /* ===========================
     2) Laporan Masuk Bulanan (Last 5 months)
     =========================== */

if (bulananCanvas) {
  const apiFiveMonth = 'https://app.faps.my.id/api/pengaduan/fivemonth';

  $.ajax({
    url: apiFiveMonth,
    method: 'GET',
    dataType: 'json',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
    timeout: 30000,
    success: function (res) {
      const arr = Array.isArray(res) ? res : [];

      // Month mapping
      const enMonthToIndex = {
        January: 0, February: 1, March: 2, April: 3, May: 4, June: 5,
        July: 6, August: 7, September: 8, October: 9, November: 10, December: 11
      };

      const bulanID = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
      ];

      // Build last 5 months (ordered oldest → newest)
      const now = new Date();
      let last5Months = [];
      for (let i = 4; i >= 0; i--) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        last5Months.push(d.getMonth()); // monthIndex
      }

      // Build lookup: monthIndex → total_laporan
      const monthValueMap = {}; 
      arr.forEach(item => {
        const idx = enMonthToIndex[item.bulan];
        if (typeof idx === "number") {
          monthValueMap[idx] = item.total_laporan || 0;
        }
      });

      // Create labels + data in correct chronological order
      const labelsBulanan = last5Months.map(idx => bulanID[idx]);
      const dataBulanan   = last5Months.map(idx => monthValueMap[idx] || 0);

      // Draw Chart
      const ctx = bulananCanvas.getContext("2d");
      new Chart(ctx, {
        type: "line",
        data: {
          labels: labelsBulanan,
          datasets: [{
            label: "Total Laporan per Bulan",
            data: dataBulanan,
            borderColor: "#0277bd",
            backgroundColor: "rgba(2,119,189,0.2)",
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: "#01579b",
            lineTension: 0.2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              ticks: { beginAtZero: true, precision: 0 }
            }
          }
        }
      });
    }
  });
}

  /* ===========================
     3) Laporan Berdasarkan Kategori (Bar Chart)
     =========================== */
  const kategoriCanvas = document.getElementById('chartKategori');
  if (kategoriCanvas) {
    const apiKategori = 'https://app.faps.my.id/api/pengaduan/category';

    $.ajax({
      url: apiKategori,
      method: 'GET',
      dataType: 'json',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
      data: {},
      timeout: 30000,
      success: function (res) {
        const arr = Array.isArray(res) ? res : [];

        // Base categories we always show (default 0)
        const counts = {
          diterima: 0,      // "masuk"
          diverifikasi: 0,  // "diverifikasi" / "verifikasi"
          ditolak: 0,
          diproses: 0,
          selesai: 0
        };

        // Map API status -> our keys
        arr.forEach(item => {
          const status = String(item.status || '').toLowerCase();
          const total  = item.total || 0;

          if (status === 'masuk' || status === 'diterima') {
            counts.diterima = total;
          } else if (status === 'diverifikasi' || status === 'verifikasi') {
            counts.diverifikasi = total;
          } else if (status === 'ditolak') {
            counts.ditolak = total;
          } else if (status === 'diproses') {
            counts.diproses = total;
          } else if (status === 'selesai') {
            counts.selesai = total;
          }
        });

        const labelsKategori = [
          'Diterima',
          'Diverifikasi',
          'Ditolak',
          'Diproses',
          'Selesai'
        ];

        const dataKategori = [
          counts.diterima,
          counts.diverifikasi,
          counts.ditolak,
          counts.diproses,
          counts.selesai
        ];

        // Colors based on meaning
        const colors = [
          '#1e88e5', // Diterima (biru)
          '#5e35b1', // Diverifikasi (ungu)
          '#e53935', // Ditolak (merah)
          '#fb8c00', // Diproses (oranye)
          '#43a047'  // Selesai (hijau)
        ];

        const ctx = kategoriCanvas.getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labelsKategori,
            datasets: [{
              label: 'Jumlah Laporan',
              data: dataKategori,
              backgroundColor: colors,
              borderColor: colors.map(c => c),
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              x: {
                ticks: {
                  autoSkip: false
                }
              },
              y: {
                ticks: {
                  beginAtZero: true,
                  precision: 0
                }
              }
            },
            legend: {
              display: false
            }
          }
        });
      },
      error: function (xhr, status, err) {
        console.error('Error /category:', status, err);
      }
    });
  }


    /* ===========================
     4) Laporan setiap Kecamatan (Bar Chart)
     =========================== */
  const kecamatanCanvas = document.getElementById('chartKecamatan');
  if (kecamatanCanvas) {
    const apiKecamatan = 'https://app.faps.my.id/api/pengaduan/kecamatan';

    // Fixed ordered list of all kecamatan (x-axis)
    const kecamatanList = [
      'Andir',
      'Antapani',
      'Arcamanik',
      'Astanaanyar',
      'Babakan Ciparay',
      'Bandung Kidul',
      'Bandung Kulon',
      'Bandung Wetan',
      'Batununggal',
      'Bojongloa Kaler',
      'Bojongloa Kidul',
      'Buahbatu',
      'Cibeunying Kaler',
      'Cibeunying Kidul',
      'Cibiru',
      'Cicendo',
      'Cidadap',
      'Cinambo',
      'Coblong',
      'Gedebage',
      'Kiaracondong',
      'Lengkong',
      'Mandalajati',
      'Panyileukan',
      'Rancasari',
      'Regol',
      'Sukajadi',
      'Sukasari',
      'Sumur Bandung',
      'Ujungberung'
    ];

    $.ajax({
      url: apiKecamatan,
      method: 'GET',
      dataType: 'json',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
      data: {},
      timeout: 30000,
      success: function (res) {
        const arr = Array.isArray(res) ? res : [];

        // Map API -> { 'Bojongloa Kaler': 14, ... }
        const valueMap = {};
        arr.forEach(item => {
          if (!item.kecamatan) return;
          valueMap[item.kecamatan.trim()] = item.total_laporan || 0;
        });

        // Build data in fixed order, default 0 if not found
        const dataKecamatan = kecamatanList.map(nm => valueMap[nm] || 0);

        const ctx = kecamatanCanvas.getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: kecamatanList,
            datasets: [{
              label: 'Jumlah Laporan',
              data: dataKecamatan,
              backgroundColor: 'rgba(33,150,243,0.6)',   // soft blue
              borderColor: 'rgba(25,118,210,1)',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              x: {
                ticks: {
                  autoSkip: false,
                  maxRotation: 60,
                  minRotation: 45,
                  fontSize: 10
                }
              },
              y: {
                ticks: {
                  beginAtZero: true,
                  precision: 0
                }
              }
            },
            legend: {
              display: false
            },
            tooltips: {
              callbacks: {
                title: function (tooltipItems, data) {
                  return data.labels[tooltipItems[0].index];
                },
                label: function (tooltipItem, data) {
                  return 'Jumlah Laporan: ' + data.datasets[0].data[tooltipItem.index];
                }
              }
            }
          }
        });
      },
      error: function (xhr, status, err) {
        console.error('Error /kecamatan:', status, err);
      }
    });
  }

/* ==========================================================
   5) Jumlah laporan terbanyak berdasarkan kelurahan
   ========================================================== */
const kelurahanCanvas = document.getElementById('chartKelurahan');

if (kelurahanCanvas) {
  const apiKelurahan = 'https://app.faps.my.id/api/pengaduan/kelurahan';

  $.ajax({
    url: apiKelurahan,
    method: 'GET',
    dataType: 'json',
    headers: {
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
    },
    timeout: 30000,
    success: function (res) {
      const arr = Array.isArray(res) ? res : [];

      // Build labels → "Kelurahan (Kecamatan)"
      const labelsKel = arr.map(item =>
        `${item.kelurahan} (${item.kecamatan})`
      ).reverse();

      // Values → total_pengaduan
      const dataKel = arr.map(item => item.total_pengaduan || 0).reverse();

      const ctx = kelurahanCanvas.getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: {
          labels: labelsKel,
          datasets: [{
            label: "Jumlah Pengaduan",
            data: dataKel,
            backgroundColor: "rgba(76, 175, 80, 0.6)",  // green-ish
            borderColor: "rgba(56, 142, 60, 1)",
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              ticks: {
                autoSkip: false,      // show all kelurahan labels
                maxRotation: 60,
                minRotation: 45,
                fontSize: 10
              }
            },
            y: {
              ticks: {
                beginAtZero: true,
                precision: 0
              }
            }
          },
          legend: {
            display: false
          },
          tooltips: {
            callbacks: {
              title: function (tooltipItems, data) {
                return data.labels[tooltipItems[0].index];
              },
              label: function (tooltipItem, data) {
                return "Total laporan: " + data.datasets[0].data[tooltipItem.index];
              }
            }
          }
        }
      });
    },

    error: function (xhr, status, err) {
      console.error("Error /kelurahan:", status, err);
    }
  });
}

  /* ==========================================================
     6) Petugas: tugas aktif & tugas selesai terbanyak
     ========================================================== */
  const canvasAktif   = document.getElementById('chartPetugasAktif');
  const canvasSelesai = document.getElementById('chartPetugasSelesai');

  if (canvasAktif && canvasSelesai) {
    const apiPetugas = 'https://app.faps.my.id/api/users/petugas?status=yes';

    let chartPetugasAktif   = null;
    let chartPetugasSelesai = null;

    let sortedByAktif   = [];
    let sortedBySelesai = [];

    let showAllAktif   = false;
    let showAllSelesai = false;

    $.ajax({
      url: apiPetugas,
      method: 'GET',
      dataType: 'json',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
      timeout: 30000,
      success: function (res) {
        const arr = Array.isArray(res) ? res : [];

        // Sort data once
        sortedByAktif = arr.slice().sort((a, b) => (b.tugas_aktif   || 0) - (a.tugas_aktif   || 0));
        sortedBySelesai = arr.slice().sort((a, b) => (b.tugas_selesai || 0) - (a.tugas_selesai || 0));

        // --- Helpers to (re)build charts ---
        function buildChartAktif() {
          const ctx = canvasAktif.getContext('2d');
          const source = showAllAktif ? sortedByAktif : sortedByAktif.slice(0, 5);

          const labels = source.map(p => p.petugas_nama).reverse();
          const data   = source.map(p => p.tugas_aktif || 0).reverse();

          if (chartPetugasAktif) {
            chartPetugasAktif.destroy();
          }

          chartPetugasAktif = new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets: [{
                label: 'Tugas Aktif',
                data,
                backgroundColor: 'rgba(255, 152, 0, 0.7)',  // orange-ish
                borderColor: 'rgba(245, 124, 0, 1)',
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: {
                  ticks: {
                    autoSkip: false,
                    maxRotation: 45,
                    minRotation: 0,
                    fontSize: 10
                  }
                },
                y: {
                  ticks: {
                    beginAtZero: true,
                    precision: 0
                  }
                }
              },
              legend: { display: false },
              tooltips: {
                callbacks: {
                  title: function (tooltipItems, data) {
                    return data.labels[tooltipItems[0].index];
                  },
                  label: function (tooltipItem, data) {
                    return 'Tugas aktif: ' + data.datasets[0].data[tooltipItem.index];
                  }
                }
              }
            }
          });
        }

        function buildChartSelesai() {
          const ctx = canvasSelesai.getContext('2d');
          const source = showAllSelesai ? sortedBySelesai : sortedBySelesai.slice(0, 5);

          const labels = source.map(p => p.petugas_nama).reverse();
          const data   = source.map(p => p.tugas_selesai || 0).reverse();

          if (chartPetugasSelesai) {
            chartPetugasSelesai.destroy();
          }

          chartPetugasSelesai = new Chart(ctx, {
            type: 'bar',
            data: {
              labels,
              datasets: [{
                label: 'Tugas Selesai',
                data,
                backgroundColor: 'rgba(56, 142, 60, 0.7)',  // green-ish
                borderColor: 'rgba(46, 125, 50, 1)',
                borderWidth: 1
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                x: {
                  ticks: {
                    autoSkip: false,
                    maxRotation: 45,
                    minRotation: 0,
                    fontSize: 10
                  }
                },
                y: {
                  ticks: {
                    beginAtZero: true,
                    precision: 0
                  }
                }
              },
              legend: { display: false },
              tooltips: {
                callbacks: {
                  title: function (tooltipItems, data) {
                    return data.labels[tooltipItems[0].index];
                  },
                  label: function (tooltipItem, data) {
                    return 'Tugas selesai: ' + data.datasets[0].data[tooltipItem.index];
                  }
                }
              }
            }
          });
        }

        // Build initial (Top 5)
        buildChartAktif();
        buildChartSelesai();

        // Toggle buttons
        $('#toggleAktifPetugas')
          .off('click')
          .on('click', function () {
            showAllAktif = !showAllAktif;
            $(this).text(showAllAktif ? 'Tampilkan Top 5' : 'Tampilkan Semua');
            buildChartAktif();
          });

        $('#toggleSelesaiPetugas')
          .off('click')
          .on('click', function () {
            showAllSelesai = !showAllSelesai;
            $(this).text(showAllSelesai ? 'Tampilkan Top 5' : 'Tampilkan Semua');
            buildChartSelesai();
          });
      },

      error: function (xhr, status, err) {
        console.error('Error /users/petugas:', status, err);
      }
    });
  }

}

function initStatistikLaporan() {
  const token = 'MY_SUPER_SECRET_TOKEN'; // or whatever you already use

  const baseUrl = 'https://app.faps.my.id/api/pengaduan';

  // Helper: generic fetch for {total: X}
  function fetchTotal(endpoint, $target) {
    $.ajax({
      url: `${baseUrl}/${endpoint}`,
      method: 'GET',
      dataType: 'json',
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${token}`,
      },
      timeout: 30000,
      success: function (res) {
        const total = (res && typeof res.total !== 'undefined') ? res.total : 0;
        $target.text(total);
      },
      error: function (xhr, status, err) {
        console.error(`Error /${endpoint}:`, status, err);
        $target.text('0');
      }
    });
  }

  // Call each endpoint and bind to each card
  fetchTotal('masuk-hari-ini',     $('#stat-masuk-hari-ini'));
  fetchTotal('aktif',              $('#stat-laporan-aktif'));
  fetchTotal('selesai-bulan-ini',  $('#stat-selesai-bulan-ini'));
  fetchTotal('ditolak',            $('#stat-ditolak'));
}
