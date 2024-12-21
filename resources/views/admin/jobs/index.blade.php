@extends('admin.master')
@section('title')
    {{ __('attributes.jobs') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.jobs') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <!-- /.card-header -->
                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.job_name') }}</th>
                                            <th>{{ __('attributes.priority') }}</th>
                                            <th>{{ __('attributes.attempts') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jobs as $job)
                                            <tr class="{{ $job->reserved_at !== null  ? 'bg-success' : '' }}">
                                                <td>{{ $job->id }}</td>
                                                <td>{{ json_decode($job->payload)->displayName }}</td>
                                                <td>{{ $job->queue }}</td>
                                                <td>{{ $job->attempts }}</td>
                                                <td>{{ \Carbon\Carbon::createFromTimestamp($job->created_at)->format('Y-m-d H:i:s') }}
                                                </td>
                                                <td>

                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        title="{{ __('main.show') }}" data-toggle="modal"
                                                        data-target="#show-{{ $job->id }}">
                                                        <i class="fas fa-eye fa-fw"></i>
                                                    </button>

                                                </td>
                                            </tr>
                                            <!-- Modal -->
                                            <div class="modal fade show" id="show-{{ $job->id }}" aria-modal="true"
                                                role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">{{ __('main.show') }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">×</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-left">

                                                            <p>#{{ $job->id }} :
                                                                {{ json_decode($job->payload)->displayName }}</p>

                                                            <p>{{ __('attributes.payload') }}:
                                                                <pre>{{ json_encode(json_decode($job->payload), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                                                            </p>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <x-custom.close-modal-button />
                                                        </div>
                                                    </div>
                                                    <!-- /.modal-content -->
                                                </div>
                                                <!-- /.modal-dialog -->
                                            </div>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('attributes.job_name') }}</th>
                                            <th>{{ __('attributes.priority') }}</th>
                                            <th>{{ __('attributes.attempts') }}</th>
                                            <th>{{ __('attributes.created_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
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
