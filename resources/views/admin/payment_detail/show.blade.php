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
                            <p><strong>Invoice Id:</strong> {{ $payment->invoice_id }}</p>
                            <p><strong>Invoice Key:</strong> {{ $payment->invoice_key }}</p>
                            <p><strong>{{ __('attributes.amount_before_coupon') }}:</strong> {{ $payment->amount_before_coupon }}</p>
                            <p><strong>{{ __('attributes.amount_after_coupon') }}:</strong> {{ $payment->amount_after_coupon }}</p>
                            <p><strong>{{ __('attributes.discount') }}:</strong> {{ $payment->discount }}%</p>
                            <p><strong>{{ __('attributes.payment_method') }}:</strong> {{ $payment->payment_method }}</p>
                            <p><strong>{{ __('attributes.status') }}:</strong>
                                <span class="badge badge-{{ $payment->status->Color() }}">{{ $payment->status }}</span>
                            </p>
                            <p><strong>{{ __('attributes.paid_at') }}:</strong> {{ $payment->paid_at?->format('Y-m-d H:i') ?? '-' }}</p>

                            @can('payment.status')
                            <x-custom.status :model="$payment" routeName="admin.payments.status" />
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
