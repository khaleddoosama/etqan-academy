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
                            <div class="card-tools float-right">
                                @can('section.edit')
                                <button class="btn btn-primary float-right mx-3" data-toggle="modal" data-target="#editSectionModal">
                                    <i class="fas fa-edit" title="{{ __('buttons.edit') }}"></i>
                                </button>

                                <div class="modal fade" id="editSectionModal">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title text-primary">{{ __('buttons.edit') }} {{ __('attributes.section') }}</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('admin.sections.update', $section->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <x-custom.form-group type="text" name="title" value="{{ $section->title }}" />
                                                    <x-custom.form-group type="text" name="description" value="{{ $section->description }}" />
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">{{ __('buttons.close') }}</button>
                                                        <x-custom.form-submit class="btn-primary" text="{{ __('buttons.update') }}" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                                @endcan
                                @can('section.delete')
                                <x-custom.delete-button :route="'admin.sections.destroy'" :id="$section->id" />
                                @endcan
                            </div>
                        </div>
                        <div class="row justify-content-between">
                            <div class="d-flex justify-content-end mt-3 mx-3">
                                <x-custom.form-submit text="{{ __('buttons.add_lecture') }}" class="mb-3 mx-3 btn-primary"
                                    attr='data-toggle=modal data-target=#createVideoModal' />

                                <x-custom.form-submit text="{{ __('buttons.get_lecture') }}" class="btn-secondary"
                                    attr='data-toggle=modal data-target=#getVideoModal' />

                            </div>
                            <div class="d-flex justify-content-end mt-3 mx-3">

                                <x-custom.form-submit text="{{ __('buttons.add_section') }}" class="mb-3 mx-3 btn-primary"
                                    attr='data-toggle=modal data-target=#createSectionModal' />

                                <x-custom.form-submit text="{{ __('buttons.get_section') }}" class="btn-secondary"
                                    attr='data-toggle=modal data-target=#getSectionModal' />

                            </div>
                        </div>
                        <!-- /.card-header -->
                        {{-- --}}
                        <div class="mx-3 my-3 callout callout-info row justify-content-between">
                            <div>
                                <h5>{{ __('attributes.description') }}:</h5>
                                <p>{{ $section->description }}</p>
                            </div>
                            <div>
                                <h5>{{ __('attributes.parent_section_id') }}:</h5>
                                <p>
                                    @if ($section->parentSection)
                                    <a class="text-primary" href="{{ route('admin.sections.show', $section->parentSection) }}">
                                        {{ $section->parentSection->title }}
                                    </a>
                                    @else
                                    {{ __('attributes.no_parent_section') }} 
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mx-3 mt-2">
                            <button id="delete-selected" class="btn btn-danger">
                                🗑️ حذف المحدد
                            </button>
                        </div>

                        <div class="mx-3 my-3 callout callout-info">
                            <div class="d-flex justify-content-end mb-3">
                                <button id="expandAll" class="btn btn-sm btn-primary mr-2">📂 فتح الكل</button>
                                <button id="collapseAll" class="btn btn-sm btn-danger">📁 إغلاق الكل</button>
                            </div>

                            <h5><input type="checkbox" id="selectAllSections" class="mr-2"> {{ __('attributes.sections') }}:</h5>
                            <ul class="section-list" data-parent-id="{{ $section->id }}">
                                @foreach ($section->childrenSections as $child)
                                <li class="section-item" data-id="{{ $child->id }}">
                                    @include('admin.section._section_tree', ['section' => $child])
                                </li>
                                @endforeach
                            </ul>

                        </div>

                        <ul class="lecture-list list-group mx-3 my-3 callout callout-info" data-section-id="{{ $section->id }}">
                            <h5><input type="checkbox" id="selectAllLectures" class="mr-2"> {{ __('attributes.lectures') }}:</h5>

                            @foreach ($section->lectures as $lecture)
                            <li class="list-group-item lecture-item" data-id="{{ $lecture->id }}" style="cursor: grab">
                                <div class="d-flex col-12">
                                    <div>
                                        <input type="checkbox" class="lecture-checkbox mr-2" value="{{ $lecture->id }}">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <a class="btn btn-link btnn text-primary text-decoration-none"
                                            href="{{ route('admin.lectures.edit', $lecture->id) }}">
                                            🎓 {{ __('attributes.video') }} #{{ $loop->iteration }}: {{ $lecture->title }}
                                        </a>
                                        <x-custom.delete-button :route="'admin.lectures.destroy'" :id="$lecture->id" />
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>






                        <!-- form start -->

                    </div>
                    <!-- /.card -->
                </div>

            </div>
            <!-- /.row -->
            @include('admin.section.create-lecture-modal')

            @include('admin.section.duplicate-lecture-modal')

            @include('admin.section.create-section-modal')

            @include('admin.section.duplicate-section-modal')
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}',
        routes: {
            reassignAndSortLecture: '{{ route("admin.lectures.reassignAndSort") }}',
            reassignAndSortSection: '{{ route("admin.sections.reassignAndSort") }}',
            lecturesUpdateOrder: '{{ route("admin.lectures.updateOrder") }}',
            getSectionsByCourse: '{{ route("admin.sections.get", ":course_id") }}',
            getLecturesBySection: '{{ route("admin.lectures.get", ":section_id") }}',
            sectionsBulkDelete: '{{ route("admin.sections.bulkDelete") }}',
            lecturesBulkDelete: '{{ route("admin.lectures.bulkDelete") }}',
        },
        messages: {
            requiredTitle: "{{ __('validation.required', ['attribute' => __('attributes.title')]) }}",
            requiredVideo: "{{ __('validation.required', ['attribute' => __('attributes.video')]) }}",
            choose: "{{ __('buttons.choose') }}",
            video: "{{ __('attributes.video') }}",
        }
    };
</script>


<script src="{{ asset('asset/admin/dist/js/pages/section.js') }}"></script>
@endsection
