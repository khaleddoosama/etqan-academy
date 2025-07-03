@extends('admin.master')

@section('title')
{{ __('attributes.payment_detail') }}
@endsection

@section('content')
<div class="content-wrapper">
    <x-custom.header-page title="{{ __('attributes.payment_detail') }}" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <div class="card-body">
                            {{-- Main Payment Info --}}
                            <h5><strong>{{ __('attributes.user') }}</strong></h5>
                            <p><strong>{{ __('attributes.name') }}:</strong> {{ $payment->user->name ?? 'Guest' }}</p>
                            <p><strong>{{ __('attributes.email') }}:</strong> <a href="mailto:{{ $payment->user->email }}">{{ $payment->user->email }}</a></p>
                            <p><strong>{{ __('attributes.phone') }}:</strong> <a href="https://wa.me/{{ $payment->user->phone }}" target="_blank">{{ $payment->user->phone }}</a></p>

                            <hr>

                            <h5><strong>{{ __('attributes.payment_info') }}</strong></h5>
                            <p><strong>Gateway:</strong> {{ $payment->gateway ?? 'fawaterak' }}</p>
                            <p><strong>Invoice Id:</strong> {{ $payment->invoice_id ?? '-' }}</p>
                            <p><strong>Invoice Key:</strong> {{ $payment->invoice_key ?? '-' }}</p>
                            <p><strong>{{ __('attributes.amount_before_coupon') }}:</strong> {{ $payment->amount_before_coupon }}</p>
                            <p><strong>{{ __('attributes.amount_after_coupon') }}:</strong> {{ $payment->amount_after_coupon }}</p>
                            <!-- @if($payment->gateway === 'instapay')
                            <p><strong>{{ __('attributes.amount_confirmed') }}:</strong> {{ $payment->amount_confirmed }}</p>
                            @endif -->
                            <p><strong>{{ __('attributes.discount') }}:</strong> {{ $payment->discount }}%</p>
                            <p><strong>{{ __('attributes.payment_method') }}:</strong> {{ $payment->payment_method }}</p>
                            <p><strong>{{ __('attributes.status') }}:</strong>
                                <span class="badge badge-{{ $payment->status->Color() }}">{{ $payment->status }}</span>
                            </p>
                            <p><strong>{{ __('attributes.paid_at') }}:</strong> {{ $payment->paid_at?->format('Y-m-d H:i') ?? '-' }}</p>

                            {{-- Show transfer image for Instapay payments --}}
                            @if($payment->gateway === 'instapay' && $payment->transfer_image)
                            <hr>
                            <h5><strong>{{ __('attributes.transfer_image') }}</strong></h5>
                            <div class="mb-3">
                                <img src="{{ $payment->transfer_image_url }}" alt="Transfer Image" class="img-fluid" style="max-width: 400px; max-height: 400px;">
                            </div>
                            <p><a href="{{ $payment->transfer_image_url }}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> View Full Image
                                </a></p>
                            @endif

                            {{-- Instapay Admin Controls --}}
                             {{--@if($payment->gateway === 'instapay')
                            <hr>
                            <div class="alert alert-info">
                                <h5><strong><i class="fas fa-info-circle"></i> Instapay Payment Management</strong></h5>
                                <p>This is an Instapay payment that requires manual review. You can update the confirmed amount and approve or reject the payment.</p>
                            </div>

                            @can('payment_detail.update')
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-edit"></i> Update Confirmed Amount</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.payment_details.update_amount', $payment->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="amount">{{ __('attributes.amount_confirmed') }}</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control" name="amount"
                                                            value="{{ $payment->amount_confirmed }}" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">EGP</span>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        <strong>Current:</strong> {{ $payment->amount_confirmed }} EGP |
                                                        <strong>Expected:</strong> {{ $payment->amount_after_coupon }} EGP
                                                        @if($payment->amount_confirmed != $payment->amount_after_coupon)
                                                        <br><span class="text-warning">⚠️ Amount mismatch detected!</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <button type="button" class="btn btn-primary btn-sm update-amount-btn">
                                                    <i class="fas fa-save"></i> Update Amount
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Payment Actions</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($payment->status->value === 'pending')
                                            <p class="text-info mb-3">
                                                <i class="fas fa-clock"></i> This payment is pending review.
                                            </p>
                                            <form action="{{ route('admin.payment_details.status', $payment->id) }}" method="POST" class="d-inline approve-payment-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="paid">
                                                <button type="button" class="btn btn-success btn-sm mb-2 btn-block approve-btn">
                                                    <i class="fas fa-check"></i> Approve Payment
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.payment_details.status', $payment->id) }}" method="POST" class="d-inline reject-payment-form">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="button" class="btn btn-danger btn-sm btn-block reject-btn">
                                                    <i class="fas fa-times"></i> Reject Payment
                                                </button>
                                            </form>
                                            @elseif($payment->status->value === 'paid')
                                            <div class="alert alert-success mb-0">
                                                <i class="fas fa-check-circle"></i> <strong>Payment Approved</strong>
                                                <br><small>Approved on: {{ $payment->paid_at?->format('Y-m-d H:i') ?? $payment->updated_at->format('Y-m-d H:i') }}</small>
                                            </div>
                                            @elseif($payment->status->value === 'cancelled')
                                            <div class="alert alert-danger mb-0">
                                                <i class="fas fa-times-circle"></i> <strong>Payment Rejected</strong>
                                                <br><small>Rejected on: {{ $payment->updated_at->format('Y-m-d H:i') }}</small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-lock"></i> You don't have permission to modify payments.
                            </div>
                            @endcan
                            @endif--}}



                            @can('payment.status')
                            <x-custom.status :model="$payment" routeName="admin.payment_details.status" />
                            @endcan

                            <hr>



                            {{-- Payment Items Table --}}
                            <h5><strong>{{ __('attributes.payment_items') }}</strong></h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.course') }}</th>
                                        <th>{{ __('attributes.installment') }}</th>
                                        <th>{{ __('attributes.package_plan') }}</th>
                                        <th>{{ __('attributes.payment_type') }}</th>
                                        <th>{{ __('attributes.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->paymentItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->course->title ?? '-' }}</td>
                                        <td>{{ $item->courseInstallment->course->title ?? '-' }}</td>
                                        <td>{{ $item->packagePlan->title ?? ($item->packagePlan->package->title ?? '-') }}</td>
                                        <td>{{ $item->payment_type->value ?? '-' }}</td>
                                        <td>{{ $item->amount }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <h5><strong>Payment Data</strong></h5>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <a data-toggle="collapse" href="#paymentData" role="button" aria-expanded="false" aria-controls="paymentData">
                                        Toggle Payment Data JSON
                                    </a>
                                </div>
                                <div class="collapse" id="paymentData">
                                    <div class="card-body bg-light">
                                        <pre><code class="json">{{ json_encode($payment->payment_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            </div>



                            @php
                            function deepDecode($data) {
                            if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                            return deepDecode($decoded);
                            }
                            return $data;
                            }

                            if (is_array($data)) {
                            foreach ($data as $key => $value) {
                            $data[$key] = deepDecode($value);
                            }
                            }

                            return $data;
                            }

                            $payload = deepDecode($payment->response_payload);
                            @endphp

                            <h5><strong>Response Payload</strong></h5>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <a class="text-primary" data-toggle="collapse" href="#responsePayload" role="button"
                                        aria-expanded="false" aria-controls="responsePayload">
                                        Toggle Response Payload
                                    </a>
                                </div>
                                <div class="collapse" id="responsePayload">
                                    <div class="card-body bg-light">
                                        <pre><code class="json">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            </div>





                            <hr>

                            {{-- Timestamps --}}
                            <h5><strong>{{ __('attributes.timestamps') }}</strong></h5>
                            <p><strong>{{ __('attributes.created_at') }}:</strong> {{ $payment->created_at }}</p>
                            <p><strong>{{ __('attributes.updated_at') }}:</strong> {{ $payment->updated_at }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    // Wait for DOM and SweetAlert2
    function initializeHandlers() {
        console.log('Initializing handlers...');

        if (typeof Swal === 'undefined') {
            console.error('❌ SweetAlert2 is not loaded - retrying in 500ms');
            setTimeout(initializeHandlers, 500);
            return;
        }


        // Count buttons
        const approveButtons = document.querySelectorAll('.approve-btn');
        const rejectButtons = document.querySelectorAll('.reject-btn');
        const updateButtons = document.querySelectorAll('.update-amount-btn');

        console.log('Found buttons:', {
            approve: approveButtons.length,
            reject: rejectButtons.length,
            update: updateButtons.length
        });

        // Approve Payment Handler
        approveButtons.forEach(function(button, index) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: 'Approve Payment',
                    html: '<p><strong>Are you sure you want to approve this Instapay payment?</strong></p><p class="text-muted">This will grant course access and mark payment as paid.</p>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve',
                    cancelButtonText: 'Cancel'
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
        });

        // Reject Payment Handler
        rejectButtons.forEach(function(button, index) {
            console.log('✅ Attaching reject handler to button', index);
            button.addEventListener('click', function(e) {
                console.log('🔴 Reject button clicked!');
                e.preventDefault();
                const form = this.closest('form');

                Swal.fire({
                    title: 'Reject Payment',
                    html: '<p><strong>Are you sure you want to reject this payment?</strong></p><div class="alert alert-warning">This action cannot be undone!</div>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reject',
                    cancelButtonText: 'Cancel'
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

        // Update Amount Handler
        updateButtons.forEach(function(button, index) {
            console.log('✅ Attaching update handler to button', index);
            button.addEventListener('click', function(e) {
                console.log('🔵 Update button clicked!');
                e.preventDefault();
                const form = this.closest('form');
                const amountInput = form.querySelector('input[name="amount"]');
                const newAmount = amountInput.value;

                Swal.fire({
                    title: 'Update Amount',
                    html: `<p><strong>Confirm amount update to ${newAmount} EGP?</strong></p>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Update Amount',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Updating amount...',
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

        console.log('✅ All handlers initialized successfully!');
    }

    // Start initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeHandlers);
    } else {
        // DOM is already ready
        initializeHandlers();
    }
</script>
@endsection
