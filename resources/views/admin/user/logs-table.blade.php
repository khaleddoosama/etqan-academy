@if($logs->count() > 0)
    <div class="table-responsive">
        <table id="logs-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('attributes.type') }}</th>
                    <th>{{ __('attributes.description') }}</th>
                    <th>{{ __('attributes.log_name') }}</th>
                    <th>{{ __('attributes.date') }}</th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>
                            @if($log->causer_id == $user->id)
                                <span class="badge badge-primary">{{ __('attributes.performed_by_user') }}</span>
                            @else
                                <span class="badge badge-info">{{ __('attributes.performed_on_user') }}</span>
                            @endif
                        </td>
                        <td>{!! $log->description !!}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $log->log_name }}</span>
                        </td>
                        <td>
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                            <br>
                            <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm"
                                title="{{ __('main.show') }}" data-toggle="modal"
                                data-target="#show-{{ $log->id }}">
                                <i class="fas fa-eye fa-fw"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <p class="text-muted">
                {{ __('main.showing') }} {{ $logs->firstItem() }} {{ __('main.to') }} {{ $logs->lastItem() }}
                {{ __('main.of') }} {{ $logs->total() }} {{ __('main.results') }}
                ({{ $logs->perPage() }} {{ __('attributes.per_page') }})
            </p>
        </div>
        <div>
            {{ $logs->links('admin.user.pagination-links') }}
        </div>
    </div>

    <!-- Modals for log details -->
    @foreach ($logs as $log)
        <div class="modal fade" id="show-{{ $log->id }}" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __('attributes.log_details') }} #{{ $log->id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>{{ __('attributes.id') }}:</strong></td>
                                <td>{{ $log->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('attributes.log_name') }}:</strong></td>
                                <td><span class="badge badge-secondary">{{ $log->log_name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('attributes.description') }}:</strong></td>
                                <td>{{ $log->description }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('attributes.subject') }}:</strong></td>
                                <td>
                                    @if($log->subject_type)
                                        {{ $log->subject_type }} (ID: {{ $log->subject_id }})
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('attributes.causer') }}:</strong></td>
                                <td>
                                    @if($log->causer)
                                        {{ $log->causer->first_name }} {{ $log->causer->last_name }} ({{ $log->causer->email }})
                                    @else
                                        {{ __('attributes.system') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('attributes.properties') }}:</strong></td>
                                <td>
                                    @if($log->properties && $log->properties->isNotEmpty())
                                        <pre class="bg-light p-2 rounded">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('attributes.date') }}:</strong></td>
                                <td>
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    <br>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('buttons.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ __('messages.no_logs_found') }}
    </div>
@endif
