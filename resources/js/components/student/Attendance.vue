<template>
  <div class="student-attendance">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Attendance</h3>
        <div class="card-tools">
          <div class="form-inline">
            <div class="form-group mr-2">
              <select class="form-control" v-model="month" @change="fetchAttendance">
                <option v-for="(name, index) in months" :key="index" :value="index + 1">{{ name }}</option>
              </select>
            </div>
            <div class="form-group">
              <select class="form-control" v-model="year" @change="fetchAttendance">
                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div v-else>
          <div class="attendance-summary mb-4">
            <div class="row">
              <div class="col-md-3">
                <div class="info-box bg-success">
                  <div class="info-box-content">
                    <span class="info-box-text">Present</span>
                    <span class="info-box-number">{{ summary.present }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box bg-danger">
                  <div class="info-box-content">
                    <span class="info-box-text">Absent</span>
                    <span class="info-box-number">{{ summary.absent }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box bg-warning">
                  <div class="info-box-content">
                    <span class="info-box-text">Late</span>
                    <span class="info-box-number">{{ summary.late }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box bg-info">
                  <div class="info-box-content">
                    <span class="info-box-text">Percentage</span>
                    <span class="info-box-number">{{ summary.percentage }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="attendance-calendar">
            <div class="table-responsive">
              <table class="table table-bordered text-center">
                <thead>
                  <tr>
                    <th v-for="day in weekDays" :key="day">{{ day }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(week, weekIndex) in calendarDays" :key="weekIndex">
                    <td v-for="(day, dayIndex) in week" :key="dayIndex" :class="getDayClass(day)">
                      <template v-if="day.date">
                        <div class="day-number">{{ day.date.getDate() }}</div>
                        <div v-if="day.status" class="day-status">
                          <span v-if="day.status === 'present'" class="badge badge-success">P</span>
                          <span v-else-if="day.status === 'absent'" class="badge badge-danger">A</span>
                          <span v-else-if="day.status === 'late'" class="badge badge-warning">L</span>
                          <span v-else-if="day.status === 'holiday'" class="badge badge-info">H</span>
                        </div>
                      </template>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="attendance-list mt-4">
            <h4>Attendance Details</h4>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="record in attendance" :key="record.date">
                    <td>{{ formatDate(record.date) }}</td>
                    <td>
                      <span v-if="record.status === 'present'" class="badge badge-success">Present</span>
                      <span v-else-if="record.status === 'absent'" class="badge badge-danger">Absent</span>
                      <span v-else-if="record.status === 'late'" class="badge badge-warning">Late</span>
                      <span v-else-if="record.status === 'holiday'" class="badge badge-info">Holiday</span>
                    </td>
                    <td>{{ record.remarks || 'N/A' }}</td>
                  </tr>
                  <tr v-if="attendance.length === 0">
                    <td colspan="3" class="text-center">No attendance records found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StudentAttendance',
  data() {
    return {
      loading: true,
      month: new Date().getMonth() + 1,
      year: new Date().getFullYear(),
      attendance: [],
      months: [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ],
      weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      years: []
    }
  },
  computed: {
    summary() {
      const present = this.attendance.filter(a => a.status === 'present').length;
      const absent = this.attendance.filter(a => a.status === 'absent').length;
      const late = this.attendance.filter(a => a.status === 'late').length;
      const total = present + absent + late;
      
      return {
        present,
        absent,
        late,
        percentage: total > 0 ? Math.round((present / total) * 100) : 0
      };
    },
    calendarDays() {
      const daysInMonth = new Date(this.year, this.month, 0).getDate();
      const firstDay = new Date(this.year, this.month - 1, 1).getDay();
      
      let days = [];
      let week = Array(7).fill(null).map(() => ({ date: null, status: null }));
      
      // Fill in the days before the first day of the month
      for (let i = 0; i < firstDay; i++) {
        week[i] = { date: null, status: null };
      }
      
      // Fill in the days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(this.year, this.month - 1, day);
        const dayOfWeek = date.getDay();
        
        // Find attendance record for this day
        const record = this.attendance.find(a => {
          const recordDate = new Date(a.date);
          return recordDate.getDate() === day;
        });
        
        week[dayOfWeek] = {
          date,
          status: record ? record.status : null
        };
        
        // Start a new week if we reach the end of the week
        if (dayOfWeek === 6 || day === daysInMonth) {
          days.push([...week]);
          week = Array(7).fill(null).map(() => ({ date: null, status: null }));
        }
      }
      
      return days;
    }
  },
  mounted() {
    this.initYears();
    this.fetchAttendance();
  },
  methods: {
    initYears() {
      const currentYear = new Date().getFullYear();
      for (let i = currentYear - 2; i <= currentYear; i++) {
        this.years.push(i);
      }
    },
    fetchAttendance() {
      this.loading = true;
      axios.get('/api/student/attendance', {
        params: {
          month: this.month,
          year: this.year
        }
      })
        .then(response => {
          this.attendance = response.data.data;
        })
        .catch(error => {
          console.error('Error fetching attendance:', error);
          this.$toastr.error('Failed to load attendance data');
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    getDayClass(day) {
      if (!day.date) return 'empty-day';
      
      const classes = ['calendar-day'];
      
      if (day.status === 'present') {
        classes.push('present-day');
      } else if (day.status === 'absent') {
        classes.push('absent-day');
      } else if (day.status === 'late') {
        classes.push('late-day');
      } else if (day.status === 'holiday') {
        classes.push('holiday-day');
      }
      
      // Check if it's today
      const today = new Date();
      if (day.date.getDate() === today.getDate() &&
          day.date.getMonth() === today.getMonth() &&
          day.date.getFullYear() === today.getFullYear()) {
        classes.push('today');
      }
      
      return classes.join(' ');
    }
  }
}
</script>

<style scoped>
.calendar-day {
  height: 60px;
  position: relative;
}

.day-number {
  position: absolute;
  top: 5px;
  left: 5px;
  font-size: 14px;
}

.day-status {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.empty-day {
  background-color: #f8f9fa;
}

.present-day {
  background-color: rgba(40, 167, 69, 0.1);
}

.absent-day {
  background-color: rgba(220, 53, 69, 0.1);
}

.late-day {
  background-color: rgba(255, 193, 7, 0.1);
}

.holiday-day {
  background-color: rgba(23, 162, 184, 0.1);
}

.today {
  border: 2px solid #007bff;
}
</style>
