<div class="card card-lecture my-2" data-id="{{ $section->id }}">
    <div class="card-header" id="heading-{{ $section->id }}">
        <h5 class="mb-0 d-flex col-12">
            <div>
                <input type="checkbox" class="section-checkbox mr-2" value="{{ $section->id }}">
            </div>
            <div class="d-flex justify-content-between align-items-center w-100">

                <button class="btn btn-link text-left text-primary text-decoration-none"
                    data-toggle="collapse"
                    data-target="#collapse-{{ $section->id }}"
                    aria-expanded="true"
                    aria-controls="collapse-{{ $section->id }}">
                    <i class="toggle-icon fas fa-chevron-down mr-2 transition" style="transition: transform 0.3s;"></i>

                    @if (auth()->user()->can('section.edit') || auth()->user()->can('section.show'))
                    <a class="btn btn-link btnn text-primary text-decoration-none p-0" href="{{ route('admin.sections.show', $section->id) }}">📁 {{ $section->title }}
                    </a>
                    @else
                    📁 {{ $section->title }}
                    @endif
                </button>
                <div>
                    @can('section.delete')
                    <x-custom.delete-button :route="'admin.sections.destroy'" :id="$section->id" />
                    @endcan
                </div>
            </div>
        </h5>
    </div>

    <div id="collapse-{{ $section->id }}"
        class="collapse" style="visibility: visible;"
        aria-labelledby="heading-{{ $section->id }}">

        {{-- Lectures --}}
        @if ($section->lectures->isNotEmpty())
        <ul class="lecture-list list-group list-group-flush" style="min-height: 30px;" data-section-id="{{ $section->id }}">
            @foreach ($section->lectures as $lecture)
            <li class="list-group-item d-flex align-items-center col-12" data-id="{{ $lecture->id }}">
                <div>
                    <input type="checkbox" class="lecture-checkbox mr-2" value="{{ $lecture->id }}">
                </div>
                <div class="d-flex align-items-center justify-content-between w-100">
                    @if (auth()->user()->can('lecture.edit') || auth()->user()->can('lecture.show'))
                    <a class="btn btn-link btnn text-primary text-decoration-none"
                        href="{{ route('admin.lectures.edit', $lecture->id) }}">
                        🎓 {{ __('attributes.video') }} #{{ $loop->iteration }}: {{ $lecture->title }}
                    </a>
                    @else
                    🎓 {{ __('attributes.video') }} #{{ $loop->iteration }}: {{ $lecture->title }}
                    @endif
                </div>

                <div>
                    @can('lecture.delete')
                    <x-custom.delete-button :route="'admin.lectures.destroy'" :id="$lecture->id" />
                    @endcan
                </div>
            </li>
            @endforeach
        </ul>
        @endif

        {{-- Child Sections --}}
        @if ($section->childrenSections->isNotEmpty())
        <div class="ml-4">
            @foreach ($section->childrenSections as $child)
            @include('admin.section._section_tree', ['section' => $child])
            @endforeach
        </div>
        @endif
    </div>
</div>
