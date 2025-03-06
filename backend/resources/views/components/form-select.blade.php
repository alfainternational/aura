<div class="form-group mb-3">
    @if(isset($label))
        <label for="{{ $id }}" class="form-label fw-medium">{{ $label }}
            @if(isset($required) && $required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select 
        id="{{ $id }}"
        name="{{ $name }}"
        class="form-select {{ $class ?? '' }} {{ $errors->has($name) ? 'is-invalid' : '' }}"
        {{ isset($required) && $required ? 'required' : '' }}
        {{ isset($readonly) && $readonly ? 'readonly' : '' }}
        {{ isset($disabled) && $disabled ? 'disabled' : '' }}
        {{ isset($multiple) && $multiple ? 'multiple' : '' }}
        {{ $attributes ?? '' }}
    >
        @if(isset($placeholder))
            <option value="">{{ $placeholder }}</option>
        @endif
        
        {{ $slot }}
    </select>
    
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    @if(isset($helpText))
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
