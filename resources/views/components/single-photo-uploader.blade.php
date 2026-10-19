<div>
    <h4 class="mb-3">{{ $title ?? 'Upload Photo' }}</h4>
    <div class="text-center">
        <label for="{{ $id }}" class="d-block">
            <div class="border border-2 border-dashed rounded p-2 dropzone" id="dropzone-{{ $id }}" style="cursor: pointer; background-color: #f8f9fa;">
                <img id="preview-{{ $id }}" src="{{ $defaultImage ?? '../../../assets/img/icons/cloud-upload.svg' }}" alt="your image" class="img-fluid mb-3" style="max-height: 150px;" />
                <p class="text-muted">Click or drag to upload</p>
            </div>
        </label>
        <input class="form-control d-none" id="{{ $id }}" type="file" accept=".jpg, .jpeg, .png, .webp" name="{{ $name ?? 'photo' }}" {{ $required ? 'required' : '' }} />
        <div class="invalid-feedback">Please select a valid image.</div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('{{ $id }}');
        const previewImage = document.getElementById('preview-{{ $id }}');
        const dropzone = document.getElementById('dropzone-{{ $id }}');

        // Handle file input change
        fileInput.addEventListener('change', (evt) => {
            const [file] = fileInput.files;
            if (file) {
                previewImage.src = URL.createObjectURL(file);
            }
        });

        // Prevent default behavior for drag-and-drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => e.preventDefault());
            dropzone.addEventListener(eventName, (e) => e.stopPropagation());
        });

        // Highlight dropzone on dragover
        dropzone.addEventListener('dragover', () => {
            dropzone.classList.add('border-primary');
        });

        // Remove highlight on dragleave
        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-primary');
        });

        // Handle file drop
        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files; // Assign dropped files to the input
                const [file] = files;
                if (file) {
                    previewImage.src = URL.createObjectURL(file);
                }
            }
            dropzone.classList.remove('border-primary');
        });
    });
</script>
<style>
    .dropzone {
        transition: border-color 0.3s ease;
    }

    .dropzone.border-primary {
        border-color: #007bff !important;
        background-color: #e9f5ff;
    }
</style>
