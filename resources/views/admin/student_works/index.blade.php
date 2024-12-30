@extends('admin.master')
@section('title')
    {{ __('attributes.student_works') }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('asset/admin/plugins/ekko-lightbox/ekko-lightbox.css') }}">
    <style>
        .image-container {
            display: flex;
            flex-wrap: wrap;
            border: 2px dashed #ccc;
            padding: 10px;
            position: relative;
        }

        .image-container img {
            width: 150px;
            height: 150px;
            margin: 10px;
            cursor: grab;
        }

        .dragging-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 18px;
            display: none;
            z-index: 10;
        }

        .ui-sortable-helper {
            opacity: 0.8;
        }
    </style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <x-custom.header-page title="{{ __('attributes.student_works') }}" />

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            @can('student_work.create')
                                <div class="card-header" style="display: flex;justify-content: end">
                                    <a href="{{ route('admin.student_works.create') }}" class="btn btn-primary"
                                        style="color: white; text-decoration: none;">
                                        {{ __('buttons.create') }} {{ __('attributes.student_work') }}
                                    </a>
                                </div>
                            @endcan
                            <div class="card-body">

                                <ul class="nav nav-tabs" id="tabs">

                                    @foreach ($studentWorkCategories as $category)
                                        <li class="nav-item">
                                            <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}"
                                                data-toggle="tab" href="#tab-{{ $category->id }}">{{ $category->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach ($studentWorkCategories as $category)
                                        <div class="tab-pane container {{ $loop->iteration == 1 ? 'active' : '' }}"
                                            id="tab-{{ $category->id }}">
                                            <div class="image-container row" style="min-height: 200px"
                                                id="sortable-{{ $category->id }}">
                                                @foreach ($studentWorks->where('student_work_category_id', $category->id) as $studentWork)
                                                    <div class="student-work-item col-sm-2 p-3"
                                                        data-id="{{ $studentWork->id }}" style="cursor: grab"
                                                        data-category="{{ $studentWork->student_work_category_id }}"
                                                        data-sort="{{ $studentWork->title }}">
                                                        <x-media-display :path="$studentWork->path" :type="$studentWork->type"
                                                            :title="$studentWork->title" :id="$studentWork->id" />
                                                    </div>
                                                @endforeach
                                                {{-- Drag and drop message --}}
                                                <div class="dragging-message">Drop your images here</div>

                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            // Make images sortable
            $(".image-container").sortable({
                connectWith: ".image-container",
                update: function(event, ui) {
                    // Get the ID of the active tab
                    const activeTab = $(".nav-tabs .active").attr("href").replace('#tab-', '');


                    var studentWorkOrder = [];
                    $('#sortable-' + activeTab + ' .student-work-item').each(function(index) {
                        studentWorkOrder.push($(this).data('id'));
                    });
                    console.log(studentWorkOrder);
                    $.ajax({
                        url: '{{ route('admin.student_works.updateOrder') }}',
                        method: 'POST',
                        data: {
                            student_works: studentWorkOrder,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // toastr.success
                            toastr.success('Order updated successfully');
                        },
                        error: function(xhr, status, error) {
                            console.log(error);
                            console.log(xhr);
                        }
                    });

                }
            });

            // Handle drag-and-drop image upload
            $(".image-container").on("dragover", function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass("dragging");
                $(this).find(".dragging-message").fadeIn();

            });

            $(".image-container").on("dragleave", function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass("dragging");
                $(this).find(".dragging-message").fadeOut();

            });

            $(".image-container").on("drop", function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass("dragging");
                $(this).find(".dragging-message").fadeOut();

                const files = e.originalEvent.dataTransfer.files;
                const activeTab = $(".nav-tabs .active").attr("href").replace('#tab-', '');
                const container = $(this);

                Array.from(files).forEach(file => {
                    if (file.type.startsWith("image/") || file.type.startsWith("application/pdf") ||
                        file.type.startsWith("video/")) {
                        const reader = new FileReader();
                        reader.onload = function(event) {



                            const formData = new FormData();
                            formData.append('pathes[]',
                                file); // Array syntax for multiple files
                            formData.append('student_work_category_id', activeTab);
                            formData.append('_token', '{{ csrf_token() }}');

                            $.ajax({
                                url: '{{ route('admin.student_works.store') }}',
                                method: 'POST',
                                processData: false,
                                contentType: false,
                                data: formData,
                                success: function(response) {
                                    toastr.success('Image uploaded successfully');
                                    var mediaElement = ''
                                    if (file.type.startsWith("image/")) {
                                        mediaElement = '<img src="' + event.target
                                            .result +
                                            '" alt="Image">';
                                    } else if (file.type.startsWith(
                                            "application/pdf")) {
                                        mediaElement = '<embed src="' + event.target
                                            .result +
                                            '" type="application/pdf" width="100%" height="150px" />';
                                    } else if (file.type.startsWith("video/")) {
                                        mediaElement =
                                            '<video controls class="img-fluid" style="max-height: 150px;"><source src="' +
                                            event.target.result +
                                            '" type="video/mp4" /></video>';
                                    }
                                    mediaElement = $(
                                        '<div class="image-container" id="sortable-tab1">' +
                                        mediaElement + '</div>');
                                    container.append(mediaElement);

                                },
                                error: function(error) {
                                    console.error("Error uploading image:", error);
                                    toastr.error('Error uploading image');
                                }
                            });

                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            $(".image-container img").disableSelection();
        });
    </script>
@endsection
