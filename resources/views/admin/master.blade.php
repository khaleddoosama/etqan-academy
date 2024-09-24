<!DOCTYPE html>
<html lang={{ str_replace('_', '-', app()->getLocale()) }}>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <!-- ==================== styles ====================== -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- icon --}}
    <link rel="icon" href="{{ asset('asset/logo.jpg') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('asset/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet"
        href="{{ asset('asset/admin/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('asset/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('asset/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet"
        href="{{ asset('asset/admin/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/bs-stepper/css/bs-stepper.min.css') }}">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/dropzone/min/dropzone.min.css') }}">
    <!-- bs-custom-file-input -->
    <script src="{{ asset('asset/admin/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('asset/admin/dist/css/adminlte.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/toastr/toastr.min.css') }}">

    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/summernote/summernote-bs4.min.css') }}">

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
        integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}

    <style>
        .note-editable ul {
            list-style-type: disc;
            /* التأكد من أن نوع القائمة غير المرتبة هو نقطي */
            list-style-position: inside;
            /* يمكن تغيير هذا الخيار حسب رغبتك */
        }

        .note-editable ol {
            list-style-type: decimal;
            /* التأكد من أن نوع القائمة المرتبة هو رقمي */
            list-style-position: inside;
            /* يمكن تغيير هذا الخيار حسب رغبتك */
        }

        .note-editable li {
            display: list-item;
            /* تأكد من أن العناصر تظهر كعناصر قائمة */
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('asset/logo.jpg') }}" alt="EtqanLogo" height="60"
                width="60" style="border-radius: 50%">
        </div>


        <!-- /.navbar -->
        @include('admin.body.header')
        <!-- Main Sidebar Container -->
        @include('admin.body.sidebar')

        <!-- Content Wrapper. Contains page content -->
        @yield('content')
        <!-- /.content-wrapper -->
        @include('admin.body.footer')


    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('asset/admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <!-- Bootstrap 4 -->
    <script src="{{ asset('asset/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('asset/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- jquery-validation -->
    <script src="{{ asset('asset/admin/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('asset/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{ asset('asset/admin/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('asset/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/admin/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('asset/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
    </script>
    <!-- Bootstrap Switch -->
    <script src="{{ asset('asset/admin/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
    <!-- BS-Stepper -->
    <script src="{{ asset('asset/admin/plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
    <!-- dropzonejs -->
    <script src="{{ asset('asset/admin/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('asset/admin/plugins/chart.js/Chart.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('asset/admin/dist/js/adminlte.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('asset/admin/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('asset/admin/plugins/summernote/lang/summernote-ar-AR.min.js') }}"></script>
    <!-- FLOT CHARTS -->
    <script src="{{ asset('asset/admin/plugins/flot/jquery.flot.js') }}"></script>
    <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
    <script src="{{ asset('asset/admin/plugins/flot/plugins/jquery.flot.resize.js') }}"></script>
    <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
    <script src="{{ asset('asset/admin/plugins/flot/plugins/jquery.flot.pie.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('asset/admin/dist/js/demo.js') }}"></script>
    <!-- Page specific script -->

    {{-- pusher --}}
    {{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = true;

      var pusher = new Pusher('76ac8bd1b72f06108985', {
        cluster: 'mt1'
      });

    //   var channel = pusher.subscribe('my-channel');
    //   channel.bind('my-event', function(data) {
    //     alert(JSON.stringify(data));
    //   });
    </script> --}}
    <script>
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "ordering": true, // Enable ordering
                "order": [], // No default ordering (columns are unsorted initially)                "buttons": ["copy", "csv", "excel", "print", "colvis"],
                "language": {
                    "emptyTable": "{{ __('datatable.no_data_available_in_table') }}",
                    "lengthMenu": "{{ __('datatable.show _MENU_ entries') }}",
                    "search": "{{ __('datatable.search') }}:",
                    "zeroRecords": "{{ __('datatable.no_matching_records_found') }}",
                    "paginate": {
                        "next": "{{ __('datatable.next') }}",
                        "previous": "{{ __('datatable.previous') }}"
                    },
                    "info": "{{ __('datatable.showing from _START_ to _END_ of _TOTAL_ entries') }}",
                    "infoEmpty": "{{ __('datatable.showing 0 to 0 of 0 entries') }}",
                    "infoFiltered": "({{ __('datatable.filtered from _MAX_ total entries') }})",
                    "thousands": ",",
                    "loadingRecords": "{{ __('datatable.loading...') }}",
                    "processing": "{{ __('datatable.processing...') }}",
                },

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //Datemask dd/mm/yyyy
            $('#datemask').inputmask('dd/mm/yyyy', {
                'placeholder': 'dd/mm/yyyy'
            })
            //Datemask2 mm/dd/yyyy
            $('#datemask2').inputmask('mm/dd/yyyy', {
                'placeholder': 'mm/dd/yyyy'
            })
            //Money Euro
            $('[data-mask]').inputmask()

            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'L'
            });

            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                }
            });

            //Date range picker
            $('#reservation').daterangepicker()
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            })
            //Date range as a button
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                        'MMMM D, YYYY'))
                }
            )

            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })

            //Bootstrap Duallistbox
            $('.duallistbox').bootstrapDualListbox()

            //Colorpicker
            $('.my-colorpicker1').colorpicker()
            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            })

            $("input[data-bootstrap-switch]").each(function() {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            })

        })
        // BS-Stepper Init
        document.addEventListener('DOMContentLoaded', function() {
            window.stepper = new Stepper(document.querySelector('.bs-stepper'))
        })
    </script>
    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
    <!-- Toastr -->
    <script src="{{ asset('asset/admin/plugins/toastr/toastr.min.js') }}"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.js.map"></script> --}}


    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script> --}}

    <!-- ==================== Script ====================== -->
    <!----------------- Charts JS ------------------->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>


    <!---  ============= ionicons ============== --->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>



    <script>
        $(document).ready(function() {
            $(".delete-button").on("click", function(e) {
                e.preventDefault();
                // Display a SweetAlert2 confirmation dialog
                Swal.fire({
                    title: ` {{ __('main.Are you sure') }}?`,
                    text: `{!! __("main.You won't be able to revert this") !!}!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `{{ __('main.Yes, delete it') }}!`
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Submit the delete form
                        e.target.closest('#delete-form').submit();
                        console.log("Item deleted");
                    } else {
                        console.log("Item not deleted");
                    }
                });
            });
        });
    </script>

    <script>
        // when user selects a file #exampleInputFile must be show in img with id = #profilePicture
        $(document).ready(function() {
            $('#input-image').on('change', function(event) {
                const input = event.target;
                const img = $('#profilePicture');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        img.attr('src', e.target.result);
                        img.removeClass('d-none');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
        });
    </script>
    <script>
        // call ajax to mark as read
        $(document).on('click', '.notification', function(e) {
            e.preventDefault();
            var notification_id = $(this).data('id');
            var notification_url = $(this).data('url');
            $.ajax({
                url: "{{ route('notification.markAsRead') }}",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "notification_id": notification_id
                },
                success: function(data) {
                    console.log('success');
                    window.location.href = notification_url;
                },
                error: function(error) {
                    console.log('error');
                }
            });
        });
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.summernote').each(function() {
                $(this).summernote({
                    lang: 'ar-AR', // تعيين اللغة العربية
                    height: 200, // تعيين ارتفاع المحرر
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    placeholder: 'أدخل وصف المحتوى هنا...',
                    direction: 'rtl' // تعيين اتجاه النص إلى اليمين إلى اليسار
                });
            });

        });
    </script>

    @yield('scripts')
</body>

</html>
