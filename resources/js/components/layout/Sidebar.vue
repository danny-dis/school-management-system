<template>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <router-link to="/" class="brand-link">
      <img src="/images/logo.png" alt="Zophlic Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Zophlic SMS</span>
    </router-link>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img :src="userAvatar" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ user.name }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Dashboard -->
          <li class="nav-item">
            <router-link to="/" class="nav-link" :class="{ active: isActive('/') }">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </router-link>
          </li>
          
          <!-- Student Menu -->
          <template v-if="user.role === 'Student'">
            <li class="nav-item">
              <router-link to="/student/profile" class="nav-link" :class="{ active: isActive('/student/profile') }">
                <i class="nav-icon fas fa-user"></i>
                <p>Profile</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/student/attendance" class="nav-link" :class="{ active: isActive('/student/attendance') }">
                <i class="nav-icon fas fa-calendar-check"></i>
                <p>Attendance</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/student/results" class="nav-link" :class="{ active: isActive('/student/results') }">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Results</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/student/timetable" class="nav-link" :class="{ active: isActive('/student/timetable') }">
                <i class="nav-icon fas fa-calendar-alt"></i>
                <p>Timetable</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/student/fees" class="nav-link" :class="{ active: isActive('/student/fees') }">
                <i class="nav-icon fas fa-money-bill"></i>
                <p>Fees</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/student/books" class="nav-link" :class="{ active: isActive('/student/books') }">
                <i class="nav-icon fas fa-book"></i>
                <p>Library</p>
              </router-link>
            </li>
          </template>
          
          <!-- Teacher Menu -->
          <template v-if="user.role === 'Teacher'">
            <li class="nav-item">
              <router-link to="/teacher/profile" class="nav-link" :class="{ active: isActive('/teacher/profile') }">
                <i class="nav-icon fas fa-user"></i>
                <p>Profile</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/teacher/classes" class="nav-link" :class="{ active: isActive('/teacher/classes') }">
                <i class="nav-icon fas fa-chalkboard"></i>
                <p>My Classes</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/teacher/attendance" class="nav-link" :class="{ active: isActive('/teacher/attendance') }">
                <i class="nav-icon fas fa-calendar-check"></i>
                <p>Attendance</p>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/teacher/marks" class="nav-link" :class="{ active: isActive('/teacher/marks') }">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Marks</p>
              </router-link>
            </li>
          </template>
          
          <!-- Admin Menu -->
          <template v-if="user.role === 'Admin'">
            <li class="nav-item">
              <router-link to="/admin/dashboard" class="nav-link" :class="{ active: isActive('/admin/dashboard') }">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
              </router-link>
            </li>
            
            <!-- Academic Menu -->
            <li class="nav-item has-treeview" :class="{ 'menu-open': isActiveGroup('/admin/students', '/admin/classes', '/admin/sections', '/admin/subjects') }">
              <a href="#" class="nav-link" :class="{ active: isActiveGroup('/admin/students', '/admin/classes', '/admin/sections', '/admin/subjects') }">
                <i class="nav-icon fas fa-graduation-cap"></i>
                <p>
                  Academic
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link to="/admin/students" class="nav-link" :class="{ active: isActive('/admin/students') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Students</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/classes" class="nav-link" :class="{ active: isActive('/admin/classes') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Classes</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/sections" class="nav-link" :class="{ active: isActive('/admin/sections') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Sections</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/subjects" class="nav-link" :class="{ active: isActive('/admin/subjects') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Subjects</p>
                  </router-link>
                </li>
              </ul>
            </li>
            
            <!-- Staff Menu -->
            <li class="nav-item has-treeview" :class="{ 'menu-open': isActiveGroup('/admin/teachers', '/admin/employees') }">
              <a href="#" class="nav-link" :class="{ active: isActiveGroup('/admin/teachers', '/admin/employees') }">
                <i class="nav-icon fas fa-users"></i>
                <p>
                  Staff
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link to="/admin/teachers" class="nav-link" :class="{ active: isActive('/admin/teachers') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Teachers</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/employees" class="nav-link" :class="{ active: isActive('/admin/employees') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Employees</p>
                  </router-link>
                </li>
              </ul>
            </li>
            
            <!-- Examination Menu -->
            <li class="nav-item">
              <router-link to="/admin/exams" class="nav-link" :class="{ active: isActive('/admin/exams') }">
                <i class="nav-icon fas fa-file-alt"></i>
                <p>Examinations</p>
              </router-link>
            </li>
            
            <!-- Reports Menu -->
            <li class="nav-item">
              <router-link to="/admin/reports" class="nav-link" :class="{ active: isActive('/admin/reports') }">
                <i class="nav-icon fas fa-chart-line"></i>
                <p>Reports</p>
              </router-link>
            </li>
            
            <!-- Settings Menu -->
            <li class="nav-item has-treeview" :class="{ 'menu-open': isActiveGroup('/admin/settings', '/admin/modules', '/admin/license') }">
              <a href="#" class="nav-link" :class="{ active: isActiveGroup('/admin/settings', '/admin/modules', '/admin/license') }">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                  Settings
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <router-link to="/admin/settings" class="nav-link" :class="{ active: isActive('/admin/settings') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>General Settings</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/modules" class="nav-link" :class="{ active: isActive('/admin/modules') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Module Management</p>
                  </router-link>
                </li>
                <li class="nav-item">
                  <router-link to="/admin/license" class="nav-link" :class="{ active: isActive('/admin/license') }">
                    <i class="far fa-circle nav-icon"></i>
                    <p>License Management</p>
                  </router-link>
                </li>
              </ul>
            </li>
          </template>
          
          <!-- Logout -->
          <li class="nav-item">
            <a href="#" class="nav-link" @click.prevent="logout">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'SidebarComponent',
  computed: {
    ...mapGetters(['currentUser']),
    user() {
      return this.currentUser || {};
    },
    userAvatar() {
      if (this.user && this.user.photo) {
        return this.user.photo;
      }
      return '/images/default-avatar.png';
    }
  },
  methods: {
    isActive(path) {
      return this.$route.path === path;
    },
    isActiveGroup(...paths) {
      return paths.some(path => this.$route.path.startsWith(path));
    },
    logout() {
      this.$store.dispatch('logout')
        .then(() => {
          this.$router.push('/login');
        })
        .catch(error => {
          console.error('Logout error:', error);
        });
    }
  }
};
</script>
