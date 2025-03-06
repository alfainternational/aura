<div class="form-group mb-3">
    @if(isset($label))
        <label for="{{ $id }}" class="form-label fw-medium">{{ $label }}
            @if(isset($required) && $required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <textarea 
        id="{{ $id }}"
        name="{{ $name }}"
        class="form-control {{ $class ?? '' }} {{ $errors->has($name) ? 'is-invalid' : '' }}"
        placeholder="{{ $placeholder ?? '' }}"
        rows="{{ $rows ?? 3 }}"
        {{ isset($required) && $required ? 'required' : '' }}
        {{ isset($readonly) && $readonly ? 'readonly' : '' }}
        {{ isset($disabled) && $disabled ? 'disabled' : '' }}
        {{ isset($maxlength) ? "maxlength=$maxlength" : '' }}
        {{ $attributes ?? '' }}
    >{{ $value ?? old($name) }}</textarea>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    @if(isset($helpText))
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
