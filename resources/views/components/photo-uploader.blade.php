<div>
    {{-- <h4 class="mb-3">{{ $title ?? 'Upload Photos' }}</h4> --}}
    <div id="preview-container-{{ $id }}" class="d-flex flex-wrap mb-2"></div>
    <div class="custom-dropzone mb-5" id="file-preview-container-{{ $id }}">
        <div class="dz-message text-body-tertiary text-opacity-75">
            <div class="mb-0">
                Drag your photo here<span class="text-body-secondary px-1">or</span>
                <button class="btn btn-link p-0" type="button" id="file-browse-button-{{ $id }}">Browse from device</button>
            </div>
            <div id="alert-container-{{ $id }}" class="d-none">
                <small class="text-danger">You can only upload a maximum of {{ $maxPhotos ?? 1 }} photos.</small>
            </div>
            <img class="mt-3 me-2" src="../../../assets/img/icons/image-icon.png" width="40" alt="" />
        </div>
        <input type="file" name="{{ $name ?? 'photos[]' }}" id="file-input-{{ $id }}" class="d-none" multiple accept="image/*" />
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('file-input-{{ $id }}');
        const previewContainer = document.getElementById('preview-container-{{ $id }}');
        const filePreviewContainer = document.getElementById('file-preview-container-{{ $id }}');
        const alertContainer = document.getElementById('alert-container-{{ $id }}');
        const maxPhotos = {{ $maxPhotos ?? 1 }}; // Set the maximum number of photos allowed

        // Open file dialog when clicking the custom dropzone
        filePreviewContainer.addEventListener('click', () => fileInput.click());

        // Prevent default behavior for drag-and-drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            filePreviewContainer.addEventListener(eventName, (e) => e.preventDefault());
            filePreviewContainer.addEventListener(eventName, (e) => e.stopPropagation());
        });

        // Highlight dropzone on dragover
        filePreviewContainer.addEventListener('dragover', () => {
            filePreviewContainer.classList.add('border-primary');
        });

        // Remove highlight on dragleave
        filePreviewContainer.addEventListener('dragleave', () => {
            filePreviewContainer.classList.remove('border-primary');
        });

        // Handle file drop
        filePreviewContainer.addEventListener('drop', (e) => {

            filePreviewContainer.classList.remove('border-primary');

            const files = e.dataTransfer.files;
            const totalFiles = previewContainer.children.length + files.length;

            if (totalFiles > maxPhotos) {
                alertContainer.classList.remove('d-none'); // Show the alert
                return;
            } else {
                alertContainer.classList.add('d-none'); // Hide the alert
            }

            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'position-relative me-2';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.addEventListener('click', () => {
                        previewDiv.remove();
                        removeFile(file);
                    });

                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeBtn);
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        });

        // Handle file input change
        fileInput.addEventListener('change', function () {
            const currentFiles = Array.from(fileInput.files);
            const totalFiles = previewContainer.children.length + currentFiles.length;

            if (totalFiles > maxPhotos) {
                alertContainer.classList.remove('d-none'); // Show the alert
                fileInput.value = ''; // Clear the input to prevent excess files
                return;
            } else {
                alertContainer.classList.add('d-none'); // Hide the alert
            }

            currentFiles.forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'position-relative me-2';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.addEventListener('click', () => {
                        previewDiv.remove();
                        removeFile(file);
                    });

                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeBtn);
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        });

        // Remove file from input
        function removeFile(fileToRemove) {
            const dataTransfer = new DataTransfer();
            Array.from(fileInput.files).forEach(file => {
                if (file !== fileToRemove) {
                    dataTransfer.items.add(file);
                }
            });
            fileInput.files = dataTransfer.files;
        }
    });
</script>

<style>
    .custom-dropzone {
        border: 2px dashed #ccc;
        border-radius: 10px;
        padding: 50px;
        text-align: center;
        cursor: pointer;
        background-color: #f9f9f9;
    }

    .custom-dropzone:hover {
        background-color: #f1f1f1;
    }

    .custom-dropzone.border-primary {
        border-color: #007bff !important;
        background-color: #e9f5ff;
    }

    #preview-container-{{ $id }} img {
        width: 80px;
        height: 80px;
        padding: 18px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: white;
    }

    #preview-container-{{ $id }} .remove-btn {
        position: absolute;
        top: 0;
        right: 0;
        background: none;
        color: black;
        border: none;
        border-radius: 5px;
        font-size: 15px;
        cursor: pointer;
    }
</style>
