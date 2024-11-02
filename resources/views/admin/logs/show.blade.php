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
                                <form action="{{ route('admin.logs.bulk_delete', $type) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        title="{{ __('buttons.delete_all') }}">
                                        <i class="fas fa-trash"></i> {{ __('buttons.delete_all') }}
                                    </button>
                                </form>
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
                                        @foreach ($logs as $log)
                                            <div>
                                                <tr>
                                                    <td>
                                                        #{{ $log->id }} -
                                                        {!! $log->description !!}
                                                    </td>

                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            title="{{ __('main.show') }}" data-toggle="modal"
                                                            data-target="#show-{{ $log->id }}">
                                                            <i class="fas fa-eye fa-fw"></i>
                                                        </button>
                                                    </td>

                                                    <!-- Modal -->
                                                    <div class="modal fade show" id="show-{{ $log->id }}"
                                                        aria-modal="true" role="dialog">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">{{ __('main.show') }} {{ ucfirst($log->event) }}</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body text-left">
                                                                    @php
                                                                        $logData = json_decode($log->properties, true);
                                                                    @endphp
                                                                    <p><strong>ID:</strong> {{ $log->id }}</p>
                                                                    <p><strong>Event:</strong> {{ $log->event }}</p>
                                                                    <p><strong>by:</strong> {{ $log->causer?->name }}</p>
                                                                    <p><strong>Created at:</strong> {{ $log->created_at }}</p>
                                                                    <p><strong>Table:</strong> {{ $log->subject_type }}</p>
                                                                    <p><strong>Table ID:</strong> {{ $log->subject_id }}</p>
                                                                    @if (is_array($logData))
                                                                        <ul class="list-group">
                                                                            @foreach ($logData as $key => $value)
                                                                                <li class="list-group-item">
                                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                    @if (is_array($value))
                                                                                        <pre>{!! json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}</pre>
                                                                                    @else
                                                                                        {!! $value !!}
                                                                                    @endif
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <p>{{ $log->properties }}</p>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <x-custom.close-modal-button />
                                                                </div>
                                                            </div>
                                                            <!-- /.modal-content -->
                                                        </div>
                                                        <!-- /.modal-dialog -->
                                                    </div>

                                                </tr>
                                            </div>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <th>ID</th>
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
