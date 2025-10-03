{{-- Action buttons for accounting entries --}}
<div class="btn-group" role="group">
    @can('accounting_entry.edit')
        <a href="{{ route('admin.accounting.entries.edit', $row->id) }}" 
           class="btn btn-sm btn-primary" 
           title="{{ __('buttons.edit') }}">
            <i class="fas fa-edit"></i>
        </a>
    @endcan
    
    @can('accounting_entry.delete')
        <button type="button" 
                class="btn btn-sm btn-danger" 
                onclick="confirmDelete({{ $row->id }})"
                title="{{ __('buttons.delete') }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>

<script>
function confirmDelete(entryId) {
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("You will not be able to revert this entry!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ __("buttons.yes_delete") }}',
        cancelButtonText: '{{ __("buttons.cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create and submit delete form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/accounting/entries/' + entryId;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Add DELETE method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
