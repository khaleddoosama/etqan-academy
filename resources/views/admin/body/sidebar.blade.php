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

                  @php
                      $userManagementItems = [
                          [
                              'key' => 'users',
                              'permission' => 'user.list',
                              'label' => __('attributes.users'),
                              'icon' => 'fas fa-users',
                              'badge' => auth()
                                  ->user()
                                  ->unreadNotifications()
                                  ->where('type', 'App\Notifications\UserRegisteredNotification')
                                  ->count(),
                              'subItems' => [
                                  [
                                      'route' => 'admin.users.active',
                                      'label' => __('attributes.users_active'),
                                      'request_patterns' => ['*/admin/users/active', '*/admin/users/active/*'],
                                  ],
                                  [
                                      'route' => 'admin.users.inactive',
                                      'label' => __('attributes.users_inactive'),
                                      'request_patterns' => ['*/admin/users/inactive', '*/admin/users/inactive/*'],
                                  ],
                              ],
                              'request_patterns' => ['*/admin/users', '*/admin/users/*'],
                          ],
                          [
                              'key' => 'instructors',
                              'permission' => 'instructor.list',
                              'label' => __('attributes.instructors'),
                              'icon' => 'school-outline',
                              'route' => 'admin.instructors.index',
                              'request_patterns' => ['*/admin/instructors', '*/admin/instructors/*'],
                          ],
                          [
                              'key' => 'student_works',
                              'permission' => 'student_work.list',
                              'label' => __('attributes.student_works'),
                              'icon' => 'clipboard-outline',
                              'route' => 'admin.student_works.index',
                              'request_patterns' => ['*/admin/student_works', '*/admin/student_works/*'],
                          ],
                          [
                              'key' => 'student_opinions',
                              'permission' => 'student_opinion.list',
                              'label' => __('attributes.student_opinions'),
                              'icon' => 'chatbox-ellipses-outline',
                              'route' => 'admin.student-opinions.index',
                              'request_patterns' => ['*/admin/student-opinions', '*/admin/student-opinions/*'],
                          ],
                      ];

                      $isActive = function ($patterns) {
                          return collect($patterns)->contains(fn($pattern) => Request::is($pattern));
                      };

                      $isMenuOpen = collect($userManagementItems)->contains(
                          fn($item) => (isset($item['subItems']) && $isActive($item['request_patterns'])) ||
                              $isActive($item['request_patterns']),
                      );
                  @endphp

                  <li class="nav-item {{ $isMenuOpen ? 'menu-open' : '' }}">
                      <a href="#" class="nav-link {{ $isMenuOpen ? 'active' : '' }}">
                          <i class="nav-icon fas fa-user-cog"></i>
                          <p>
                              {{ __('attributes.user_management') }}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview"
                          style="background-color: rgba(255, 255, 255, 0.1); {{ $isMenuOpen ? '' : 'display: none;' }}">
                          @foreach ($userManagementItems as $item)
                              @can($item['permission'])
                                  @if (isset($item['subItems']))
                                      <li class="nav-item {{ $isActive($item['request_patterns']) ? 'menu-open' : '' }}">
                                          <a href="#"
                                              class="nav-link {{ $isActive($item['request_patterns']) ? 'active' : '' }}">
                                              <i class="nav-icon {{ $item['icon'] }}"></i>
                                              <p>
                                                  {{ $item['label'] }}
                                                  <i class="fas fa-angle-left right"></i>
                                                  @if ($item['badge'] ?? false)
                                                      <span class="badge badge-info right">{{ $item['badge'] }}</span>
                                                  @endif
                                              </p>
                                          </a>
                                          <ul class="nav nav-treeview">
                                              @foreach ($item['subItems'] as $subItem)
                                                  <li class="nav-item">
                                                      <a href="{{ route($subItem['route']) }}"
                                                          class="nav-link {{ $isActive($subItem['request_patterns']) ? 'active' : '' }}">
                                                          <i class="far fa-circle nav-icon"></i>
                                                          <p>{{ $subItem['label'] }}</p>
                                                      </a>
                                                  </li>
                                              @endforeach
                                          </ul>
                                      </li>
                                  @else
                                      <li class="nav-item">
                                          <a href="{{ route($item['route']) }}"
                                              class="nav-link {{ $isActive($item['request_patterns']) ? 'active' : '' }}">
                                              <span class="icon nav-icon"><ion-icon
                                                      name="{{ $item['icon'] }}"></ion-icon></span>
                                              <p>{{ $item['label'] }}</p>
                                          </a>
                                      </li>
                                  @endif
                              @endcan
                          @endforeach
                      </ul>
                  </li>


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

                  @php
                      $courseMenuItems = [
                          'lectures' => [
                              'route' => 'admin.lectures.index',
                              'icon' => 'videocam-outline',
                              'label' => __('attributes.lectures'),
                              'permission' => null, // يمكنك إضافة الصلاحية لاحقًا
                          ],
                          'courses' => [
                              'route' => 'admin.courses.index',
                              'icon' => 'book-outline',
                              'label' => __('attributes.courses'),
                              'permission' => 'course.list',
                          ],
                          'course_installments' => [
                              'route' => 'admin.course_installments.index',
                              'icon' => 'cash-outline',
                              'label' => __('attributes.course_installments'),
                              'permission' => 'course_installment.list',
                          ],
                          'course_offers' => [
                              'route' => 'admin.course_offers.index',
                              'icon' => 'pricetag-outline',
                              'label' => __('attributes.course_offers'),
                              'permission' => 'course_offer.list',
                          ],
                      ];

                      $isActive = function ($prefix) {
                          return Request::is("*/admin/$prefix*");
                      };

                      $isMenuOpen = collect(array_keys($courseMenuItems))->contains(fn($prefix) => $isActive($prefix));
                  @endphp

                  <li class="nav-item {{ $isMenuOpen ? 'menu-open' : '' }}">
                      <a href="#" class="nav-link {{ $isMenuOpen ? 'active' : '' }}">
                          <span class="icon nav-icon"><ion-icon name="videocam-outline"></ion-icon></span>
                          <p>
                              {{ __('attributes.course_management') }}
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview"
                          style="background-color: rgba(255, 255, 255, 0.1); {{ $isMenuOpen ? '' : 'display: none;' }}">
                          @foreach ($courseMenuItems as $key => $item)
                              @can($item['permission'])
                                  <li class="nav-item">
                                      <a href="{{ route($item['route']) }}"
                                          class="nav-link {{ $isActive($key) ? 'active' : '' }}">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="{{ $item['icon'] }}"></ion-icon></span>
                                          <p>{{ $item['label'] }}</p>
                                      </a>
                                  </li>
                              @endcan
                          @endforeach
                      </ul>
                  </li>




                  {{-- Inquiry, Withdrawal, and Request Course --}}
                  @php
                      $requestMenuItems = [
                          'inquiries' => [
                              'route' => 'admin.inquiries.index',
                              'icon' => 'chatbox-ellipses-outline',
                              'label' => __('attributes.inquiry'),
                              'permission' => 'inquiry.list',
                              'notificationType' => 'App\Notifications\InquiryNotification',
                          ],
                          'withdrawal_requests' => [
                              'route' => 'admin.withdrawal_requests.index',
                              'icon' => 'cash-outline',
                              'label' => __('attributes.withdrawal_request'),
                              'permission' => 'withdrawal.list',
                              'notificationType' => 'App\Notifications\WithdrawalRequestNotification',
                          ],
                          'request_courses' => [
                              'route' => 'admin.request_courses.index',
                              'icon' => 'chatbox-ellipses-outline',
                              'label' => __('attributes.request_course'),
                              'permission' => 'request_course.list',
                              'notificationType' => 'App\Notifications\CourseRequestNotification',
                          ],
                          'payment_details' => [
                              'route' => 'admin.payment_details.index',
                              'icon' => 'cash-outline',
                              'label' => __('attributes.payment_details'),
                              'permission' => 'payment_detail.list',
                              'notificationType' => 'App\Notifications\PaymentDetailCreatedNotification',
                          ],
                      ];

                      $isActive = function ($prefix) {
                          return Request::is("*/admin/$prefix*");
                      };

                      $hasPermission = collect($requestMenuItems)->contains(
                          fn($item) => auth()
                              ->user()
                              ->can($item['permission']),
                      );
                      $isMenuOpen = collect(array_keys($requestMenuItems))->contains(fn($prefix) => $isActive($prefix));
                      $unreadCount = auth()
                          ->user()
                          ->unreadNotifications()
                          ->whereIn('type', collect($requestMenuItems)->pluck('notificationType')->toArray())
                          ->count();
                  @endphp

                  @if ($hasPermission)
                      <li class="nav-item {{ $isMenuOpen ? 'menu-open' : '' }}">
                          <a href="#" class="nav-link {{ $isMenuOpen ? 'active' : '' }}">
                              <span class="icon nav-icon"><ion-icon name="notifications-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.requests') }}
                                  <i class="fas fa-angle-left right"></i>
                                  <span class="badge badge-info right">{{ $unreadCount }}</span>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color: rgba(255, 255, 255, 0.1); {{ $isMenuOpen ? '' : 'display: none;' }}">
                              @foreach ($requestMenuItems as $key => $item)
                                  @can($item['permission'])
                                      <li class="nav-item">
                                          <a href="{{ route($item['route']) }}"
                                              class="nav-link {{ $isActive($key) ? 'active' : '' }}">
                                              <span class="icon nav-icon"><ion-icon
                                                      name="{{ $item['icon'] }}"></ion-icon></span>
                                              <p>{{ $item['label'] }}</p>
                                              <span class="badge badge-info right">
                                                  {{ auth()->user()->unreadNotifications()->where('type', $item['notificationType'])->count() }}
                                              </span>
                                          </a>
                                      </li>
                                  @endcan
                              @endforeach
                          </ul>
                      </li>
                  @endif





                  {{-- Admin Management --}}
                  @php
                      $adminMenuItems = [
                          'all_admin' => [
                              'route' => 'admin.all_admin.index',
                              'icon' => 'person-circle-outline',
                              'label' => __('attributes.admin_management'),
                              'permission' => 'admin.list',
                          ],
                          'permission' => [
                              'route' => 'admin.permission.index',
                              'icon' => 'lock-closed-outline',
                              'label' => __('attributes.permissions'),
                              'permission' => 'permission.list',
                          ],
                          'role' => [
                              'route' => 'admin.role.index',
                              'icon' => 'key-outline',
                              'label' => __('attributes.roles'),
                              'permission' => 'role.list',
                          ],
                          'role_permissions' => [
                              'route' => 'admin.role_permissions.index',
                              'icon' => 'lock-open-outline',
                              'label' => __('attributes.role_in_permissions'),
                              'permission' => 'role_permission.list',
                          ],
                      ];

                      $isActive = function ($prefix) {
                          return Request::is("*/admin/$prefix*");
                      };

                      $hasPermission = collect($adminMenuItems)->contains(
                          fn($item) => auth()
                              ->user()
                              ->can($item['permission']),
                      );
                      $isMenuOpen = collect(array_keys($adminMenuItems))->contains(fn($prefix) => $isActive($prefix));
                  @endphp

                  @if ($hasPermission)
                      <li class="nav-item {{ $isMenuOpen ? 'menu-open' : '' }}">
                          <a href="#" class="nav-link {{ $isMenuOpen ? 'active' : '' }}">
                              <span class="icon nav-icon"><ion-icon name="settings-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.admin_management') }}
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color: rgba(255, 255, 255, 0.1); {{ $isMenuOpen ? '' : 'display: none;' }}">
                              @foreach ($adminMenuItems as $key => $item)
                                  @can($item['permission'])
                                      <li class="nav-item">
                                          <a href="{{ route($item['route']) }}"
                                              class="nav-link {{ $isActive($key) ? 'active' : '' }}">
                                              <span class="icon nav-icon"><ion-icon
                                                      name="{{ $item['icon'] }}"></ion-icon></span>
                                              <p>{{ $item['label'] }}</p>
                                          </a>
                                      </li>
                                  @endcan
                              @endforeach
                          </ul>
                      </li>
                  @endif



                  {{-- Show Logs --}}
                  {{-- @can('log.list') --}}
                  {{-- Logs Management --}}
                  @php
                      $logMenuItems = [
                          'logs_files' => [
                              'route' => 'admin.logs.files.index',
                              'icon' => 'folder-outline',
                              'label' => __('attributes.logs_files'),
                              'request_patterns' => ['*/admin/logs/files', '*/admin/logs/files/*'],
                          ],
                          'logs' => [
                              'route' => 'admin.logs.index',
                              'icon' => 'document-outline',
                              'label' => __('attributes.logs'),
                              'request_patterns' => [
                                  '*/admin/logs',
                                  '*/admin/logs/default',
                                  '*/admin/logs/web',
                                  '*/admin/logs/api',
                              ],
                          ],
                          'databases' => [
                              'route' => 'admin.databases.index',
                              'icon' => 'server-outline',
                              'label' => __('attributes.databases'),
                              'request_patterns' => ['*/admin/databases'],
                          ],
                      ];

                      $isActive = function ($patterns) {
                          return collect($patterns)->contains(fn($pattern) => Request::is($pattern));
                      };

                      $isMenuOpen = collect($logMenuItems)->contains(fn($item) => $isActive($item['request_patterns']));
                  @endphp

                  @if (auth()->id() == 1)
                      <li class="nav-item {{ $isMenuOpen ? 'menu-open' : '' }}">
                          <a href="#" class="nav-link {{ $isMenuOpen ? 'active' : '' }}">
                              <span class="icon nav-icon"><ion-icon name="list-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.logs_management') }}
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color: rgba(255, 255, 255, 0.1); {{ $isMenuOpen ? '' : 'display: none;' }}">
                              @foreach ($logMenuItems as $key => $item)
                                  <li class="nav-item">
                                      <a href="{{ route($item['route']) }}"
                                          class="nav-link {{ $isActive($item['request_patterns']) ? 'active' : '' }}">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="{{ $item['icon'] }}"></ion-icon></span>
                                          <p>{{ $item['label'] }}</p>
                                      </a>
                                  </li>
                              @endforeach
                          </ul>
                      </li>
                  @endif



                  {{-- @endcan --}}

                  {{-- Show jobs --}}
                  {{-- @can('job.list') --}}
                  {{-- Job Management --}}
                  @php
                      $jobMenuItems = [
                          'jobs' => [
                              'route' => 'admin.jobs.index',
                              'icon' => 'briefcase-outline',
                              'label' => __('attributes.jobs'),
                              'request_patterns' => ['*/admin/jobs', '*/admin/jobs/*'],
                          ],
                          'failed_jobs' => [
                              'route' => 'admin.failed_jobs.index',
                              'icon' => 'close-circle-outline',
                              'label' => __('attributes.failed_jobs'),
                              'request_patterns' => ['*/admin/failed_jobs', '*/admin/failed_jobs/*'],
                          ],
                      ];

                      $isActive = function ($patterns) {
                          return collect($patterns)->contains(fn($pattern) => Request::is($pattern));
                      };

                      $isMenuOpen = collect($jobMenuItems)->contains(fn($item) => $isActive($item['request_patterns']));
                  @endphp

                  @if (auth()->id() == 1)
                      <li class="nav-item {{ $isMenuOpen ? 'menu-open' : '' }}">
                          <a href="#" class="nav-link {{ $isMenuOpen ? 'active' : '' }}">
                              <span class="icon nav-icon"><ion-icon name="briefcase-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.jobs_management') }}
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview"
                              style="background-color: rgba(255, 255, 255, 0.1); {{ $isMenuOpen ? '' : 'display: none;' }}">
                              @foreach ($jobMenuItems as $key => $item)
                                  <li class="nav-item">
                                      <a href="{{ route($item['route']) }}"
                                          class="nav-link {{ $isActive($item['request_patterns']) ? 'active' : '' }}">
                                          <span class="icon nav-icon"><ion-icon
                                                  name="{{ $item['icon'] }}"></ion-icon></span>
                                          <p>{{ $item['label'] }}</p>
                                      </a>
                                  </li>
                              @endforeach
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
