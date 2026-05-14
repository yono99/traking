<template>
  <AppLayout>
    <div class="p-6 max-w-7xl mx-auto">

      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laporan & Analisis</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau aktivitas berkas dan performa unit</p>
      </div>

      <!-- Filter Card -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex flex-wrap items-end gap-4">
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai</label>
            <input
              type="date"
              v-model="startDate"
              class="block rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Akhir</label>
            <input
              type="date"
              v-model="endDate"
              class="block rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
          <button
            @click="fetchData"
            :disabled="loading"
            class="flex items-center gap-2 bg-indigo-600 text-white text-sm px-5 py-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition"
          >
            <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <span>{{ loading ? 'Memuat...' : 'Tampilkan Data' }}</span>
          </button>
          <button
            @click="downloadExcel"
            class="flex items-center gap-2 bg-emerald-600 text-white text-sm px-5 py-2 rounded-lg hover:bg-emerald-700 transition"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Unduh Excel
          </button>
        </div>
        <p v-if="errorMsg" class="text-red-500 text-xs mt-3">{{ errorMsg }}</p>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 mb-1">Total Berkas</p>
          <p class="text-2xl font-semibold text-gray-800">{{ stats.total ?? '—' }}</p>
          <p class="text-xs text-gray-400 mt-1">Semua aktivitas</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 mb-1">Sedang Diproses</p>
          <p class="text-2xl font-semibold text-indigo-600">{{ stats.proses ?? '—' }}</p>
          <p class="text-xs text-gray-400 mt-1">Aktif di unit</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 mb-1">Selesai Diserahkan</p>
          <p class="text-2xl font-semibold text-emerald-600">{{ stats.selesai ?? '—' }}</p>
          <p class="text-xs text-gray-400 mt-1">Produk diterima</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
          <p class="text-xs text-gray-500 mb-1">Menunggu Proses</p>
          <p class="text-2xl font-semibold text-amber-500">{{ stats.tunggu ?? '—' }}</p>
          <p class="text-xs text-gray-400 mt-1">Forward ke unit</p>
        </div>
      </div>

      <!-- Grafik Utama: Line Chart -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="flex items-start justify-between mb-4 flex-wrap gap-3">
          <div>
            <h2 class="text-base font-semibold text-gray-800">Aktivitas Berkas Harian</h2>
            <p class="text-xs text-gray-500 mt-0.5">Jumlah berkas per status dalam rentang tanggal</p>
          </div>
          <!-- Legend -->
          <div class="flex flex-wrap gap-4 text-xs text-gray-500">
            <span v-for="item in legendItems" :key="item.label" class="flex items-center gap-1.5">
              <span class="inline-block w-6 h-0.5 rounded" :style="{ background: item.color, borderTop: item.dashed ? '2px dashed '+item.color : 'none', background: item.dashed ? 'none' : item.color }"></span>
              {{ item.label }}
            </span>
          </div>
        </div>
        <div v-if="!hasData" class="flex flex-col items-center justify-center h-64 text-gray-400">
          <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <p class="text-sm">Pilih rentang tanggal lalu klik <strong>Tampilkan Data</strong></p>
        </div>
        <div v-show="hasData" style="position: relative; height: 300px;">
          <canvas ref="lineChartRef"></canvas>
        </div>
      </div>

      <!-- Bawah: Donut + Bar -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Donut Chart -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <h2 class="text-base font-semibold text-gray-800 mb-1">Distribusi Status</h2>
          <p class="text-xs text-gray-500 mb-4">Proporsi tiap tahap dalam periode</p>
          <div v-if="!hasData" class="flex items-center justify-center h-48 text-gray-300 text-sm">Belum ada data</div>
          <div v-show="hasData" style="position: relative; height: 220px;">
            <canvas ref="donutChartRef"></canvas>
          </div>
          <!-- Custom Legend Donut -->
          <div v-if="hasData" class="mt-4 space-y-2">
            <div
              v-for="(item, i) in donutLegend" :key="i"
              class="flex items-center justify-between text-xs text-gray-600"
            >
              <span class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-sm inline-block" :style="{ background: item.color }"></span>
                {{ item.label }}
              </span>
              <span class="font-medium text-gray-700">{{ item.value }} <span class="text-gray-400">({{ item.pct }}%)</span></span>
            </div>
          </div>
        </div>

        <!-- Bar Chart -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
          <h2 class="text-base font-semibold text-gray-800 mb-1">Volume per Unit</h2>
          <p class="text-xs text-gray-500 mb-4">Total berkas yang ditangani tiap unit</p>
          <div v-if="!hasData" class="flex items-center justify-center h-48 text-gray-300 text-sm">Belum ada data</div>
          <div v-show="hasData" style="position: relative; height: 220px;">
            <canvas ref="barChartRef"></canvas>
          </div>
        </div>

      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Chart, registerables } from 'chart.js'
Chart.register(...registerables)

// ── State ─────────────────────────────────────────────────────
const startDate = ref('')
const endDate   = ref('')
const loading   = ref(false)
const errorMsg  = ref('')
const hasData   = ref(false)

const stats       = ref({})
const donutLegend = ref([])

const lineChartRef  = ref(null)
const donutChartRef = ref(null)
const barChartRef   = ref(null)

let lineChart  = null
let donutChart = null
let barChart   = null

// ── Config ────────────────────────────────────────────────────
const UNIT_CONFIG = [
  { key: 'loket',            label: 'Loket',            short: 'Loket',     color: '#4F46E5' },
  { key: 'bukutanah',        label: 'Buku Tanah',       short: 'Buku Tanah',color: '#0D9488' },
  { key: 'pengukuran',       label: 'Pengukuran',       short: 'Ukur',      color: '#D97706', dashed: true },
  { key: 'loket_penyerahan', label: 'Loket Penyerahan', short: 'Penyerahan',color: '#059669' },
]

const legendItems = UNIT_CONFIG.map(u => ({ label: u.label, color: u.color, dashed: u.dashed }))

// ── Init ──────────────────────────────────────────────────────
onMounted(() => {
  const today = new Date()
  const week  = new Date(); week.setDate(week.getDate() - 6)
  endDate.value   = today.toISOString().split('T')[0]
  startDate.value = week.toISOString().split('T')[0]
  axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
})

// ── Fetch ─────────────────────────────────────────────────────
const fetchData = async () => {
  errorMsg.value = ''

  if (!startDate.value || !endDate.value) {
    errorMsg.value = 'Silakan pilih rentang tanggal terlebih dahulu.'
    return
  }
  if (startDate.value > endDate.value) {
    errorMsg.value = 'Tanggal mulai harus sebelum tanggal akhir.'
    return
  }

  loading.value = true
  try {
    const res = await axios.post('/api/get-laporan-unit', {
      start_date: startDate.value,
      end_date:   endDate.value,
    })
    const result = res.data

    if (!result.success) {
      errorMsg.value = result.message || 'Terjadi kesalahan saat memuat data.'
      return
    }

    hasData.value = true

    // Hitung stats dari data
    const totalAct = result.total_activities
    const vol      = result.volume_per_unit ?? []
 stats.value = {
  total:   result.total_activities,
  proses:  result.count_proses,
  selesai: result.count_selesai,
  tunggu:  result.count_forward,
}

    updateLineChart(result.data)
    updateDonutChart(result.distribution ?? [])
    updateBarChart(result.volume_per_unit ?? [])

  } catch (err) {
    console.error(err)
    errorMsg.value = 'Gagal terhubung ke server. Cek koneksi atau login ulang.'
  } finally {
    loading.value = false
  }
}

// ── Charts ────────────────────────────────────────────────────

// Status → unit key mapping (supaya baris dari API bisa dikelompokkan)
const STATUS_TO_UNIT = {
  'PROSES LOKET': 'loket',
  'FORWARD LOKET': 'loket',
  'PROSES BUKU TANAH': 'bukutanah',
  'PROSES VALIDASI BUKU TANAH': 'bukutanah',
  'PROSES PENGUKURAN': 'pengukuran',
  'PROSES VALIDASI BIDANG': 'pengukuran',
  'PROSES LOKET PENYERAHAN': 'loket_penyerahan',
  'SELESAI DISERAHKAN': 'loket_penyerahan',
}

const fmtDate = (str) =>
  new Date(str).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })

const updateLineChart = (rawData) => {
  // Kumpulkan semua tanggal unik
  const dates = [...new Set(rawData.map(r => r.date))].sort()

  // Group data per unit
  const seriesData = UNIT_CONFIG.map(cfg => {
    const dailyCounts = dates.map(date => {
      return rawData
        .filter(r => r.date === date && STATUS_TO_UNIT[r.status] === cfg.key)
        .reduce((sum, r) => sum + Number(r.count), 0)
    })
    return {
      label: cfg.label,
      data: dailyCounts,
      borderColor: cfg.color,
      backgroundColor: cfg.color + '18',
      borderWidth: 2,
      pointRadius: dates.length > 14 ? 2 : 4,
      pointHoverRadius: 6,
      tension: 0.4,
      fill: false,
      borderDash: cfg.dashed ? [5, 3] : [],
    }
  })

  if (lineChart) lineChart.destroy()
  lineChart = new Chart(lineChartRef.value, {
    type: 'line',
    data: { labels: dates.map(fmtDate), datasets: seriesData },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: items => 'Tanggal: ' + items[0].label,
            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} berkas`,
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { font: { size: 11 }, color: '#9CA3AF', maxRotation: 45, autoSkip: dates.length > 10 },
        },
        y: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { font: { size: 11 }, color: '#9CA3AF', stepSize: 1, precision: 0 },
          min: 0,
        }
      }
    }
  })
}

const updateDonutChart = (distribution) => {
  // Map distribution ke unit
  const unitTotals = UNIT_CONFIG.map(cfg => {
    const total = distribution
      .filter(d => STATUS_TO_UNIT[d.status] === cfg.key)
      .reduce((sum, d) => sum + Number(d.total), 0)
    return { ...cfg, total }
  })

  const grand = unitTotals.reduce((a, b) => a + b.total, 0)
  donutLegend.value = unitTotals.map(u => ({
    label: u.label,
    color: u.color,
    value: u.total,
    pct: grand > 0 ? Math.round(u.total / grand * 100) : 0,
  }))

  if (donutChart) donutChart.destroy()
  donutChart = new Chart(donutChartRef.value, {
    type: 'doughnut',
    data: {
      labels: unitTotals.map(u => u.label),
      datasets: [{
        data: unitTotals.map(u => u.total),
        backgroundColor: unitTotals.map(u => u.color),
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 6,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => {
              const sum = ctx.dataset.data.reduce((a, b) => a + b, 0)
              const pct = sum > 0 ? Math.round(ctx.parsed / sum * 100) : 0
              return ` ${ctx.label}: ${ctx.parsed} (${pct}%)`
            }
          }
        }
      }
    }
  })
}

const updateBarChart = (volumePerUnit) => {
  const data = UNIT_CONFIG.map(cfg => {
    const found = volumePerUnit.find(v => v.unit === cfg.key)
    return found ? Number(found.count) : 0
  })

  if (barChart) barChart.destroy()
  barChart = new Chart(barChartRef.value, {
    type: 'bar',
    data: {
      labels: UNIT_CONFIG.map(u => u.short),
      datasets: [{
        label: 'Total Berkas',
        data,
        backgroundColor: UNIT_CONFIG.map(u => u.color + 'CC'),
        borderColor:     UNIT_CONFIG.map(u => u.color),
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} berkas` } }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#9CA3AF' } },
        y: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { font: { size: 11 }, color: '#9CA3AF', stepSize: 1, precision: 0 },
          min: 0,
        }
      }
    }
  })
}

// ── Export ────────────────────────────────────────────────────
const downloadExcel = () => {
  if (!startDate.value || !endDate.value) {
    errorMsg.value = 'Pilih rentang tanggal terlebih dahulu.'
    return
  }
  const params = new URLSearchParams({ start_date: startDate.value, end_date: endDate.value })
  window.location.href = `/laporan-unit/export-excel?${params.toString()}`
}
</script>