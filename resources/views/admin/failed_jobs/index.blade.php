@extends('admin.master')
@section('title')
    {{ __('attributes.failed_jobs') }}
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.failed_jobs') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <!-- Retry All and Delete All Buttons -->
                                <form action="{{ route('admin.failed_jobs.retry_all') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="{{ __('buttons.retry_all') }}">
                                        <i class="fas fa-redo"></i> {{ __('buttons.retry_all') }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.failed_jobs.delete_all') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ __('buttons.delete_all') }}">
                                        <i class="fas fa-trash"></i> {{ __('buttons.delete_all') }}
                                    </button>
                                </form>
                            </div>
                            <!-- /.card-header -->

                            <div class="card-body table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>UUID</th>
                                            <th>{{ __('attributes.priority') }}</th>
                                            <th>{{ __('attributes.payload') }}</th>
                                            {{-- <th>{{ __('attributes.exception') }}</th> --}}
                                            <th>{{ __('attributes.failed_at') }}</th>
                                            <th>{{ __('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($failedJobs as $failed_job)
                                            <tr>
                                                <td>{{ $failed_job->id }}</td>
                                                <td>{{ $failed_job->uuid }}</td>
                                                <td>{{ $failed_job->queue }}</td>
                                                <td>{{ json_decode($failed_job->payload)->displayName }}</td>
                                                <td>{{ \Carbon\Carbon::parse($failed_job->failed_at)->format('Y-m-d H:i:s') }}
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                        title="{{ __('buttons.show') }}" data-toggle="modal"
                                                        data-target="#show-{{ $failed_job->id }}">
                                                        <i class="fas fa-eye fa-fw"></i>
                                                    </button>

                                                    {{-- retry --}}
                                                    <form action="{{ route('admin.failed_jobs.retry', $failed_job->uuid) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm"
                                                            title="{{ __('buttons.retry') }}">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    </form>

                                                    {{-- delete --}}
                                                    <form action="{{ route('admin.failed_jobs.delete', $failed_job->uuid) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            title="{{ __('buttons.delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <!-- Modal -->
                                            <div class="modal fade show" id="show-{{ $failed_job->id }}" aria-modal="true"
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
                                                            <p>#{{ $failed_job->id }} {{ $failed_job->uuid }}:
                                                                {{ json_decode($failed_job->payload)->displayName }}</p>

                                                            <p>{{ __('attributes.payload') }} :
                                                                <pre style="max-height: 300px">{{ json_encode(json_decode($failed_job->payload), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                                                            </p>
                                                            <p>{{ __('attributes.exception') }} :
                                                                <pre style="max-height: 300px">{{ $failed_job->exception }}</pre>

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
                                            <th>UUID</th>
                                            <th>{{ __('attributes.priority') }}</th>
                                            <th>{{ __('attributes.payload') }}</th>
                                            {{-- <th>{{ __('attributes.exception') }}</th> --}}
                                            <th>{{ __('attributes.failed_at') }}</th>
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
