<div class="modal fade {{ $class ?? '' }}" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="{{ $show ?? 'true' }}">
    <div class="modal-dialog {{ $size ?? '' }} {{ $centered ?? 'modal-dialog-centered' }} {{ $scrollable ?? 'modal-dialog-scrollable' }}">
        <div class="modal-content">
            @if(isset($header))
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title ?? 'Modal Title' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="modal-body">
                {{ $slot }}
            </div>
            
            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @else
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    @if(isset($saveButton) && $saveButton)
                        <button type="button" class="btn btn-primary">حفظ التغييرات</button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
