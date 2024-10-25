@extends('admin.master')
@section('title')
    {{ __('attributes.logs') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.logs') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                               
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example3" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Logs</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- show Default Logs --}}
                                        <tr>
                                            <td>{{ __('attributes.default_logs') }}</td>
                                            <td>
                                                <a href="{{ route('admin.logs.show', 'default') }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        {{-- show Web Logs --}}
                                        <tr>
                                            <td>{{ __('attributes.web_logs') }}</td>
                                            <td>
                                                <a href="{{ route('admin.logs.show', 'web') }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        {{-- show Api Logs --}}
                                        <tr>
                                            <td>{{ __('attributes.api_logs') }}</td>
                                            <td>
                                                <a href="{{ route('admin.logs.show', 'api') }}"
                                                    class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <th>Logs</th>
                                        <th>Actions</th>
                                    </tfoot>

                                </table>



                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->

    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $("#example3").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "ordering": true, // Enable ordering
                "order": [], // No default ordering (columns are unsorted initially)
                "buttons": ["copy", "csv", "excel"],
                "pageLength": 50,
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

            }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');


        });
    </script>
@endsection
