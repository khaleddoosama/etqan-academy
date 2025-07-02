@extends('admin.master')
@section('title')
{{ __('attributes.payment_details') }}
@endsection
@section('styles')
<!-- remove paddind and margin in table -->

@endsection
@section('content')
<div class="content-wrapper">
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ __('attributes.payment_details') }}" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            {{-- Instapay Statistics --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ \App\Models\Payment::where('gateway', 'instapay')->where('status', 'pending')->count() }}</h3>
                            <p>Pending Instapay</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" onclick="$('#filter-gateway').val('instapay'); $('#filter-status').val('pending'); $('#table').DataTable().draw();" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ \App\Models\Payment::where('gateway', 'instapay')->where('status', 'paid')->count() }}</h3>
                            <p>Approved Instapay</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <a href="#" onclick="$('#filter-gateway').val('instapay'); $('#filter-status').val('paid'); $('#table').DataTable().draw();" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Payment::where('gateway', 'instapay')->count() }}</h3>
                            <p>Total Instapay</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <a href="#" onclick="$('#filter-gateway').val('instapay'); $('#filter-status').val(''); $('#table').DataTable().draw();" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ \App\Models\Payment::where('gateway', 'fawaterak')->count() }}</h3>
                            <p>Fawaterak Payments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <a href="#" onclick="$('#filter-gateway').val('fawaterak'); $('#filter-status').val(''); $('#table').DataTable().draw();" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    {{-- Filters --}}
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select id="filter-user" class="form-control">
                                <option value="">{{ __('main.all_users') }}</option>
                                @foreach(\App\Models\User::select('id', 'first_name', 'last_name', 'email')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->id }}- {{ $user->name }} - {{ $user->email }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select id="filter-gateway" class="form-control">
                                <option value="">All Gateways</option>
                                <option value="fawaterak">Fawaterak</option>
                                <option value="instapay">Instapay</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select id="filter-status" class="form-control">
                                <option value="">{{ __('main.all_statuses') }}</option>
                                @foreach(\App\Enums\PaymentStatusEnum::cases() as $status)
                                <option value="{{ $status->value }}" {{ $status->value == 'paid' ? 'selected' : '' }}>
                                    {{ ucfirst($status->value) }}
                                </option> @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="date" id="filter-from" class="form-control" placeholder="From date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="filter-to" class="form-control" placeholder="To date">
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('admin.payment_details.export') }}" class="btn btn-primary">
                                {{ __('buttons.export_sheets') }}
                            </a>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.name') }}</th>
                                        <th>{{ __('attributes.email') }}</th>
                                        <th>{{ __('attributes.phone') }}</th>
                                        <th>Gateway</th>
                                        <th>invoice_id</th>
                                        <th>invoice_key</th>
                                        <th>{{ __('attributes.coupon') }}</th>
                                        <th>{{ __('attributes.payment_method') }}</th>
                                        <th>{{ __('attributes.amount') }}</th>
                                        <th>{{ __('attributes.status') }}</th>
                                        <th>{{ __('attributes.created_at') }}</th>
                                        <th>{{ __('main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>

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
        if ($.fn.DataTable.isDataTable("#table")) {
            $('#table').DataTable().clear().destroy();
        }
        let table = $("#table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.payment_details.data') }}",
                data: function(d) {
                    d.user_id = $('#filter-user').val();
                    d.gateway = $('#filter-gateway').val();
                    d.status = $('#filter-status').val();
                    d.from_created_at = $('#filter-from').val();
                    d.to_created_at = $('#filter-to').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user_name',
                    name: 'user.name'
                },
                {
                    data: 'user_email',
                    name: 'user.email'
                },
                {
                    data: 'user_phone',
                    name: 'user.phone'
                },
                {
                    data: 'gateway',
                    name: 'gateway'
                },
                {
                    data: 'invoice_id',
                    name: 'invoice_id'
                },
                {
                    data: 'invoice_key',
                    name: 'invoice_key'
                },
                {
                    data: 'coupon_code',
                    name: 'coupon.code'
                },
                {
                    data: 'payment_method',
                    name: 'payment_method'
                },
                {
                    data: 'amount_after_coupon',
                    name: 'amount_after_coupon'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: false,
            lengthChange: false,
            autoWidth: false,
            language: {
                emptyTable: "{{ __('datatable.no_data_available_in_table') }}",
                lengthMenu: "{{ __('datatable.show _MENU_ entries') }}",
                search: "{{ __('datatable.search') }}:",
                zeroRecords: "{{ __('datatable.no_matching_records_found') }}",
                paginate: {
                    next: "{{ __('datatable.next') }}",
                    previous: "{{ __('datatable.previous') }}"
                },
                info: "{{ __('datatable.showing from _START_ to _END_ of _TOTAL_ entries') }}",
                infoEmpty: "{{ __('datatable.showing 0 to 0 of 0 entries') }}",
                infoFiltered: "({{ __('datatable.filtered from _MAX_ total entries') }})",
                thousands: ",",
                loadingRecords: "{{ __('datatable.loading...') }}",
                processing: "{{ __('datatable.processing...') }}",
            },

        });

        $('#filter-user, #filter-gateway, #filter-status, #filter-from, #filter-to').on('change', function() {
            table.draw();
        });

        // Quick Action SweetAlert2 handlers
        $(document).on('click', '.quick-approve-btn', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const paymentId = form.data('payment-id');

            Swal.fire({
                title: '<i class="fas fa-check-circle text-success"></i> Quick Approve',
                html: `
                    <p><strong>Approve Instapay Payment #${paymentId}?</strong></p>
                    <p class="text-muted">This will instantly approve the payment and grant course access.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Approve',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Approving payment...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });

        $(document).on('click', '.quick-reject-btn', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const paymentId = form.data('payment-id');

            Swal.fire({
                title: '<i class="fas fa-times-circle text-danger"></i> Quick Reject',
                html: `
                    <p><strong>Reject Instapay Payment #${paymentId}?</strong></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone!
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-times"></i> Reject',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Rejecting payment...',
                        icon: 'info',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
