@if($users->count() > 0)
    <div class="table-responsive">
        <table id="users-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="id">
                            #
                            @if(request('sort_by') == 'id')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>{{ __('attributes.image') }}</th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="first_name">
                            {{ __('attributes.first_name') }}
                            @if(request('sort_by') == 'first_name')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="last_name">
                            {{ __('attributes.last_name') }}
                            @if(request('sort_by') == 'last_name')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="email">
                            {{ __('attributes.email') }}
                            @if(request('sort_by') == 'email')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="phone">
                            {{ __('attributes.phone') }}
                            @if(request('sort_by') == 'phone')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="status">
                            {{ __('attributes.status') }}
                            @if(request('sort_by') == 'status')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="email_verified_at">
                            {{ __('attributes.email_verified_at') }}
                            @if(request('sort_by') == 'email_verified_at')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        @php
                            $userOnilneTitle = $user->UserOnline();
                            if ($userOnilneTitle) {
                                $userOnilneTitle = 'Online';
                            } elseif ($user->last_login) {
                                $userOnilneTitle = Carbon\Carbon::parse($user->last_login)->diffForHumans();
                            } else {
                                $userOnilneTitle = 'N/A';
                            }
                        @endphp
                        <td title="{{ $userOnilneTitle }}">
                            {!! $user->UserOnline()
                                ? "<i class='fas fa-circle text-success'></i>"
                                : "<i class='fas fa-circle text-danger'></i>" !!}
                            {{ $user->id }}
                        </td>
                        <td>
                            <x-custom.profile-picture :user="$user" size="50" />
                        </td>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <x-custom.status-span :status="$user->status" />
                        </td>
                        <td>
                            @if ($user->email_verified_at)
                                {{ $user->email_verified_at }}
                            @else
                                N/A
                                @can('user.verify')
                                    @if ($user->email_verified_at == null)
                                        <form action="{{ route('admin.users.verify', $user->id) }}"
                                            method="POST" style="display: inline-block;" class="mx-3">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-success"
                                                title="{{ __('buttons.verify_email') }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            @endif
                        </td>
                        <td>
                            @can('user_course.list')
                                <a href="{{ route('admin.users.courses.index', $user) }}"
                                    class="btn btn-warning" title="{{ __('buttons.add_course') }}"
                                    style="color: white; text-decoration: none;">
                                    <i class="fas fa-plus"></i>
                                </a>
                            @endcan

                            @can('user.show')
                                <a href="{{ route('admin.users.logs', $user->id) }}"
                                    class="btn btn-info" title="{{ __('buttons.view_logs') }}"
                                    style="color: white; text-decoration: none;">
                                    <i class="fas fa-history"></i>
                                </a>
                            @endcan

                            @can('user.edit')
                                <x-custom.edit-button route="admin.users.edit" :id="$user->id" />
                            @endcan

                            @can('user.status')
                                <x-custom.change-status-button :status="$user->status"
                                    route="admin.users.status" :id="$user->id" />
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="id">
                            #
                            @if(request('sort_by') == 'id')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>{{ __('attributes.image') }}</th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="first_name">
                            {{ __('attributes.first_name') }}
                            @if(request('sort_by') == 'first_name')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="last_name">
                            {{ __('attributes.last_name') }}
                            @if(request('sort_by') == 'last_name')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="email">
                            {{ __('attributes.email') }}
                            @if(request('sort_by') == 'email')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="phone">
                            {{ __('attributes.phone') }}
                            @if(request('sort_by') == 'phone')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="status">
                            {{ __('attributes.status') }}
                            @if(request('sort_by') == 'status')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="javascript:void(0)" class="sort-link text-dark text-decoration-none" data-sort="email_verified_at">
                            {{ __('attributes.email_verified_at') }}
                            @if(request('sort_by') == 'email_verified_at')
                                @if(request('sort_direction', 'desc') == 'asc')
                                    <i class="fas fa-sort-up"></i>
                                @else
                                    <i class="fas fa-sort-down"></i>
                                @endif
                            @else
                                <i class="fas fa-sort text-muted"></i>
                            @endif
                        </a>
                    </th>
                    <th>{{ __('main.actions') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <p class="text-muted">
                {{ __('main.showing') }} {{ $users->firstItem() }} {{ __('main.to') }} {{ $users->lastItem() }}
                {{ __('main.of') }} {{ $users->total() }} {{ __('main.results') }}
                ({{ $users->perPage() }} {{ __('attributes.per_page') }})
            </p>
        </div>
        <div>
            {{ $users->links('admin.user.pagination-links') }}
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ __('messages.no_users_found') }}
    </div>
@endif
