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
                <th>{{ __('main.actions') }}</th>
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
                    <td>{!! $log->description !!}</td>
                    <td>
                        @if($log->subject)
                            <div>
                                <strong>{{ class_basename($log->subject_type) }}</strong>
                                @if($log->subject && method_exists($log->subject, 'name') && $log->subject->name)
                                    <br><small class="text-muted">{{ $log->subject->name }}</small>
                                @elseif($log->subject && method_exists($log->subject, 'title') && $log->subject->title)
                                    <br><small class="text-muted">{{ $log->subject->title }}</small>
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
                                            <h5 class="modal-title">{{ __('main.log_properties') }} - {{ $log->description }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            @php
                                                $logData = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
                                            @endphp
                                            @if (is_array($logData))
                                                <div class="row">
                                                    @foreach ($logData as $key => $value)
                                                        <div class="col-12 mb-3">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            @if (is_array($value))
                                                                <pre class="bg-light p-2 rounded mt-1">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @else
                                                                <div class="mt-1">{{ $value }}</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <pre class="bg-light p-3 rounded">{{ $log->properties }}</pre>
                                            @endif
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
                    <td>
                        <button type="button" class="btn btn-warning btn-sm"
                                title="{{ __('main.show') }}" data-toggle="modal"
                                data-target="#show-{{ $log->id }}">
                            <i class="fas fa-eye"></i>
                        </button>

                        <!-- Show Modal -->
                        <div class="modal fade" id="show-{{ $log->id }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{ __('main.show') }} {{ $log->log_name ? ucfirst($log->log_name) : '' }}</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>ID:</strong> {{ $log->id }}</p>
                                                <p><strong>{{ __('main.log_type') }}:</strong> {{ $log->log_name }}</p>
                                                <p><strong>{{ __('main.causer') }}:</strong> {{ $log->causer?->name ?? __('main.system') }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>{{ __('main.created_at') }}:</strong> {{ $log->created_at }}</p>
                                                <p><strong>{{ __('main.subject') }}:</strong> {{ $log->subject_type }}</p>
                                                <p><strong>Subject ID:</strong> {{ $log->subject_id }}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <h6><strong>{{ __('main.description') }}:</strong></h6>
                                        <p>{{ $log->description }}</p>

                                        @if($log->properties)
                                            <hr>
                                            <h6><strong>{{ __('main.properties') }}:</strong></h6>
                                            @php
                                                $logData = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
                                            @endphp
                                            @if (is_array($logData))
                                                <ul class="list-group">
                                                    @foreach ($logData as $key => $value)
                                                        <li class="list-group-item">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            @if (is_array($value))
                                                                <pre class="mt-2">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @else
                                                                <span class="ml-2">{{ $value }}</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <pre class="bg-light p-3 rounded">{{ $log->properties }}</pre>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('buttons.close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
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
            @include('admin.logs.pagination-links', ['paginator' => $logs])
        </div>
    </div>
@endif
