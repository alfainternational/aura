<div class="table-responsive {{ $wrapperClass ?? '' }}">
    <table class="table {{ $class ?? '' }}">
        @if(isset($header))
            <thead class="{{ $headerClass ?? '' }}">
                {{ $header }}
            </thead>
        @endif
        
        <tbody class="{{ $bodyClass ?? '' }}">
            {{ $slot }}
        </tbody>
        
        @if(isset($footer))
            <tfoot class="{{ $footerClass ?? '' }}">
                {{ $footer }}
            </tfoot>
        @endif
    </table>
</div>

@if(isset($pagination))
    <div class="d-flex justify-content-center mt-3">
        {{ $pagination }}
    </div>
@endif
