@extends('admin.master')
@section('title')
    {{ __('buttons.show_section') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="px-4 mb-3 row">
                    <div class="w-50">
                        <h1> {{ __('buttons.show_section') }} </h1>
                    </div>
                    <div class="w-50">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ __('main.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.courses.index') }}">{{ $section->course->title }}</a></li>

                            <li class="breadcrumb-item active">{{ __('buttons.show_section') }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class=" content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><small>
                                        {{ __('attributes.section') }}: {{ $section->title }}</small></h3>
                            </div>
                            <!-- /.card-header -->
                            {{--  --}}
                            <div class="mx-3 my-3 callout callout-info">
                                <h5>{{ __('attributes.description') }}:</h5>
                                <p>{{ $section->description }}</p>
                            </div>

                            <div class="mx-3 my-3 callout callout-info">
                                <h5>{{ __('attributes.lectures') }}:</h5>
                                <div id="accordion">
                                    @foreach ($section->lectures as $lecture)
                                        <div class="card card-lecture" data-id="{{ $lecture->id }}" style="cursor: grab">
                                            <div class="card-header" id="heading-{{ $loop->iteration }}">
                                                <h5 class="mb-0 row justify-content-between align-items-center">
                                                    <a class="btn btn-link btnn text-primary text-decoration-none"
                                                        {{-- data-toggle="collapse" --}}
                                                        href="{{ route('admin.lectures.edit', $lecture->id) }}"
                                                        {{-- data-target="#collapse-{{ $loop->iteration }}" --}} {{-- aria-expanded="false" --}} {{-- aria-controls="collapse-{{ $loop->iteration }}" --}}
                                                        @if ($lecture->processed == 0) style="color: orange !important"
                                                        @elseif ($lecture->processed == -1)
                                                        style="color: red !important; text-decoration: line-through !important" @endif>
                                                        {{ __('attributes.video') }} #{{ $loop->iteration }}:
                                                        {{ $lecture->title }} @if ($lecture->processed == 0)
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        @endif
                                                    </a>

                                                    <div>

                                                        <x-custom.edit-button :route="'admin.lectures.edit'" :id="$lecture->id" />
                                                        {{-- get compoent called delete-button --}}
                                                        <x-custom.delete-button :route="'admin.lectures.destroy'" :id="$lecture->id" />
                                                    </div>

                                                </h5>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>




                            <div class="card-footer">
                                <x-custom.form-submit text="{{ __('buttons.add_lecture') }}" class="mb-3 btn-primary"
                                    attr='data-toggle=modal data-target=#createVideoModal' />

                                {{-- <x-custom.form-submit text="{{ __('buttons.get_lecture') }}" class="btn-secondary"
                                    attr='data-toggle=modal data-target=#getVideoModal' /> --}}
                            </div>
                            <!-- form start -->

                        </div>
                        <!-- /.card -->
                    </div>

                </div>
                <!-- /.row -->
                @include('admin.section.create-lecture-modal')

                {{-- @include('admin.section.duplicate-lecture-modal') --}}
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('scripts')
    <script>
        // when upload attachments show the attachments preview
        $('#input-attachments').change(function() {
            console.log('attachments');
            $('#showAttachments').show('blind');

            // remove old preview
            $('#showAttachments').empty();

            // get the files
            var files = this.files;

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.createElement('div');
                    preview.classList.add('col-md-6');
                    preview.classList.add('col-12');
                    preview.classList.add('my-3');
                    let type = e.target.result.split(':')[1].split('/')[0];

                    if (type == 'image') {
                        preview.innerHTML = '<img class="img-thumbnail img-fluid" src="' + e.target.result +
                            '"/>';
                    } else if (type == 'video') {
                        preview.innerHTML = '<video style="width: 100%; height: 100%;" controls src="' + e
                            .target.result + '" >';
                    } else if (type == 'audio') {
                        preview.innerHTML =
                            '<audio style="width: 100%; height: 100%;" controls class="audio"  src="' + e.target
                            .result + '" >';
                    } else if (type == 'application') {
                        preview.innerHTML = '<iframe style="width: 100%; height: 100%;" src="' + e.target
                            .result + '" >' + '</iframe>';
                    } else {
                        console.log(e.target);
                        preview.innerHTML = '<a class="text-primary" href="' + e.target.result +
                            '" target="_blank">View attachment</a>';
                    }
                    $('#showAttachments').append(preview);
                }
                reader.readAsDataURL(file);
            }

        });

        // show video from youtube
        $('#input-video').change(function() {
            var videoId = $(this).val();
            var videoUrl = 'https://www.youtube.com/embed/' + videoId + '?enablejsapi=1';
            $('#showVideo').show('blind');
            $('#showVideo').attr('src', videoUrl);
        });
    </script>
    
    <script>
        $('#form1').validate({
            rules: {
                title: {
                    required: true,
                },
                video: {
                    required: true,
                }
            },
            messages: {
                title: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}"
                },
                video: {
                    required: "{{ __('validation.required', ['attribute' => __('attributes.video')]) }}",
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                error.css('padding', '0 7.5px');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },


        });
    </script>
@endsection
