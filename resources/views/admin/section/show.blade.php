@extends('admin.master')
@section('title')
    {{ __('buttons.show_section') }}
@endsection
@section('content')
    <div class="content-wrapper">

        <!-- Content Wrapper. Contains page content -->
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('buttons.show_section') }}" />

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
                                        <div class="card">
                                            <div class="card-header" id="heading-{{ $loop->iteration }}">
                                                <h5 class="mb-0 row justify-content-between align-items-center">
                                                    <button class="btn btn-link" data-toggle="collapse"
                                                        data-target="#collapse-{{ $loop->iteration }}" aria-expanded="false"
                                                        aria-controls="collapse-{{ $loop->iteration }}">
                                                        {{ __('attributes.video') }} #{{ $loop->iteration }}:
                                                        {{ $lecture->title }}
                                                    </button>

                                                    <button type="button" class="btn btn-primary"
                                                        title="{{ __('buttons.edit') }}" data-toggle='modal'
                                                        data-target='#editVideoModal-{{ $loop->iteration }}'
                                                        style="color: white; text-decoration: none;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <div class="modal fade" id="editVideoModal-{{ $loop->iteration }}"
                                                        tabindex="-1" role="dialog" aria-labelledby="editVideoModalLabel"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form id="form1"
                                                                    action="{{ route('admin.lectures.update', $lecture) }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @method('PUT')
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editVideoModalLabel">
                                                                            {{ __('buttons.add_video') }}
                                                                        </h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">

                                                                        <input type="hidden" name="section_id"
                                                                            value="{{ $section->id }}">

                                                                        <x-custom.form-group type="text" name="title"
                                                                            value="{{ $lecture->title }}" />

                                                                        <div class='form-group row'>
                                                                            <x-input-label
                                                                                for="input-video-{{ $loop->iteration }}"
                                                                                class="'col-sm-12 col-form-label">{{ __('attributes.video') }}</x-input-label>

                                                                            <div class="input-group col-sm-12'">
                                                                                <div class="custom-file">
                                                                                    <input type="file" name="video"
                                                                                        id="input-video-{{ $loop->iteration }}"
                                                                                        class="custom-file-input"
                                                                                        accept="video/*">
                                                                                    <x-input-label
                                                                                        for="input-video-{{ $loop->iteration }}"
                                                                                        class="custom-file-label col-form-label"
                                                                                        data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('buttons.close') }}</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">{{ __('buttons.save') }}</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </h5>


                                            </div>

                                            <div id="collapse-{{ $loop->iteration }}" class="collapse"
                                                aria-labelledby="heading-{{ $loop->iteration }}" data-parent="#accordion">
                                                <div class="card-body">
                                                    {{ $lecture->description }}

                                                    <video width="320" height="240" controls>
                                                        <source src="{{ asset($lecture->video) }}" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="modal fade" id="createVideoModal" tabindex="-1" role="dialog"
                                aria-labelledby="createVideoModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form id="form1" action="{{ route('admin.lectures.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="createVideoModalLabel">
                                                    {{ __('buttons.add_video') }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <input type="hidden" name="section_id" value="{{ $section->id }}">

                                                <x-custom.form-group type="text" name="title" />

                                                <div class='form-group row'>
                                                    <x-input-label for="input-video"
                                                        class="'col-sm-12 col-form-label">{{ __('attributes.video') }}</x-input-label>

                                                    <div class="input-group col-sm-12'">
                                                        <div class="custom-file">
                                                            <input type="file" name="video" id="input-video"
                                                                class="custom-file-input" accept="video/*">
                                                            <x-input-label for="input-video"
                                                                class="custom-file-label col-form-label"
                                                                data-browse="{{ __('buttons.browse') }}">{{ __('buttons.choose') }}</x-input-label>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- show video --}}
                                                {{-- <div class="form-group">
                                                    <video width="320" height="240" controls  id="video">
                                                        <source src="" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div> --}}
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">{{ __('buttons.close') }}</button>
                                                <button type="submit"
                                                    class="btn btn-primary">{{ __('buttons.save') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>


                            <div class="card-footer">
                                <x-custom.form-submit text="{{ __('buttons.add_lecture') }}" class=" btn-primary"
                                    attr='data-toggle=modal data-target=#createVideoModal' />
                            </div>
                            <!-- form start -->

                        </div>
                        <!-- /.card -->
                    </div>

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('scripts')
    <!-- Page specific script -->
    <script>
        $(function() {
            $('#form1').validate({
                rules: {
                    name: {
                        required: true,
                    },
                },
                messages: {
                    name: {
                        required: "{{ __('validation.required', ['attribute' => __('attributes.name')]) }}"
                    },
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
                }
            });
        });
    </script>

    {{-- <script>
        // when user selects a file #videoInputFile must be show in video with id = #profilePicture
        $(document).ready(function() {
            $('#input-video').on('change', function(event) {
                console.log(11);
                const input = event.target;
                const video = $('#video');
                
                const url = URL.createObjectURL(input.files[0]);

                video.attr('src', url);
                // remove class d-none
            });
        });
    </script> --}}
@endsection
