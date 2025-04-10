<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h1 class="hero-title">Welcome to Zophlic School Management System</h1>
        <p class="hero-subtitle">A comprehensive solution for educational institutions to manage students, teachers, classes, and more.</p>
        <a href="#features" class="hero-button">Explore Features</a>
      </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title">Our Features</h2>
        <p class="section-subtitle">Discover the powerful tools and modules that make our school management system stand out.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="(feature, index) in features" :key="index" class="feature-card">
            <div class="feature-icon">
              <i :class="`fas fa-${feature.icon}`"></i>
            </div>
            <h3 class="feature-title">{{ feature.title }}</h3>
            <p class="feature-description">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="section-title">What Our Users Say</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
          <div v-for="(testimonial, index) in testimonials" :key="index" class="testimonial-card">
            <p class="testimonial-content">{{ testimonial.content }}</p>
            <div class="flex items-center mt-4">
              <img :src="testimonial.avatar" alt="Avatar" class="w-12 h-12 rounded-full mr-4">
              <div>
                <p class="testimonial-author">{{ testimonial.name }}</p>
                <p class="testimonial-role">{{ testimonial.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-16 bg-primary-700 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-6">Ready to transform your school management?</h2>
        <p class="text-xl mb-8 max-w-3xl mx-auto">Join thousands of educational institutions that have streamlined their operations with our system.</p>
        <a href="/contact" class="bg-white text-primary-700 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300">Contact Us Today</a>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      features: [
        {
          title: 'Student Management',
          icon: 'user-graduate',
          description: 'Comprehensive student profiles, attendance tracking, and performance monitoring.'
        },
        {
          title: 'Teacher Management',
          icon: 'chalkboard-teacher',
          description: 'Manage teacher information, assignments, and performance evaluations.'
        },
        {
          title: 'Online Learning',
          icon: 'laptop',
          description: 'Virtual classrooms, course management, and assignment submission.'
        },
        {
          title: 'Fee Management',
          icon: 'money-bill-wave',
          description: 'Streamlined fee collection, invoicing, and payment tracking.'
        },
        {
          title: 'Library Management',
          icon: 'book',
          description: 'Book inventory, lending system, and digital resource management.'
        },
        {
          title: 'Communication',
          icon: 'comments',
          description: 'Integrated messaging, notifications, and announcement system.'
        }
      ],
      testimonials: [
        {
          content: 'This system has transformed how we manage our school. The comprehensive features and user-friendly interface have made administrative tasks much easier.',
          name: 'John Smith',
          role: 'School Principal',
          avatar: '/images/testimonials/avatar1.jpg'
        },
        {
          content: 'As a teacher, I appreciate how easy it is to track attendance, manage assignments, and communicate with students and parents.',
          name: 'Sarah Johnson',
          role: 'High School Teacher',
          avatar: '/images/testimonials/avatar2.jpg'
        },
        {
          content: 'The online learning module has been a game-changer for our institution, especially during remote learning periods.',
          name: 'Michael Brown',
          role: 'IT Administrator',
          avatar: '/images/testimonials/avatar3.jpg'
        }
      ]
    };
  }
};
</script>
