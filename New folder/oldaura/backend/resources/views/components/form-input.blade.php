<div class="form-group mb-3">
    @if(isset($label))
        <label for="{{ $id }}" class="form-label fw-medium">{{ $label }}
            @if(isset($required) && $required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="input-group">
        @if(isset($prepend))
            <span class="input-group-text">{{ $prepend }}</span>
        @endif
        
        <input 
            type="{{ $type ?? 'text' }}" 
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-control {{ $class ?? '' }} {{ $errors->has($name) ? 'is-invalid' : '' }}"
            placeholder="{{ $placeholder ?? '' }}"
            value="{{ $value ?? old($name) }}"
            {{ isset($required) && $required ? 'required' : '' }}
            {{ isset($readonly) && $readonly ? 'readonly' : '' }}
            {{ isset($disabled) && $disabled ? 'disabled' : '' }}
            {{ isset($min) ? "min=$min" : '' }}
            {{ isset($max) ? "max=$max" : '' }}
            {{ isset($step) ? "step=$step" : '' }}
            {{ isset($pattern) ? "pattern=$pattern" : '' }}
            {{ isset($maxlength) ? "maxlength=$maxlength" : '' }}
            {{ isset($autocomplete) ? "autocomplete=$autocomplete" : '' }}
            {{ $attributes ?? '' }}
        >
        
        @if(isset($append))
            <span class="input-group-text">{{ $append }}</span>
        @endif
        
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    @if(isset($helpText))
        <small class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
