import axios from 'axios';
import * as bootstrap from 'bootstrap';
import jQuery from 'jquery';
import 'select2';
import Swal from 'sweetalert2';
import toastr from 'toastr';

// Make libraries available globally
window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;
window.axios = axios;
window.Swal = Swal;
window.toastr = toastr;

// Configure axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configure toastr
toastr.options = {
    closeButton: true,
    newestOnTop: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    preventDuplicates: false,
    showDuration: '300',
    hideDuration: '1000',
    timeOut: '5000',
    extendedTimeOut: '1000',
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut'
};
