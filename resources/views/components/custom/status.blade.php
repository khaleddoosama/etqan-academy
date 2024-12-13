@php
    use App\Enums\Status;
@endphp
<div class="row d-inline-flex m-0">
    @if ($model->status === Status::PENDING)
        <form action="{{ route($routeName, $model->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ Status::APPROVED->value }}">
            <button type="submit" class="btn btn-{{ Status::APPROVED->Color() }}" title="{{ __('buttons.approve') }}">
                <i class="fas fa-check"></i>
            </button>
        </form>
        <form action="{{ route($routeName, $model->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ Status::REJECTED->value }}">
            <button type="submit" class="btn btn-{{ Status::REJECTED->Color() }}" title="{{ __('buttons.reject') }}">
                <i class="fas fa-times"></i>
            </button>
        </form>
    @elseif ($model->status === Status::APPROVED)
        <form action="{{ route($routeName, $model->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ Status::REJECTED->value }}">
            <button type="submit" class="btn btn-{{ Status::REJECTED->Color() }}" title="{{ __('buttons.reject') }}">
                <i class="fas fa-times"></i>
            </button>
        </form>
    @elseif ($model->status === Status::REJECTED)
        <form action="{{ route($routeName, $model->id) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="{{ Status::APPROVED->value }}">
            <button type="submit" class="btn btn-{{ Status::APPROVED->Color() }}"
                title="{{ __('buttons.approve') }}">
                <i class="fas fa-check"></i>
            </button>
        </form>
    @endif
</div>
