 <!-- Navbar -->
 <div class="topbar">

     <nav class="main-header navbar navbar-expand navbar-white navbar-light">
         <!-- Left navbar links -->
         <ul class="navbar-nav">
             <li class="nav-item">
                 <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
             </li>
             <li class="nav-item d-none d-sm-inline-block">
                 <a href="{{ route('admin.clear_cache') }}" class="nav-link btn btn-default">
                     <i class="fas fa-sync"></i>
                     {{ __('main.clear_cache') }}
                 </a>
             </li>
         </ul>

         <!-- Right navbar links -->
         <ul class="ml-auto navbar-nav">
             <!-- Navbar Search -->
             {{-- change lang --}}
             <li class="nav-item dropdown">
                 <a class="nav-link" data-toggle="dropdown" href="#">
                     <i class="fas fa-globe"></i> {{ LaravelLocalization::getCurrentLocaleName() }}
                 </a>
                 <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                     <div class="dropdown-divider"></div>
                     <a class="dropdown-item d-flex align-items-center justify-content-between" rel="alternate"
                         hreflang="ar" href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                         <img src="{{ asset('asset/admin/imgs/flags/EG.png') }}" alt=""
                             style="width: 20px;height: 20px;">
                         العربية
                     </a>
                     <div class="dropdown-divider"></div>
                     <a class="dropdown-item d-flex align-items-center justify-content-between" rel="alternate"
                         hreflang="en" href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                         <img src="{{ asset('asset/admin/imgs/flags/US.png') }}" alt=""
                             style="width: 20px;height: 20px;">
                         English
                     </a>

                 </div>
             </li>
             <li class="nav-item">
                 <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                     <i class="fas fa-search"></i>
                 </a>
                 <div class="navbar-search-block">
                     <form class="form-inline">
                         <div class="input-group input-group-sm">
                             <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                 aria-label="Search">
                             <div class="input-group-append">
                                 <button class="btn btn-navbar" type="submit">
                                     <i class="fas fa-search"></i>
                                 </button>
                                 <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                     <i class="fas fa-times"></i>
                                 </button>
                             </div>
                         </div>
                     </form>
                 </div>
             </li>

             <!-- Messages Dropdown Menu -->
             <!--
             <li class="nav-item dropdown">
                 <a class="nav-link" data-toggle="dropdown" href="#">
                     <i class="far fa-comments"></i>
                     {{-- <span class="badge badge-danger navbar-badge">3</span> --}}
                 </a>
                 <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                     {{-- <a href="#" class="dropdown-item">
                         <!-- Message Start -->
                         <div class="media">
                             <img src="dist/img/user1-128x128.jpg" alt="User Avatar"
                                 class="mr-3 img-size-50 img-circle">
                             <div class="media-body">
                                 <h3 class="dropdown-item-title">
                                     Brad Diesel
                                     <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                 </h3>
                                 <p class="text-sm">Call me whenever you can...</p>
                                 <p class="text-sm text-muted"><i class="mr-1 far fa-clock"></i> 4 Hours Ago</p>
                             </div>
                         </div>
                         <!-- Message End -->
                     </a>
                     <div class="dropdown-divider"></div> --}}

                     <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                 </div>
             </li>
             -->
             <!-- Notifications Dropdown Menu -->
             @php
                 $user = auth()
                     ->user()
                     ->loadCount('unreadNotifications')
                     ->load([
                         'notifications' => function ($query) {
                             $query->latest()->take(5);
                         },
                     ]);

                 $unreadNotificationsCount = $user->unread_notifications_count;
                 $latestNotifications = $user->notifications;
             @endphp
             <li class="nav-item dropdown">
                 <a class="nav-link" data-toggle="dropdown" href="#">
                     <i class="far fa-bell"></i>
                     <span class="badge badge-warning navbar-badge">{{ $unreadNotificationsCount }}</span>
                 </a>
                 <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                     <span class="dropdown-item dropdown-header">{{ $unreadNotificationsCount }}
                         الإشعارات</span>
                     @foreach ($latestNotifications as $notification)
                         <div class="dropdown-divider"></div>
                         <a href="#" class="dropdown-item notification" data-id="{{ $notification->id }}"
                             data-url="{{ $notification->data['action'] }}"
                             @if ($notification->read_at == null) style="background-color:#f1f4f8" @endif>
                             <i class="mr-2 {{ $notification->data['icon'] }}"></i>
                             {{ $notification->data['title'] }}
                             <span class="float-right text-sm text-muted"
                                 title="{{ $notification->created_at }}">{{ $notification->created_at->diffForHumans() }}</span>
                             <div class="pl-1 mx-4 dropdown-message">
                                 <p class="text-sm">{{ $notification->data['message'] }}</p>
                             </div>
                         </a>
                     @endforeach

                     <div class="dropdown-divider"></div>
                     <a href="{{ route('admin.notifications.index') }}"
                         class="dropdown-item dropdown-footer">{{ __('main.see_all_notifications') }}</a>
                 </div>
             </li>

             <li class="nav-item">
                 <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                     <i class="fas fa-expand-arrows-alt"></i>
                 </a>
             </li>

             <li class="nav-item dropdown">
                 <a class="nav-link" data-toggle="dropdown" href="#">
                     <div class="user">
                         <x-custom.profile-picture :user="auth()->user()" size="30" />
                     </div>
                 </a>
                 <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="width: 200px;min-width:200px">

                     <a href="{{ route('admin.profile') }}"
                         class="dropdown-item dropdown-footer d-flex justify-content-between align-items-center">
                         {{ __('main.profile') }}
                         <i class="fas fa-user"></i>
                     </a>
                     <div class="dropdown-divider"></div>
                     <form action="{{ route('logout') }}" method="POST">
                         @csrf
                         <button type="submit"
                             class="dropdown-item dropdown-footer d-flex justify-content-between align-items-center">
                             {{ __('buttons.logout') }}
                             <i class="fas fa-sign-out-alt"></i>
                         </button>
                     </form>
                     <div class="dropdown-divider"></div>


                 </div>
             </li>
         </ul>
     </nav>
 </div>
