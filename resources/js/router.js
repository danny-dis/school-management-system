import Vue from 'vue';
import VueRouter from 'vue-router';

// Import components
import Login from './components/auth/Login.vue';
import Dashboard from './components/Dashboard.vue';
import NotFound from './components/NotFound.vue';

// Student components
import StudentProfile from './components/student/Profile.vue';
import StudentAttendance from './components/student/Attendance.vue';
import StudentResults from './components/student/Results.vue';
import StudentTimetable from './components/student/Timetable.vue';
import StudentFees from './components/student/Fees.vue';
import StudentBooks from './components/student/Books.vue';

// Teacher components
import TeacherProfile from './components/teacher/Profile.vue';
import TeacherClasses from './components/teacher/Classes.vue';
import TeacherAttendance from './components/teacher/Attendance.vue';
import TeacherMarks from './components/teacher/Marks.vue';

// Admin components
import AdminDashboard from './components/admin/Dashboard.vue';
import AdminStudents from './components/admin/students/Index.vue';
import AdminStudentCreate from './components/admin/students/Create.vue';
import AdminStudentEdit from './components/admin/students/Edit.vue';
import AdminStudentView from './components/admin/students/View.vue';
import AdminClasses from './components/admin/classes/Index.vue';
import AdminSections from './components/admin/sections/Index.vue';
import AdminSubjects from './components/admin/subjects/Index.vue';
import AdminTeachers from './components/admin/teachers/Index.vue';
import AdminExams from './components/admin/exams/Index.vue';
import AdminReports from './components/admin/reports/Index.vue';

Vue.use(VueRouter);

// Route definitions
const routes = [
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  
  // Student routes
  {
    path: '/student/profile',
    name: 'student.profile',
    component: StudentProfile,
    meta: { requiresAuth: true, role: 'Student' }
  },
  {
    path: '/student/attendance',
    name: 'student.attendance',
    component: StudentAttendance,
    meta: { requiresAuth: true, role: 'Student' }
  },
  {
    path: '/student/results',
    name: 'student.results',
    component: StudentResults,
    meta: { requiresAuth: true, role: 'Student' }
  },
  {
    path: '/student/timetable',
    name: 'student.timetable',
    component: StudentTimetable,
    meta: { requiresAuth: true, role: 'Student' }
  },
  {
    path: '/student/fees',
    name: 'student.fees',
    component: StudentFees,
    meta: { requiresAuth: true, role: 'Student' }
  },
  {
    path: '/student/books',
    name: 'student.books',
    component: StudentBooks,
    meta: { requiresAuth: true, role: 'Student' }
  },
  
  // Teacher routes
  {
    path: '/teacher/profile',
    name: 'teacher.profile',
    component: TeacherProfile,
    meta: { requiresAuth: true, role: 'Teacher' }
  },
  {
    path: '/teacher/classes',
    name: 'teacher.classes',
    component: TeacherClasses,
    meta: { requiresAuth: true, role: 'Teacher' }
  },
  {
    path: '/teacher/attendance',
    name: 'teacher.attendance',
    component: TeacherAttendance,
    meta: { requiresAuth: true, role: 'Teacher' }
  },
  {
    path: '/teacher/marks',
    name: 'teacher.marks',
    component: TeacherMarks,
    meta: { requiresAuth: true, role: 'Teacher' }
  },
  
  // Admin routes
  {
    path: '/admin/dashboard',
    name: 'admin.dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/students',
    name: 'admin.students.index',
    component: AdminStudents,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/students/create',
    name: 'admin.students.create',
    component: AdminStudentCreate,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/students/:id/edit',
    name: 'admin.students.edit',
    component: AdminStudentEdit,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/students/:id',
    name: 'admin.students.view',
    component: AdminStudentView,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/classes',
    name: 'admin.classes.index',
    component: AdminClasses,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/sections',
    name: 'admin.sections.index',
    component: AdminSections,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/subjects',
    name: 'admin.subjects.index',
    component: AdminSubjects,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/teachers',
    name: 'admin.teachers.index',
    component: AdminTeachers,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/exams',
    name: 'admin.exams.index',
    component: AdminExams,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  {
    path: '/admin/reports',
    name: 'admin.reports.index',
    component: AdminReports,
    meta: { requiresAuth: true, role: 'Admin' }
  },
  
  // 404 route
  {
    path: '*',
    name: 'not-found',
    component: NotFound
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const loggedIn = localStorage.getItem('token');
  const userRole = localStorage.getItem('role');
  
  // Check if route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!loggedIn) {
      return next({ name: 'login' });
    }
    
    // Check if route requires specific role
    if (to.matched.some(record => record.meta.role)) {
      if (to.meta.role !== userRole) {
        return next({ name: 'dashboard' });
      }
    }
  }
  
  // Check if route is for guests only (like login)
  if (to.matched.some(record => record.meta.guest)) {
    if (loggedIn) {
      return next({ name: 'dashboard' });
    }
  }
  
  next();
});

export default router;
