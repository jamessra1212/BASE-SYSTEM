import './bootstrap';
import jQuery from 'jquery';
import * as bootstrap from 'bootstrap';
import toastr from 'toastr';
import Swal from 'sweetalert2'; // <-- 1. ADD THIS IMPORT
import 'admin-lte';

// Assign to window for global access
window.$ = window.jQuery = jQuery;
window.bootstrap = bootstrap;
window.toastr = toastr;
window.Swal = Swal;

// 2. Explicit DataTables Sequence
import DataTable from 'datatables.net-bs5';
window.DataTable = DataTable;

import 'datatables.net-buttons-bs5';
import 'datatables.net-select-bs5';

import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';

// 3. Configure Toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "4000",
    "extendedTimeOut": "1000"
};

// 4. Modern Bootstrap Dropdown handling
document.addEventListener('DOMContentLoaded', () => {
    const dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));
});
