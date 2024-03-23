        <section class="content-header">
            <div class="container-fluid">
                <div class="px-4 mb-3 row">
                    <div class="w-50">
                        <h1> {{ $title }} </h1>
                    </div>
                    <div class="w-50">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('main.dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
