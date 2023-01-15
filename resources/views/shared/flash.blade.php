@if($flash = flash()->get())
    <div class="{{ $flash->getClass() }} p-5">
        {{ $flash->getMessage() }}
    </div>
@endif
