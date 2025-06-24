<!-- Logs Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('main.log_type') }}</th>
                <th>{{ __('main.description') }}</th>
                <th>{{ __('main.subject') }}</th>
                <th>{{ __('main.causer') }}</th>
                <th>{{ __('main.properties') }}</th>
                <th>{{ __('main.created_at') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>
                        <span class="badge badge-pill
                            @switch($log->log_name)
                                @case('created') bg-success @break
                                @case('updated') bg-info @break
                                @case('deleted') bg-danger @break
                                @case('login') bg-primary @break
                                @case('logout') bg-secondary @break
                                @default bg-dark
                            @endswitch
                        ">
                            {{ $log->log_name }}
                        </span>
                    </td>
                    <td>{{ $log->description }}</td>
                    <td>
                        @if($log->subject)
                            <div>
                                <strong>{{ class_basename($log->subject_type) }}</strong>
                                @if(method_exists($log->subject, 'name'))
                                    <br><small class="text-muted">{{ $log->subject->name ?? $log->subject_id }}</small>
                                @elseif(method_exists($log->subject, 'title'))
                                    <br><small class="text-muted">{{ $log->subject->title ?? $log->subject_id }}</small>
                                @else
                                    <br><small class="text-muted">ID: {{ $log->subject_id }}</small>
                                @endif
                            </div>
                        @else
                            <span class="text-muted">{{ __('main.no_subject') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($log->causer)
                            <div>
                                <strong>{{ $log->causer->name }}</strong>
                                <br><small class="text-muted">{{ class_basename($log->causer_type) }}</small>
                            </div>
                        @else
                            <span class="text-muted">{{ __('main.system') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($log->properties && $log->properties->count() > 0)
                            <button type="button" class="btn btn-sm btn-outline-info"
                                    data-toggle="modal" data-target="#propertiesModal{{ $log->id }}">
                                <i class="fas fa-eye"></i> {{ __('main.view_properties') }}
                            </button>

                            <!-- Properties Modal -->
                            <div class="modal fade" id="propertiesModal{{ $log->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('main.log_properties') }} - {!! $log->description !!}</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <pre class="bg-light p-3 rounded">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('buttons.close') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">{{ __('main.no_properties') }}</span>
                        @endif
                    </td>
                    <td>
                        <div title="{{ $log->created_at }}">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                            <br><small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('main.no_logs_found') }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($logs->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="pagination-info">
            <small class="text-muted">
                {{ __('main.showing') }} {{ $logs->firstItem() ?? 0 }} {{ __('main.to') }} {{ $logs->lastItem() ?? 0 }}
                {{ __('main.of') }} {{ $logs->total() }} {{ __('main.results') }}
            </small>
        </div>
        <div class="pagination-links">
            @include('admin.admin.pagination-links', ['paginator' => $logs])
        </div>
    </div>
@endif
