<template>
  <AppLayout>
    <div class="p-6 max-w-7xl mx-auto">
      <div class="mb-6 bg-white rounded-lg shadow p-4">
        <h2 class="text-xl font-semibold mb-4">Analisis Aktivitas Pengguna</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
            <input
              type="date"
              v-model="startDate"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
            <input
              type="date"
              v-model="endDate"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
          </div>
        </div>
        
        <div class="flex space-x-4">
          <button
            @click="fetchData"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
          >
            Tampilkan Data
          </button>
          
          <button
            @click="downloadExcel"
            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700"
          >
            Unduh Excel
          </button>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-4">
        <div ref="chartContainer" class="h-96"></div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import * as echarts from 'echarts'
import AppLayout from "@/Layouts/AppLayout.vue"

const startDate = ref('')
const endDate = ref('')
const chartContainer = ref(null)
let chart = null

onMounted(() => {
  chart = echarts.init(chartContainer.value)
  window.addEventListener('resize', () => chart?.resize())
})

const fetchData = async () => {
  if (!startDate.value || !endDate.value) {
    alert('Silakan pilih rentang tanggal terlebih dahulu')
    return
  }

  try {
    const response = await fetch('/api/date-range-data', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        start_date: startDate.value,
        end_date: endDate.value
      })
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    
    if (result.success) {
      updateChart(result.data, result.total_activities)
    } else {
      alert('Gagal mengambil data: ' + (result.message || 'Terjadi kesalahan'))
    }
  } catch (error) {
    console.error('Error:', error)
    alert('Terjadi kesalahan saat mengambil data')
  }
}

const downloadExcel = () => {
  if (!startDate.value || !endDate.value) {
    alert('Silakan pilih rentang tanggal terlebih dahulu')
    return
  }

  const params = new URLSearchParams({
    start_date: startDate.value,
    end_date: endDate.value
  })

  window.location.href = `/laporan-unit/export-excel?${params.toString()}`
}

const updateChart = (data, totalActivities) => {
  const dates = [...new Set(data.map(item => item.date))].sort()
  const statuses = [...new Set(data.map(item => item.status))]
  
  const series = statuses.map(status => {
    const statusData = dates.map(date => {
      const entry = data.find(item => item.date === date && item.status === status)
      return entry ? entry.count : 0
    })
    
    return {
      name: status,
      type: 'line',
      smooth: true,
      data: statusData,
      areaStyle: {
        opacity: 0.1
      }
    }
  })

  const option = {
    title: {
      text: 'Grafik Aktivitas Pengguna',
      subtext: `Total Aktivitas: ${totalActivities}`,
      left: 'center'
    },
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'cross'
      }
    },
    legend: {
      data: statuses.map(status => translateStatus(status)),
      top: 50
    },
    grid: {
      top: 100,
      left: '3%',
      right: '4%',
      bottom: '3%',
      containLabel: true
    },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: dates.map(date => formatDate(date)),
      axisLabel: {
        rotate: 45
      }
    },
    yAxis: {
      type: 'value',
      name: 'Jumlah Aktivitas'
    },
    series: series.map(s => ({
      ...s,
      name: translateStatus(s.name)
    }))
  }

  chart.setOption(option)
}

const translateStatus = (status) => {
  const translations = {
    'pending': 'Menunggu',
    'in_progress': 'Sedang Diproses',
    'completed': 'Selesai',
    'rejected': 'Ditolak'
  }
  return translations[status] || status
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}
</script>