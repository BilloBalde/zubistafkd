<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/js/feather.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('assets/plugins/sweetalert/sweetalerts.min.js') }}"></script>
<script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.0/jspdf.umd.min.js"></script>
<script>
    function showToast(message, type = 'success') {
        const icons = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
        Swal.fire({
            toast: true,
            position: 'bottom-end',
            icon: icons[type] ?? 'info',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }
</script>
<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if ($.fn.dataTable.isDataTable('.datanew')) {
            // Destroy the existing DataTable instance
            $('.datanew').DataTable().destroy();
        }

        // Initialize DataTable with export buttons
        $('.datanew').DataTable({
            dom: 'Bfrtip', // Enable the buttons
            searching : false, //
            /* buttons: [
                {
                    extend: 'excel',
                    text: '<img src="{{ asset('assets/img/icons/excel.svg') }}" alt="img">', // Replace text with image
                    titleAttr: 'Export to Excel'
                }
            ], */
            order: [],
            /* initComplete: function() {
                // Move the DataTable export buttons to the specific container
                $('.dt-buttons').appendTo('#exportButtonsContainer');
            } */
        });
    });
</script>
