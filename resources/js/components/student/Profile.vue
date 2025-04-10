<template>
  <div class="student-profile">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Student Profile</h3>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div v-else>
          <div class="row">
            <div class="col-md-3 text-center">
              <div class="profile-img">
                <img :src="student.photo || '/images/default-avatar.png'" alt="Profile Image" class="img-fluid rounded-circle">
              </div>
              <h4 class="mt-3">{{ student.name }}</h4>
              <p class="text-muted">{{ student.registration ? `Class: ${student.registration.class.name}` : '' }}</p>
              <p class="text-muted">{{ student.registration ? `Section: ${student.registration.section.name}` : '' }}</p>
              <p class="text-muted">{{ student.registration ? `Roll: ${student.registration.roll_no}` : '' }}</p>
            </div>
            <div class="col-md-9">
              <ul class="nav nav-tabs" id="profileTab" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">Personal Info</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="parents-tab" data-toggle="tab" href="#parents" role="tab" aria-controls="parents" aria-selected="false">Parents</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="false">Address</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">Edit Profile</a>
                </li>
              </ul>
              <div class="tab-content" id="profileTabContent">
                <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                  <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <th width="30%">Registration No</th>
                          <td>{{ student.registration ? student.registration.regi_no : 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Name</th>
                          <td>{{ student.name }}</td>
                        </tr>
                        <tr>
                          <th>Email</th>
                          <td>{{ student.email || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Phone</th>
                          <td>{{ student.phone_no || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Date of Birth</th>
                          <td>{{ formatDate(student.dob) }}</td>
                        </tr>
                        <tr>
                          <th>Gender</th>
                          <td>{{ getGender(student.gender) }}</td>
                        </tr>
                        <tr>
                          <th>Blood Group</th>
                          <td>{{ getBloodGroup(student.blood_group) }}</td>
                        </tr>
                        <tr>
                          <th>Religion</th>
                          <td>{{ getReligion(student.religion) }}</td>
                        </tr>
                        <tr>
                          <th>Nationality</th>
                          <td>{{ student.nationality || 'N/A' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="parents" role="tabpanel" aria-labelledby="parents-tab">
                  <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <th width="30%">Father's Name</th>
                          <td>{{ student.father_name || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Father's Phone</th>
                          <td>{{ student.father_phone_no || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Mother's Name</th>
                          <td>{{ student.mother_name || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Mother's Phone</th>
                          <td>{{ student.mother_phone_no || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Guardian</th>
                          <td>{{ student.guardian || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Guardian's Phone</th>
                          <td>{{ student.guardian_phone_no || 'N/A' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                  <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                      <tbody>
                        <tr>
                          <th width="30%">Present Address</th>
                          <td>{{ student.present_address || 'N/A' }}</td>
                        </tr>
                        <tr>
                          <th>Permanent Address</th>
                          <td>{{ student.permanent_address || 'N/A' }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                  <form @submit.prevent="updateProfile" class="mt-3">
                    <div class="form-group">
                      <label for="phone_no">Phone Number</label>
                      <input type="text" class="form-control" id="phone_no" v-model="form.phone_no">
                    </div>
                    <div class="form-group">
                      <label for="present_address">Present Address</label>
                      <textarea class="form-control" id="present_address" rows="3" v-model="form.present_address"></textarea>
                    </div>
                    <div class="form-group">
                      <label for="permanent_address">Permanent Address</label>
                      <textarea class="form-control" id="permanent_address" rows="3" v-model="form.permanent_address"></textarea>
                    </div>
                    <div class="form-group">
                      <label for="photo">Profile Photo</label>
                      <input type="file" class="form-control-file" id="photo" @change="handleFileUpload">
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="updating">
                      <span v-if="updating" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                      Update Profile
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StudentProfile',
  data() {
    return {
      loading: true,
      updating: false,
      student: {},
      form: {
        phone_no: '',
        present_address: '',
        permanent_address: '',
        photo: null
      }
    }
  },
  mounted() {
    this.fetchProfile();
  },
  methods: {
    fetchProfile() {
      this.loading = true;
      axios.get('/api/student/profile')
        .then(response => {
          this.student = response.data.data;
          this.form.phone_no = this.student.phone_no || '';
          this.form.present_address = this.student.present_address || '';
          this.form.permanent_address = this.student.permanent_address || '';
        })
        .catch(error => {
          console.error('Error fetching profile:', error);
          this.$toastr.error('Failed to load profile data');
        })
        .finally(() => {
          this.loading = false;
        });
    },
    updateProfile() {
      this.updating = true;
      
      const formData = new FormData();
      formData.append('phone_no', this.form.phone_no);
      formData.append('present_address', this.form.present_address);
      formData.append('permanent_address', this.form.permanent_address);
      
      if (this.form.photo) {
        formData.append('photo', this.form.photo);
      }
      
      axios.post('/api/student/profile', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
        .then(response => {
          this.student = response.data.data;
          this.$toastr.success('Profile updated successfully');
        })
        .catch(error => {
          console.error('Error updating profile:', error);
          this.$toastr.error('Failed to update profile');
        })
        .finally(() => {
          this.updating = false;
        });
    },
    handleFileUpload(event) {
      this.form.photo = event.target.files[0];
    },
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString();
    },
    getGender(gender) {
      const genders = {
        1: 'Male',
        2: 'Female'
      };
      return genders[gender] || 'N/A';
    },
    getBloodGroup(bloodGroup) {
      const bloodGroups = {
        1: 'A+',
        2: 'A-',
        3: 'B+',
        4: 'B-',
        5: 'AB+',
        6: 'AB-',
        7: 'O+',
        8: 'O-'
      };
      return bloodGroups[bloodGroup] || 'N/A';
    },
    getReligion(religion) {
      const religions = {
        1: 'Islam',
        2: 'Hinduism',
        3: 'Christianity',
        4: 'Buddhism',
        5: 'Other'
      };
      return religions[religion] || 'N/A';
    }
  }
}
</script>

<style scoped>
.profile-img {
  width: 150px;
  height: 150px;
  margin: 0 auto;
  overflow: hidden;
  border-radius: 50%;
  border: 5px solid #f8f9fa;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.profile-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
</style>
