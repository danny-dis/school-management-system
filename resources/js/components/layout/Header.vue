<template>
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <router-link to="/" class="nav-link">Home</router-link>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span v-if="unreadNotifications.length > 0" class="badge badge-warning navbar-badge">{{ unreadNotifications.length }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">{{ unreadNotifications.length }} Notifications</span>
          <div class="dropdown-divider"></div>
          <template v-if="unreadNotifications.length > 0">
            <a v-for="notification in unreadNotifications.slice(0, 5)" :key="notification.id" href="#" class="dropdown-item">
              <i :class="getNotificationIcon(notification.type)"></i> {{ notification.title }}
              <span class="float-right text-muted text-sm">{{ formatTimeAgo(notification.created_at) }}</span>
            </a>
            <div class="dropdown-divider"></div>
          </template>
          <template v-else>
            <a href="#" class="dropdown-item">
              <i class="fas fa-info-circle mr-2"></i> No new notifications
            </a>
            <div class="dropdown-divider"></div>
          </template>
          <router-link to="/notifications" class="dropdown-item dropdown-footer">See All Notifications</router-link>
        </div>
      </li>
      
      <!-- User Dropdown Menu -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <img :src="userAvatar" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline">{{ user.name }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- User image -->
          <li class="user-header bg-primary">
            <img :src="userAvatar" class="img-circle elevation-2" alt="User Image">
            <p>
              {{ user.name }}
              <small>{{ user.role }}</small>
            </p>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer">
            <router-link :to="profileLink" class="btn btn-default btn-flat">Profile</router-link>
            <a href="#" class="btn btn-default btn-flat float-right" @click.prevent="logout">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'HeaderComponent',
  data() {
    return {
      unreadNotifications: []
    };
  },
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
    },
    profileLink() {
      if (this.user.role === 'Student') {
        return '/student/profile';
      } else if (this.user.role === 'Teacher') {
        return '/teacher/profile';
      } else if (this.user.role === 'Admin') {
        return '/admin/profile';
      }
      return '/profile';
    }
  },
  mounted() {
    this.fetchNotifications();
    // Fetch notifications every minute
    this.notificationInterval = setInterval(this.fetchNotifications, 60000);
  },
  beforeDestroy() {
    clearInterval(this.notificationInterval);
  },
  methods: {
    fetchNotifications() {
      axios.get('/api/notifications/unread')
        .then(response => {
          this.unreadNotifications = response.data.data;
        })
        .catch(error => {
          console.error('Error fetching notifications:', error);
        });
    },
    getNotificationIcon(type) {
      const icons = {
        'info': 'fas fa-info-circle mr-2 text-info',
        'success': 'fas fa-check-circle mr-2 text-success',
        'warning': 'fas fa-exclamation-triangle mr-2 text-warning',
        'error': 'fas fa-times-circle mr-2 text-danger'
      };
      return icons[type] || icons.info;
    },
    formatTimeAgo(timestamp) {
      const now = new Date();
      const date = new Date(timestamp);
      const seconds = Math.floor((now - date) / 1000);
      
      let interval = Math.floor(seconds / 31536000);
      if (interval >= 1) {
        return interval + 'y ago';
      }
      
      interval = Math.floor(seconds / 2592000);
      if (interval >= 1) {
        return interval + 'mo ago';
      }
      
      interval = Math.floor(seconds / 86400);
      if (interval >= 1) {
        return interval + 'd ago';
      }
      
      interval = Math.floor(seconds / 3600);
      if (interval >= 1) {
        return interval + 'h ago';
      }
      
      interval = Math.floor(seconds / 60);
      if (interval >= 1) {
        return interval + 'm ago';
      }
      
      return Math.floor(seconds) + 's ago';
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

<style scoped>
.user-image {
  width: 25px;
  height: 25px;
  margin-right: 5px;
}

.user-header img {
  width: 90px;
  height: 90px;
}
</style>
