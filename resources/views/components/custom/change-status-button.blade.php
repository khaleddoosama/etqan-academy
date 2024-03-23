@props(['status', 'route', 'id'])


@if ($status == 0)
    <form action="{{ route($route, $id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="1">
        <button type="submit" class="btn btn-success" title="{{ __('buttons.activate') }}"
            style="color: white; text-decoration: none;">
            <i class="fas fa-toggle-off"></i>
        </button>
    </form>

    <form action="{{ route($route, $id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="2">
        <button type="submit" class="btn btn-danger" title="{{ __('buttons.deactivate') }}"
            style="color: white; text-decoration: none;">
            <i class="fas fa-toggle-on"></i>
        </button>
    </form>
@elseif ($status == 1)
    <form action="{{ route($route, $id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="2">
        <button type="submit" class="btn btn-danger" title="{{ __('buttons.deactivate') }}"
            style="color: white; text-decoration: none;">
            <i class="fas fa-toggle-off"></i>
        </button>
    </form>
@elseif ($status == 2 || $status == 3)
    <form action="{{ route($route, $id) }}" method="POST" style="display: inline-block;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" value="1">
        <button type="submit" class="btn btn-success" title="{{ __('buttons.activate') }}"
            style="color: white; text-decoration: none;">
            <i class="fas fa-toggle-on"></i>
        </button>
    </form>
@endif
