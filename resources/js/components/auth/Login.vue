<template>
  <div class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <img src="/images/logo.png" alt="Zophlic Logo" class="brand-image img-circle elevation-3" style="opacity: .8; width: 80px;">
        <h1><b>Zophlic</b> SMS</h1>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Sign in to start your session</p>

          <form @submit.prevent="login">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Username or Email" v-model="form.username" :class="{ 'is-invalid': errors.username }">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
              <div v-if="errors.username" class="invalid-feedback">
                {{ errors.username }}
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" placeholder="Password" v-model="form.password" :class="{ 'is-invalid': errors.password }">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
              <div v-if="errors.password" class="invalid-feedback">
                {{ errors.password }}
              </div>
            </div>
            <div class="row">
              <div class="col-8">
                <div class="icheck-primary">
                  <input type="checkbox" id="remember" v-model="form.remember">
                  <label for="remember">
                    Remember Me
                  </label>
                </div>
              </div>
              <!-- /.col -->
              <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  Sign In
                </button>
              </div>
              <!-- /.col -->
            </div>
          </form>

          <p class="mb-1">
            <a href="#" @click.prevent="forgotPassword">I forgot my password</a>
          </p>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'Login',
  data() {
    return {
      form: {
        username: '',
        password: '',
        remember: false
      },
      errors: {},
      loading: false
    };
  },
  computed: {
    ...mapGetters(['isLoggedIn', 'error'])
  },
  watch: {
    isLoggedIn(value) {
      if (value) {
        this.redirectToDashboard();
      }
    }
  },
  mounted() {
    if (this.isLoggedIn) {
      this.redirectToDashboard();
    }
  },
  methods: {
    login() {
      this.errors = {};
      
      // Validate form
      if (!this.form.username) {
        this.errors.username = 'Username or email is required';
      }
      
      if (!this.form.password) {
        this.errors.password = 'Password is required';
      }
      
      if (Object.keys(this.errors).length > 0) {
        return;
      }
      
      this.loading = true;
      
      this.$store.dispatch('login', this.form)
        .then(() => {
          this.redirectToDashboard();
        })
        .catch(error => {
          if (error.response && error.response.data) {
            const { errors } = error.response.data;
            
            if (errors) {
              this.errors = errors;
            } else if (error.response.data.message) {
              this.errors.username = error.response.data.message;
            }
          } else {
            this.errors.username = 'An error occurred during login. Please try again.';
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    redirectToDashboard() {
      const role = this.$store.getters.userRole;
      
      if (role === 'Admin') {
        this.$router.push('/admin/dashboard');
      } else if (role === 'Teacher') {
        this.$router.push('/teacher/profile');
      } else if (role === 'Student') {
        this.$router.push('/student/profile');
      } else {
        this.$router.push('/');
      }
    },
    forgotPassword() {
      alert('Please contact your administrator to reset your password.');
    }
  }
};
</script>

<style scoped>
.login-page {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f4f6f9;
}

.login-logo {
  text-align: center;
  margin-bottom: 20px;
}

.login-logo h1 {
  margin-top: 10px;
  font-size: 28px;
}
</style>
