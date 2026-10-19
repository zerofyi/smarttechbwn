<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">{{ $title }}</h5>
                <button class="btn btn-close p-1" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-body-tertiary lh-lg mb-0">{{ $message }}</p>
            </div>
            <div class="modal-footer">
                <form method="{{ $method }}" action="{{ $action }}">
                    @csrf
                    @method($method)
                    <button type="submit" class="btn {{ $confirmBtnClass }}">{{ $confirmText }}</button>
                    <button type="button" class="btn {{ $cancelBtnClass }}" data-bs-dismiss="modal">{{ $cancelText }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
