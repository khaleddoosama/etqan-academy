@if (str_contains($type, 'image'))
    <a href="{{ Storage::url($path) }}" target="_blank" data-toggle="lightbox" data-title="{{ $type }}">
        <img src="{{ Storage::url($path) }}" class="img-fluid mb-2" style="max-height: 150px;" alt="{{ $type }}" />
    </a>
@elseif (str_contains($type, 'video'))
    <video controls class="img-fluid mb-2" style="max-height: 150px;">
        <source src="{{ Storage::url($path) }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
@elseif (str_contains($type, 'pdf'))
    <a href="{{ Storage::url($path) }}" target="_blank" class="d-block">
        <embed src="{{ Storage::url($path) }}" type="application/pdf" width="100%" height="150px" />
        {{-- <p>{{ $type }}</p> --}}
    </a>
@else
    <p>Unsupported file type: {{ $type }}</p>
@endif
{{-- Buttons --}}
<div class="d-flex justify-content-between mt-2">
    {{-- Show Button --}}
    <a href="{{ Storage::url($path) }}" target="_blank" class="btn btn-sm btn-success ml-2">
        <i class="fas fa-eye"></i>
    </a>

    {{-- Delete Button --}}
    <form action="{{ route('admin.student_works.destroy', $id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
    </form>
</div>
