<template>
  <div class="dashboard">
    <div class="row">
      <div class="col-md-3 col-sm-6 col-12" v-for="(stat, index) in stats" :key="index">
        <div class="card" :class="`border-left-${stat.color}`">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="text-xs font-weight-bold text-uppercase mb-1">{{ stat.title }}</div>
                <div class="h5 mb-0 font-weight-bold">{{ stat.value }}</div>
              </div>
              <div class="col-auto">
                <i :class="`fas fa-${stat.icon} fa-2x text-gray-300`"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row mt-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h6 class="m-0 font-weight-bold">Attendance Overview</h6>
          </div>
          <div class="card-body">
            <canvas ref="attendanceChart"></canvas>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h6 class="m-0 font-weight-bold">Student Distribution</h6>
          </div>
          <div class="card-body">
            <canvas ref="distributionChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart } from 'chart.js/auto';

export default {
  data() {
    return {
      stats: [
        { title: 'Students', value: 0, icon: 'users', color: 'primary' },
        { title: 'Teachers', value: 0, icon: 'chalkboard-teacher', color: 'success' },
        { title: 'Classes', value: 0, icon: 'school', color: 'info' },
        { title: 'Revenue', value: '$0', icon: 'dollar-sign', color: 'warning' }
      ],
      attendanceChart: null,
      distributionChart: null
    };
  },
  mounted() {
    this.fetchDashboardData();
    this.initCharts();
  },
  methods: {
    fetchDashboardData() {
      axios.get('/api/admin/dashboard')
        .then(response => {
          const data = response.data.data;
          this.stats[0].value = data.students_count;
          this.stats[1].value = data.teachers_count;
          this.stats[2].value = data.classes_count;
          this.stats[3].value = data.revenue;
          
          // Update charts with new data
          this.updateCharts(data);
        })
        .catch(error => {
          console.error('Error fetching dashboard data:', error);
        });
    },
    initCharts() {
      // Initialize attendance chart
      this.attendanceChart = new Chart(this.$refs.attendanceChart, {
        type: 'line',
        data: {
          labels: ['January', 'February', 'March', 'April', 'May', 'June'],
          datasets: [{
            label: 'Student Attendance',
            data: [65, 70, 75, 80, 78, 82],
            borderColor: 'rgba(78, 115, 223, 1)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
      
      // Initialize distribution chart
      this.distributionChart = new Chart(this.$refs.distributionChart, {
        type: 'doughnut',
        data: {
          labels: ['Male', 'Female'],
          datasets: [{
            data: [55, 45],
            backgroundColor: ['#4e73df', '#1cc88a'],
            hoverBackgroundColor: ['#2e59d9', '#17a673'],
            hoverBorderColor: 'rgba(234, 236, 244, 1)',
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '70%'
        }
      });
    },
    updateCharts(data) {
      if (data.attendance_data && this.attendanceChart) {
        this.attendanceChart.data.labels = data.attendance_data.labels;
        this.attendanceChart.data.datasets[0].data = data.attendance_data.values;
        this.attendanceChart.update();
      }
      
      if (data.gender_distribution && this.distributionChart) {
        this.distributionChart.data.datasets[0].data = [
          data.gender_distribution.male,
          data.gender_distribution.female
        ];
        this.distributionChart.update();
      }
    }
  }
};
</script>

<style scoped>
.border-left-primary {
  border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
  border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
  border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
  border-left: 0.25rem solid #f6c23e !important;
}
</style>
