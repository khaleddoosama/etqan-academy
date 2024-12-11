  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('admin.home') }}" class="brand-link">
          <img src="{{ asset('asset/logo.jpg') }}" alt="Etqan Logo" class="brand-image img-circle elevation-3"
              style="opacity: .8">
          <span class="brand-text font-weight-light">{{ __('main.dashboard') }}</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="pb-3 mt-3 mb-3 user-panel d-flex">
              <div class="image">
                  <x-custom.profile-picture :user="auth()->user()" size="30" id="userPicture" />
              </div>
              <div class="info">
                  <a href="#" class="d-block">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</a>
              </div>
          </div>

          <!-- SidebarSearch Form -->
          <div class="form-inline">
              <div class="input-group" data-widget="sidebar-search">
                  <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                      aria-label="Search">
                  <div class="input-group-append">
                      <button class="btn btn-sidebar">
                          <i class="fas fa-search fa-fw"></i>
                      </button>
                  </div>
              </div>
          </div>

          <!-- Sidebar Menu -->
          <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  @can('dashboard.list')
                      {{--  home  --}}
                      <li class="nav-item">
                          <a href="{{ route('admin.home') }}"
                              class="nav-link @if (Request::is('*/admin')) active @endif">
                              <i class="nav-icon fas fa-tachometer-alt"></i>
                              <p>
                                  {{ __('main.dashboard') }}
                              </p>
                          </a>

                      </li>
                  @endcan

                  {{--  users  --}}
                  @can('user.list')
                      <li class="nav-item @if (Request::is('*/admin/users') || Request::is('*/admin/users/*')) menu-open @endif">
                          <a href="#" class="nav-link  @if (Request::is('*/admin/users') || Request::is('*/admin/users/*')) active @endif">
                              <i class="nav-icon fas fa-users"></i>

                              <p>
                                  {{ __('attributes.users') }}
                                  <i class="fas fa-angle-left right"></i>
                                  <span
                                      class="badge badge-info right">{{ auth()->user()->unreadNotifications()->where('type', 'App\Notifications\UserRegisteredNotification')->count() }}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview" style=" @if (!(Request::is('*/admin/users') || Request::is('*/admin/users/*'))) display: none @endif">

                              <li class="nav-item">
                                  <a href="{{ route('admin.users.active') }}"
                                      class="nav-link @if (Request::is('*/admin/users/active') || Request::is('*/admin/users/active/*')) active @endif">
                                      <i class="far fa-circle nav-icon"></i>
                                      <p>
                                          {{ __('attributes.users_active') }}
                                      </p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('admin.users.inactive') }}"
                                      class="nav-link @if (Request::is('*/admin/users/inactive') || Request::is('*/admin/users/inactive/*')) active @endif">
                                      <i class="far fa-circle nav-icon"></i>
                                      <p>
                                          {{ __('attributes.users_inactive') }}
                                      </p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endcan

                  {{--  categories  --}}
                  @can('category.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.categories.index') }}"
                              class="nav-link @if (Request::is('*/admin/categories') || Request::is('*/admin/categories/*')) active @endif">
                              <i class="nav-icon fas fa-th"></i>
                              <p>
                                  {{ __('attributes.categories') }}
                              </p>
                          </a>
                      </li>
                  @endcan

                  {{-- instructors --}}
                  @can('instructor.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.instructors.index') }}"
                              class="nav-link @if (Request::is('*/admin/instructors') || Request::is('*/admin/instructors/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="school-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.instructors') }}
                              </p>
                          </a>
                      </li>
                  @endcan

                  {{-- programs --}}
                  @can('program.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.programs.index') }}"
                              class="nav-link @if (Request::is('*/admin/programs') || Request::is('*/admin/programs/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="code-slash-outline"></ion-icon></span>
                              <p>{{ __('attributes.programs') }}</p>
                          </a>
                      </li>
                  @endcan

                  {{-- Courses --}}
                  @can('course.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.courses.index') }}"
                              class="nav-link @if (Request::is('*/admin/courses') || Request::is('*/admin/courses/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="book-outline"></ion-icon></span>
                              <p>{{ __('attributes.courses') }}</p>
                          </a>
                      </li>
                  @endcan


                  {{-- Lecture Management --}}
                  {{-- @if (auth()->user()->can('lectures.list')) --}}
                  <li class="nav-item @if (Request::is('*/admin/lectures') || Request::is('*/admin/lectures/*') || Request::is('*/admin/failed-lectures')) menu-open @endif">
                      <a href="#" class="nav-link @if (Request::is('*/admin/lectures') || Request::is('*/admin/lectures/*') || Request::is('*/admin/failed-lectures')) active @endif">
                          <span class="icon nav-icon"><ion-icon name="videocam-outline"></ion-icon></span>
                          <p>
                              {{ __('Lecture Management') }}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview"
                          style="background-color:rgba(255, 255, 255, 0.1);@if (!(Request::is('*/admin/lectures') || Request::is('*/admin/lectures/*') || Request::is('*/admin/failed-lectures'))) display: none @endif">
                          {{-- Lectures --}}
                          <li class="nav-item">
                              <a href="{{ route('admin.lectures.index') }}"
                                  class="nav-link @if (Request::is('*/admin/lectures') || Request::is('*/admin/lectures/*')) active @endif">
                                  <span class="icon nav-icon"><ion-icon name="videocam-outline"></ion-icon></span>

                                  <p>{{ __('attributes.lectures') }}</p>
                              </a>
                          </li>

                          {{-- Failed Lectures --}}
                          <li class="nav-item">
                              <a href="{{ route('admin.lectures.failed.index') }}"
                                  class="nav-link @if (Request::is('*/admin/failed-lectures')) active @endif">
                                  <span class="icon nav-icon"><ion-icon name="close-circle-outline"></ion-icon></span>
                                  <p>{{ __('attributes.failed_lectures') }}</p>
                              </a>
                          </li>
                      </ul>
                  </li>
                  {{-- @endif --}}



                  {{-- Inquiry, Withdrawal, and Request Course --}}
                  @if (auth()->user()->can('inquiry.list') ||
                          auth()->user()->can('withdrawal.list') ||
                          auth()->user()->can('request_course.list'))
                      <li class="nav-item @if (Request::is('*/admin/inquiries') ||
                              Request::is('*/admin/inquiries/*') ||
                              Request::is('*/admin/withdrawal-requests') ||
                              Request::is('*/admin/withdrawal-requests/*') ||
                              Request::is('*/admin/request-courses') ||
                              Request::is('*/admin/request-courses/*') ||
                              Request::is('*/admin/payment-details') ||
                              Request::is('*/admin/payment-details/*')) menu-open @endif">
                          <a href="#" class="nav-link @if (Request::is('*/admin/inquiries') ||
                                  Request::is('*/admin/inquiries/*') ||
                                  Request::is('*/admin/withdrawal-requests') ||
                                  Request::is('*/admin/withdrawal-requests/*') ||
                                  Request::is('*/admin/request-courses') ||
                                  Request::is('*/admin/request-courses/*') ||
                                  Request::is('*/admin/payment-details') ||
                                  Request::is('*/admin/payment-details/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="notifications-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.requests') }}
                                  <i class="fas fa-angle-left right"></i>
                                  <span class="badge badge-info right">
                                      {{ auth()->user()->unreadNotifications()->whereIn('type', [
                                              'App\Notifications\InquiryNotification',
                                              'App\Notifications\WithdrawalRequestNotification',
                                              'App\Notifications\CourseRequestNotification',
                                              'App\Notifications\PaymentDetailCreatedNotification',
                                          ])->count() }}
                                  </span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color:rgba(255, 255, 255, 0.1); @if (
                                  !(Request::is('*/admin/inquiries') ||
                                      Request::is('*/admin/inquiries/*') ||
                                      Request::is('*/admin/withdrawal-requests') ||
                                      Request::is('*/admin/withdrawal-requests/*') ||
                                      Request::is('*/admin/request-courses') ||
                                      Request::is('*/admin/request-courses/*') ||
                                      Request::is('*/admin/payment-details') ||
                                      Request::is('*/admin/payment-details/*')
                                  )) display: none @endif">
                              {{-- Inquiry --}}
                              @can('inquiry.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.inquiries.index') }}"
                                          class="nav-link @if (Request::is('*/admin/inquiries') || Request::is('*/admin/inquiries/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="chatbox-ellipses-outline"></ion-icon></span>
                                          <p>{{ __('attributes.inquiry') }}</p>
                                          <span class="badge badge-info right">
                                              {{ auth()->user()->unreadNotifications()->where('type', 'App\Notifications\InquiryNotification')->count() }}
                                          </span>
                                      </a>
                                  </li>
                              @endcan

                              {{-- Withdrawal --}}
                              @can('withdrawal.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.withdrawal_requests.index') }}"
                                          class="nav-link @if (Request::is('*/admin/withdrawal-requests') || Request::is('*/admin/withdrawal-requests/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon name="cash-outline"></ion-icon></span>
                                          <p>{{ __('attributes.withdrawal_request') }}</p>
                                          <span class="badge badge-info right">
                                              {{ auth()->user()->unreadNotifications()->where('type', 'App\Notifications\WithdrawalRequestNotification')->count() }}
                                          </span>
                                      </a>
                                  </li>
                              @endcan

                              {{-- RequestCourse --}}
                              @can('request_course.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.request_courses.index') }}"
                                          class="nav-link @if (Request::is('*/admin/request-courses') || Request::is('*/admin/request-courses/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="chatbox-ellipses-outline"></ion-icon></span>
                                          <p>{{ __('attributes.request_course') }}</p>
                                          <span class="badge badge-info right">
                                              {{ auth()->user()->unreadNotifications()->where('type', 'App\Notifications\CourseRequestNotification')->count() }}
                                          </span>
                                      </a>
                                  </li>
                              @endcan

                              {{-- PaymentDetail --}}
                              @can('payment_detail.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.payment_details.index') }}"
                                          class="nav-link @if (Request::is('*/admin/payment-details') || Request::is('*/admin/payment-details/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon name="cash-outline"></ion-icon></span>
                                          <p>{{ __('attributes.payment_details') }}</p>
                                          <span class="badge badge-info right">
                                              {{ auth()->user()->unreadNotifications()->where('type', 'App\Notifications\PaymentDetailCreatedNotification')->count() }}
                                          </span>
                                      </a>
                                  </li>
                              @endcan
                          </ul>
                      </li>
                  @endif




                  {{-- Admin Management --}}
                  @if (auth()->user()->can('admin.list') ||
                          auth()->user()->can('permission.list') ||
                          auth()->user()->can('role.list') ||
                          auth()->user()->can('role_permission.list'))
                      <li class="nav-item @if (Request::is('*/admin/all_admin') ||
                              Request::is('*/admin/all_admin/*') ||
                              Request::is('*/admin/permission') ||
                              Request::is('*/admin/permission/*') ||
                              Request::is('*/admin/role') ||
                              Request::is('*/admin/role/*') ||
                              Request::is('*/admin/role_permissions') ||
                              Request::is('*/admin/role_permissions/*')) menu-open @endif">
                          <a href="#" class="nav-link @if (Request::is('*/admin/all_admin') ||
                                  Request::is('*/admin/all_admin/*') ||
                                  Request::is('*/admin/permission') ||
                                  Request::is('*/admin/permission/*') ||
                                  Request::is('*/admin/role') ||
                                  Request::is('*/admin/role/*') ||
                                  Request::is('*/admin/role_permissions') ||
                                  Request::is('*/admin/role_permissions/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="settings-outline"></ion-icon></span>
                              <p>
                                  {{ __('Admin Management') }}
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color:rgba(255, 255, 255, 0.1); @if (
                                  !(Request::is('*/admin/all_admin') ||
                                      Request::is('*/admin/all_admin/*') ||
                                      Request::is('*/admin/permission') ||
                                      Request::is('*/admin/permission/*') ||
                                      Request::is('*/admin/role') ||
                                      Request::is('*/admin/role/*') ||
                                      Request::is('*/admin/role_permissions') ||
                                      Request::is('*/admin/role_permissions/*')
                                  )) display: none @endif">
                              {{-- Admins --}}
                              @can('admin.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.all_admin.index') }}"
                                          class="nav-link @if (Request::is('*/admin/all_admin') || Request::is('*/admin/all_admin/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="person-circle-outline"></ion-icon></span>
                                          <p>{{ __('attributes.admin_manage') }}</p>
                                      </a>
                                  </li>
                              @endcan

                              {{-- Permissions --}}
                              @can('permission.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.permission.index') }}"
                                          class="nav-link @if (Request::is('*/admin/permission') || Request::is('*/admin/permission/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="lock-closed-outline"></ion-icon></span>
                                          <p>{{ __('attributes.permissions') }}</p>
                                      </a>
                                  </li>
                              @endcan

                              {{-- Roles --}}
                              @can('role.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.role.index') }}"
                                          class="nav-link @if (Request::is('*/admin/role') || Request::is('*/admin/role/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon name="key-outline"></ion-icon></span>
                                          <p>{{ __('attributes.roles') }}</p>
                                      </a>
                                  </li>
                              @endcan

                              {{-- Roles In Permission --}}
                              @can('role_permission.list')
                                  <li class="nav-item">
                                      <a href="{{ route('admin.role_permissions.index') }}"
                                          class="nav-link @if (Request::is('*/admin/role_permissions') || Request::is('*/admin/role_permissions/*')) active @endif">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="lock-open-outline"></ion-icon></span>
                                          <p>{{ __('attributes.role_in_permissions') }}</p>
                                      </a>
                                  </li>
                              @endcan
                          </ul>
                      </li>
                  @endif


                  {{-- Show Logs --}}
                  {{-- @can('log.list') --}}
                  {{-- Logs Management --}}
                  @if (auth()->id() == 1)
                      <li class="nav-item @if (Request::is('*/admin/logs/files') ||
                              Request::is('*/admin/logs/files/*') ||
                              Request::is('*/admin/logs') ||
                              Request::is('*/admin/logs/default') ||
                              Request::is('*/admin/logs/web') ||
                              Request::is('*/admin/logs/api') ||
                              Request::is('*/admin/databases')) menu-open @endif">
                          <a href="#" class="nav-link @if (Request::is('*/admin/logs/files') ||
                                  Request::is('*/admin/logs/files/*') ||
                                  Request::is('*/admin/logs') ||
                                  Request::is('*/admin/logs/default') ||
                                  Request::is('*/admin/logs/web') ||
                                  Request::is('*/admin/logs/api') ||
                                  Request::is('*/admin/databases')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="list-outline"></ion-icon></span>
                              <p>
                                  {{ __('Logs Management') }}
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color:rgba(255, 255, 255, 0.1); @if (
                                  !(Request::is('*/admin/logs/files') ||
                                      Request::is('*/admin/logs/files/*') ||
                                      Request::is('*/admin/logs') ||
                                      Request::is('*/admin/logs/default') ||
                                      Request::is('*/admin/logs/web') ||
                                      Request::is('*/admin/logs/api') ||
                                      Request::is('*/admin/databases')
                                  )) display: none @endif">

                              {{-- Logs Files --}}
                              <li class="nav-item">
                                  <a href="{{ route('admin.logs.files.index') }}"
                                      class="nav-link @if (Request::is('*/admin/logs/files') || Request::is('*/admin/logs/files/*')) active @endif">
                                      <span class="icon nav-icon"><ion-icon name="folder-outline"></ion-icon></span>
                                      <!-- Second icon -->
                                      <p>{{ __('attributes.logs_files') }}</p>
                                  </a>
                              </li>

                              {{-- Logs --}}
                              <li class="nav-item">
                                  <a href="{{ route('admin.logs.index') }}"
                                      class="nav-link @if (Request::is('*/admin/logs') ||
                                              Request::is('*/admin/logs/default') ||
                                              Request::is('*/admin/logs/web') ||
                                              Request::is('*/admin/logs/api')) active @endif">
                                      <span class="icon nav-icon"><ion-icon name="document-outline"></ion-icon></span>
                                      <!-- Second icon -->
                                      <p>{{ __('attributes.logs') }}</p>
                                  </a>
                              </li>

                              {{-- Databases --}}
                              <li class="nav-item">
                                  <a href="{{ route('admin.databases.index') }}"
                                      class="nav-link @if (Request::is('*/admin/databases')) active @endif">
                                      <span class="icon nav-icon"><ion-icon name="server-outline"></ion-icon></span>
                                      <p>{{ __('attributes.databases') }}</p>
                                  </a>
                              </li>

                          </ul>
                      </li>
                  @endif


                  {{-- @endcan --}}

                  {{-- Show jobs --}}
                  {{-- @can('job.list') --}}
                  {{-- Job Management --}}
                  @if (auth()->id() == 1)
                      <li class="nav-item @if (Request::is('*/admin/jobs') ||
                              Request::is('*/admin/jobs/*') ||
                              Request::is('*/admin/failed_jobs') ||
                              Request::is('*/admin/failed_jobs/*')) menu-open @endif">
                          <a href="#" class="nav-link @if (Request::is('*/admin/jobs') ||
                                  Request::is('*/admin/jobs/*') ||
                                  Request::is('*/admin/failed_jobs') ||
                                  Request::is('*/admin/failed_jobs/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                              <p>
                                  {{ __('Job Management') }}
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color:rgba(255, 255, 255, 0.1); @if (
                                  !(Request::is('*/admin/jobs') ||
                                      Request::is('*/admin/jobs/*') ||
                                      Request::is('*/admin/failed_jobs') ||
                                      Request::is('*/admin/failed_jobs/*')
                                  )) display: none @endif">
                              {{-- Show Jobs --}}
                              <li class="nav-item">
                                  <a href="{{ route('admin.jobs.index') }}"
                                      class="nav-link @if (Request::is('*/admin/jobs') || Request::is('*/admin/jobs/*')) active @endif">
                                      <span class="icon nav-icon"><ion-icon
                                              name="briefcase-outline"></ion-icon></span>
                                      <p>{{ __('attributes.jobs') }}</p>
                                  </a>
                              </li>

                              {{-- Failed Jobs --}}
                              <li class="nav-item">
                                  <a href="{{ route('admin.failed_jobs.index') }}"
                                      class="nav-link @if (Request::is('*/admin/failed_jobs') || Request::is('*/admin/failed_jobs/*')) active @endif">
                                      <span class="icon nav-icon"><ion-icon
                                              name="close-circle-outline"></ion-icon></span>
                                      <p>{{ __('attributes.failed_jobs') }}</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endif


                  {{-- @endcan --}}
              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>
