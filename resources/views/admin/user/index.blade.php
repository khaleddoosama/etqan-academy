@extends('admin.master')
@section('title')
{{ $title }}
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <x-custom.header-page title="{{ $title }}" />


    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        @can('user.create')
                        <div class="card-header" style="display: flex;justify-content: end">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"
                                style="color: white; text-decoration: none;">
                                {{ __('buttons.create_user') }}
                            </a>
                        </div>
                        @endcan
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('attributes.image') }}</th>
                                        <th>{{ __('attributes.first_name') }}</th>
                                        <th>{{ __('attributes.last_name') }}</th>
                                        <th>{{ __('attributes.email') }}</th>
                                        <th>{{ __('attributes.phone') }}</th>
                                        <th>{{ __('attributes.status') }}</th>
                                        <th>{{ __('attributes.email_verified_at') }}</th>
                                        <th>{{ __('main.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                    <tr>
                                        <td
                                            title="{{ $user->UserOnline() ? 'Online' : Carbon\Carbon::parse($user->last_login)->diffForHumans() }}">
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

                                        <td> @can('user_course.list')
                                            <a href="{{ route('admin.users.courses.index', $user) }}"
                                                class="btn btn-warning" title="{{ __('buttons.add_course') }}"
                                                style="color: white; text-decoration: none;">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            @endcan

                                            @can('user.show')
                                            <a href="{{ route('admin.users.logs', parameters: $user->id) }}"
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
                                        <th>#</th>
                                        <th>{{ __('attributes.image') }}</th>
                                        <th>{{ __('attributes.first_name') }}</th>
                                        <th>{{ __('attributes.last_name') }}</th>
                                        <th>{{ __('attributes.email') }}</th>
                                        <th>{{ __('attributes.phone') }}</th>
                                        <th>{{ __('attributes.status') }}</th>
                                        <th>{{ __('attributes.email_verified_at') }}</th>
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
