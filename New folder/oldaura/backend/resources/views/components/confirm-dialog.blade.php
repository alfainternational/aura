<div class="modal fade" id="{{ $id ?? 'confirmDialog' }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header {{ $headerClass ?? '' }}">
                <h5 class="modal-title">{{ $title ?? 'تأكيد' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="{{ $icon ?? 'bi bi-exclamation-triangle' }} fs-1 {{ $iconClass ?? 'text-warning' }}"></i>
                </div>
                
                <p class="text-center fs-5">{{ $slot }}</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn {{ $cancelBtnClass ?? 'btn-secondary' }}" data-bs-dismiss="modal">
                    {{ $cancelText ?? 'إلغاء' }}
                </button>
                
                @if(isset($form) && $form)
                    <form action="{{ $actionUrl }}" method="{{ $method ?? 'POST' }}" class="d-inline">
                        @csrf
                        @if($method === 'DELETE' || $method === 'PUT' || $method === 'PATCH')
                            @method($method)
                        @endif
                        
                        <button type="submit" class="btn {{ $confirmBtnClass ?? 'btn-danger' }}">
                            {{ $confirmText ?? 'تأكيد' }}
                        </button>
                    </form>
                @else
                    <button 
                        type="button" 
                        class="btn {{ $confirmBtnClass ?? 'btn-danger' }}" 
                        id="{{ $confirmBtnId ?? 'confirmAction' }}"
                        {{ isset($actionUrl) ? "data-action=$actionUrl" : '' }}
                    >
                        {{ $confirmText ?? 'تأكيد' }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(isset($js) && $js)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('{{ $confirmBtnId ?? "confirmAction" }}');
        
        confirmBtn.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            
            if (actionUrl) {
                window.location.href = actionUrl;
            }
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('{{ $id ?? "confirmDialog" }}'));
            modal.hide();
        });
    });
</script>
@endif
