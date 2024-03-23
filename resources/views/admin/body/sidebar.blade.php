  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('admin.home') }}" class="brand-link">
          <img src="{{ asset('asset/admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
              class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">{{ __('main.dashboard') }}</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar user panel (optional) -->
          <div class="pb-3 mt-3 mb-3 user-panel d-flex">
              <div class="image">
                  <img src="{{ asset('asset/admin/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                      alt="User Image">
              </div>
              <div class="info">
                  <a href="#" class="d-block">{{ Auth::user()->name }}</a>
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
                  {{-- @can('user.list') --}}
                  <li class="nav-item @if (Request::is('*/admin/users') || Request::is('*/admin/users/*')) menu-open @endif">
                      <a href="#" class="nav-link  @if (Request::is('*/admin/users') || Request::is('*/admin/users/*')) active @endif">
                          <i class="nav-icon fas fa-users"></i>
                          <p>
                              {{ __('attributes.users') }}
                          </p>
                      </a>
                      <ul class="nav nav-treeview" style=" @if (!(Request::is('*/admin/users') || Request::is('*/admin/users/*'))) display: none @endif">
                          <li class="nav-item">
                              <a href="{{ route('admin.users.pending') }}"
                                  class="nav-link @if (Request::is('*/admin/users/pending') || Request::is('*/admin/users/pending/*')) active @endif">
                                  <i class="far fa-circle nav-icon"></i>
                                  <p>
                                      {{ __('attributes.users_pending') }}
                                  </p>
                              </a>
                          </li>
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
                  {{-- @endcan --}}

                  {{--  categories  --}}
                  {{-- @can('category.list') --}}
                  <li class="nav-item">
                      <a href="{{ route('admin.categories.index') }}"
                          class="nav-link @if (Request::is('*/admin/categories') || Request::is('*/admin/categories/*')) active @endif">
                          <i class="nav-icon fas fa-th"></i>
                          <p>
                              {{ __('attributes.categories') }}
                          </p>
                      </a>
                  </li>
                  {{-- @endcan --}}


                  {{-- Courses --}}
                  {{-- @can('course.list') --}}
                  <li class="nav-item">
                      <a href="{{ route('admin.courses.index') }}"
                          class="nav-link @if (Request::is('*/admin/courses') || Request::is('*/admin/courses/*')) active @endif">
                          <span class="icon nav-icon"><ion-icon name="school-outline"></ion-icon></span>
                          <span class="title">{{ __('attributes.courses') }}</span>
                      </a>
                  </li>
                  {{-- @endcan --}}




                  {{-- Permissions --}}
                  {{-- @can('permission.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.permission.index') }}"
                              class="nav-link @if (Request::is('*/admin/permission') || Request::is('*/admin/permission/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="key-outline"></ion-icon></span>
                              <p>
                                  {{ __('attributes.permissions') }}
                              </p>
                          </a>
                      </li>
                  @endcan --}}

                  {{-- Roles --}}
                  {{-- @can('role.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.role.index') }}"
                              class="nav-link @if (Request::is('*/admin/role') || Request::is('*/admin/role/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="people-outline"></ion-icon></span>
                              <span class="title">{{ __('attributes.roles') }}</span>
                          </a>
                      </li>
                  @endcan --}}

                  {{-- Roles In Permission --}}
                  {{-- @can('role_permission.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.role_permission.index') }}"
                              class="nav-link @if (Request::is('*/admin/role_permission') || Request::is('*/admin/role_permission/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="people-outline"></ion-icon></span>
                              <span class="title">{{ __('attributes.roles_in_permission') }}</span>
                          </a>
                      </li>
                  @endcan --}}

                  {{-- Admins --}}
                  {{-- @can('admin.list')
                      <li class="nav-item">
                          <a href="{{ route('admin.all_admin.index') }}"
                              class="nav-link @if (Request::is('*/admin/all_admin') || Request::is('*/admin/all_admin/*')) active @endif">
                              <span class="icon nav-icon"><ion-icon name="people-outline"></ion-icon></span>
                              <span class="title">{{ __('attributes.admin_manage') }}</span>
                          </a>
                      </li>
                  @endcan --}}
              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>
