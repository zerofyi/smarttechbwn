@extends('layouts.admin')

{{-- PAGE TITLE HERE --}}
@section('title', 'Admin : Add Product')

{{-- VENDOR CSS START --}}
@section('vendor-style')
@endsection
{{-- VENDOR CSS END --}}

{{-- PAGE CSS START --}}
@section('page-style')
<link href="../../../vendors/dropzone/dropzone.css" rel="stylesheet">
<link href="../../../vendors/choices/choices.min.css" rel="stylesheet">
<link href="../../../vendors/flatpickr/flatpickr.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="../../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
<link href="../../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
<link href="../../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
<link href="../../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
<link href="../../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
<script>
    var phoenixIsRTL = window.config.config.phoenixIsRTL;
    if (phoenixIsRTL) {
    var linkDefault = document.getElementById('style-default');
    var userLinkDefault = document.getElementById('user-style-default');
    linkDefault.setAttribute('disabled', true);
    userLinkDefault.setAttribute('disabled', true);
    document.querySelector('html').setAttribute('dir', 'rtl');
    } else {
    var linkRTL = document.getElementById('style-rtl');
    var userLinkRTL = document.getElementById('user-style-rtl');
    linkRTL.setAttribute('disabled', true);
    userLinkRTL.setAttribute('disabled', true);
    }
</script>
<script src="https://cdn.tiny.cloud/1/ejzg416maubi6r811jtm5z74lxjtcpkz72o6spos7gdcsp4c/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea#myeditorinstance', // Replace this CSS selector to match the placeholder element for TinyMCE
    plugins: 'code table lists',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
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

    .photo-preview-container img {
        width: 80px;
        height: 80px;
        padding: 18px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: white;
    }

    .photo-preview-container .remove-btn {
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
@endsection
{{-- PAGE CSS END --}}

{{-- CONTENT START --}}
@section('content')
<div class="content">
    <nav class="mb-3" aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products') }}">Products</a></li>
        <li class="breadcrumb-item active">Add New Product</li>
      </ol>
    </nav>
    <form class="mb-9" id="product-form" method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3 flex-between-end mb-5">
        <div class="col-auto">
          <h2 class="mb-2">Add a product</h2>
          <h5 class="text-body-tertiary fw-semibold">Orders placed across your store</h5>
        </div>
        <div class="col-auto">
            <button class="btn btn-phoenix-secondary me-2 mb-2 mb-sm-0" type="button">Discard</button>
            <button class="btn btn-phoenix-primary me-2 mb-2 mb-sm-0" type="button">Save draft</button>
            <button class="btn btn-primary mb-2 mb-sm-0" type="submit">Publish product</button>
        </div>
      </div>
      <div class="row g-5">
        <div class="col-12 col-xl-8">
          <h4 class="mb-3">Product Title</h4><input class="form-control mb-5" type="text" name="title" required placeholder="Write title here..." />
          <div class="mb-6">
            <h4 class="mb-3"> Product Description</h4>
            <textarea class="tinymce" name="description" data-tinymce='{"height":"15rem","placeholder":"Write a description here..."}'></textarea>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Product Variants</h4>
            <button type="button" id="add-variant-button" class="btn btn-primary btn-sm">Add New Variant</button>
          </div>

          <div class="card mb-3" id="variant-1">
            <div class="card-body">
              <div class="row gx-3">
                <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
                  <h4 class="mb-0 text-body-highlight">Variant 1</h4>
                </div>

                <!-- Inline photo uploader replaces x-photo-uploader component -->
                <div class="col-12 mb-4">
                    <div class="photo-preview-container d-flex flex-wrap mb-2" id="preview-container-variant-1"></div>
                    <div class="custom-dropzone mb-5" id="file-preview-container-variant-1">
                        <div class="dz-message text-body-tertiary text-opacity-75">
                            <div class="mb-0">
                                Drag your photo here<span class="text-body-secondary px-1">or</span>
                                <button class="btn btn-link p-0" type="button" id="file-browse-button-variant-1">Browse from device</button>
                            </div>
                            <div id="alert-container-variant-1" class="d-none">
                                <small class="text-danger">You can only upload a maximum of 4 photos.</small>
                            </div>
                            <img class="mt-3 me-2" src="../../../assets/img/icons/image-icon.png" width="40" alt="" />
                        </div>
                        <input type="file" name="variants[1][photos][]" id="file-input-variant-1" class="d-none" multiple accept="image/*" />
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                  <div class="mb-4">
                    <div class="d-flex flex-wrap mb-2">
                      <h5 class="mb-0 text-body-highlight me-2">SKU</h5>
                    </div>
                    <input type="text" class="form-control mb-3" placeholder="SKU" name="variants[1][sku]" required/>
                  </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                  <div class="mb-4">
                    <div class="d-flex flex-wrap mb-2">
                      <h5 class="mb-0 text-body-highlight me-2">Price</h5>
                    </div>
                    <input type="number" class="form-control mb-3" placeholder="Price" name="variants[1][price]" required/>
                  </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                  <div class="mb-4">
                    <div class="d-flex flex-wrap mb-2">
                      <h5 class="mb-0 text-body-highlight me-2">MRP</h5>
                    </div>
                    <input type="text" class="form-control mb-3" placeholder="MRP" name="variants[1][compare_at_price]" required/>
                  </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                  <div class="d-flex flex-wrap mb-2">
                    <h5 class="mb-0 text-body-highlight me-2">Status</h5>
                  </div>
                  <select class="form-select" name="variants[1][status]" aria-label="status" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="mb-4">
                      <div class="d-flex flex-wrap mb-2">
                        <h5 class="mb-0 text-body-highlight me-2">Barcode</h5>
                      </div>
                      <input type="text" class="form-control mb-3" placeholder="Barcode" name="variants[1][barcode]" />
                    </div>
                </div>

                <div id="attribute-options-container-1" class="col-12 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0 text-body-highlight">Options</h5>
                        <button type="button" id="add-option-button-1" class="btn btn-falcon-default btn-sm">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    <!-- Option 1 (Default) -->
                    <div class="row gx-3 align-items-center mb-3 attribute-option">
                        <div class="col-11 col-sm-5">
                            <select id="attribute-select-1-1" class="form-select attribute-select" data-option-index="1-1" name="variants[1][attributes][1][attribute_id]">
                                <option value="">Select Attribute</option>
                                <!-- Attributes will be dynamically populated here -->
                            </select>
                        </div>
                        <div class="col-11 col-sm-5">
                            <select id="value-select-1-1" class="form-select value-select" data-option-index="1-1" name="variants[1][attributes][1][value_id]">
                                <option value="">Select Value</option>
                                <!-- Values will be dynamically populated based on the selected attribute -->
                            </select>
                        </div>
                        <div class="col-1 col-sm-2">
                            <!-- Initially hidden for the first option -->
                            <button type="button" class="btn btn-falcon-default btn-sm remove-option d-none">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-4">
          <div class="row g-2">
            <div class="col-12 col-xl-12">
              <div class="card mb-3">
                <div class="card-body">
                    <div class="col-12 col-sm-6 col-xl-12">
                        <x-single-photo-uploader
                            id="productImage"
                            title="Product Image"
                            name="product_image"
                            :required="true"
                            default-image="../../../assets/img/icons/cloud-upload.svg"
                        />
                    </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-xl-12">
              <div class="card mb-3">
                <div class="card-body">
                  <h4 class="card-title mb-4">Organize</h4>
                  <div class="row gx-3">
                    <div class="col-12 col-sm-6 col-xl-12">
                      <div class="mb-4">
                        <div class="d-flex flex-wrap mb-2">
                          <h5 class="mb-0 text-body-highlight me-2">Category</h5><a class="fw-bold fs-9" target="_blank" href="{{ route('admin.categories') }}">Add new category</a>
                        </div>
                        <select class="form-select mb-3" name="category_id" aria-label="category" required>
                        </select>
                      </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-12">
                      <div class="mb-4">
                        <div class="d-flex flex-wrap mb-2">
                          <h5 class="mb-0 text-body-highlight me-2">Brand</h5><a class="fw-bold fs-9" target="_blank" href="{{ route('admin.brands') }}">Add new brand</a>
                        </div>
                        <select class="form-select mb-3" name="brand_id" aria-label="brand" required>
                        </select>
                      </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-12">
                        <div class="mb-4">
                            <div class="d-flex flex-wrap mb-2">
                                <h5 class="mb-0 text-body-highlight me-2">Product condition</h5>
                            </div>
                            <select class="form-select" name="product_condition" aria-label="Product condition" required>
                                <option value="new">New</option>
                                <option value="refurbished" selected>Refurbished</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex flex-wrap mb-2">
                                <h5 class="mb-0 text-body-highlight me-2">Tag</h5>
                            </div>
                            <select class="form-select" name="tags" aria-label="tag" required>
                                <option value="Lowest Price">Lowest Price</option>
                                <option value="Flash Sale">Flash Sale</option>
                                <option value="New Arrival">New Arrival</option>
                                <option value="Best Seller">Best Seller</option>
                                <option value="Limited Edition">Limited Edition</option>
                                <option value="Clearance">Clearance</option>
                                <option value="Hot Deal">Hot Deal</option>
                                <option value="Trending">Trending</option>
                                <option value="Exclusive">Exclusive</option>
                                <option value="Top Rated">Top Rated</option>
                                <option value="Online Only">Online Only</option>
                                <option value="Back in Stock">Back in Stock</option>
                                <option value="Limited Time Offer">Limited Time Offer</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex flex-wrap mb-2">
                                <h5 class="mb-0 text-body-highlight me-2">Featured</h5>
                            </div>
                            <select class="form-select" name="is_featured" aria-label="featured" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="d-flex flex-wrap mb-2">
                            <h5 class="mb-0 text-body-highlight me-2">Rating</h5>
                        </div>
                        <select class="form-select" name="rating" aria-label="rating" required>
                            <option value="5">5 Star</option>
                            <option value="4.8">4.8 Star</option>
                            <option value="4.5" selected>4.5 Star</option>
                            <option value="4.3">4.3 Star</option>
                            <option value="4">4 Star</option>
                            <option value="3.8">3.8 Star</option>
                            <option value="3.5">3.5 Star</option>
                            <option value="3.3">3.3 Star</option>
                            <option value="3">3 Star</option>
                            <option value="2.8">2.8 Star</option>
                            <option value="2.5">2.5 Star</option>
                            <option value="2.3">2.3 Star</option>
                            <option value="2">2 Star</option>
                            <option value="1.8">1.8 Star</option>
                            <option value="1.5">1.5 Star</option>
                            <option value="1.3">1.3 Star</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-12">
      </div>
    </form>

    <x-admin.footer />
</div>
@endsection
{{-- CONTENT END --}}

{{-- VENDOR JS START --}}
@section('vendor-script')
@endsection
{{-- VENDOR JS END --}}

{{-- PAGE JS START --}}
@section('page-script')
<script src="../../../vendors/popper/popper.min.js"></script>
<script src="../../../vendors/bootstrap/bootstrap.min.js"></script>
<script src="../../../vendors/anchorjs/anchor.min.js"></script>
<script src="../../../vendors/is/is.min.js"></script>
<script src="../../../vendors/fontawesome/all.min.js"></script>
<script src="../../../vendors/lodash/lodash.min.js"></script>
<script src="../../../vendors/list.js/list.min.js"></script>
<script src="../../../vendors/feather-icons/feather.min.js"></script>
<script src="../../../vendors/dayjs/dayjs.min.js"></script>
<script src="../../../vendors/dropzone/dropzone-min.js"></script>
<script src="../../../vendors/choices/choices.min.js"></script>
<script src="../../../vendors/flatpickr/flatpickr.min.js"></script>
<script src="../../../assets/js/phoenix.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Photo Uploaders
        initPhotoUploader('variant-1', 4);      // First variant photos

        // Fetch data from the get-data route
        fetch("{{ route('admin.get-data') }}")
            .then(response => response.json())
            .then(data => {
                // Store data globally for reuse
                window.appData = data;

                // Populate categories
                const categorySelect = document.querySelector('select[name="category_id"]');
                if (data.categories) {
                    categorySelect.innerHTML = '<option value="">Select Category</option>';
                    data.categories.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.category_id;
                        option.textContent = category.name;
                        categorySelect.appendChild(option);
                    });
                }

                // Populate brands
                const brandSelect = document.querySelector('select[name="brand_id"]');
                if (data.brands) {
                    brandSelect.innerHTML = '<option value="">Select Brand</option>';
                    data.brands.forEach(brand => {
                        const option = document.createElement('option');
                        option.value = brand.brand_id;
                        option.textContent = brand.name;
                        brandSelect.appendChild(option);
                    });
                }

                // Initial population of attributes for the first option
                const firstAttributeSelect = document.getElementById('attribute-select-1-1');
                populateAttributes(firstAttributeSelect, data.attributes, 1);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });

        // VARIANT MANAGEMENT
        let variantCounter = 1;
        const variantsContainer = document.querySelector('.col-12.col-xl-8');

        // Add New Variant Button
        const addVariantButton = document.getElementById('add-variant-button');
        addVariantButton.addEventListener('click', function() {
            variantCounter++;
            addNewVariant(variantCounter);
        });

        // Function to add a new variant
        function addNewVariant(variantIndex) {
            const firstVariant = document.getElementById('variant-1');
            const newVariant = firstVariant.cloneNode(true);
            newVariant.id = `variant-${variantIndex}`;

            // Update title and add remove button
            const titleCol = newVariant.querySelector('.card-body .row .col-12.mb-4');
            titleCol.innerHTML = `
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-body-highlight">Variant ${variantIndex}</h4>
                    <button type="button" class="btn btn-falcon-default btn-sm remove-variant" data-variant-id="${variantIndex}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;

            // Update photo uploader IDs and names
            const photoPreviewContainer = newVariant.querySelector('.photo-preview-container');
            photoPreviewContainer.id = `preview-container-variant-${variantIndex}`;

            const filePreviewContainer = newVariant.querySelector('.custom-dropzone');
            filePreviewContainer.id = `file-preview-container-variant-${variantIndex}`;

            const alertContainer = newVariant.querySelector('[id^="alert-container"]');
            alertContainer.id = `alert-container-variant-${variantIndex}`;

            const fileBrowseButton = newVariant.querySelector('[id^="file-browse-button"]');
            fileBrowseButton.id = `file-browse-button-variant-${variantIndex}`;

            const fileInput = newVariant.querySelector('[id^="file-input"]');
            fileInput.id = `file-input-variant-${variantIndex}`;
            fileInput.name = `variants[${variantIndex}][photos][]`;
            // Clear file input value
            fileInput.value = '';

            // Clear preview container
            photoPreviewContainer.innerHTML = '';

            // Update all other input names and IDs
            newVariant.querySelectorAll('input:not([id^="file-input"]), select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/variants\[1\]/, `variants[${variantIndex}]`);
                }
                if (input.id && !input.id.includes('file-input')) {
                    input.id = input.id.replace(/-1(-|\b)/, `-${variantIndex}$1`);
                }
                if (input.tagName === 'INPUT') {
                    input.value = '';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                }
            });

            // Update attribute options container
            const attributeContainer = newVariant.querySelector('[id^="attribute-options-container"]');
            attributeContainer.id = `attribute-options-container-${variantIndex}`;

            // Update the Add Option button
            const addOptionHeader = attributeContainer.querySelector('.d-flex.justify-content-between');
            addOptionHeader.innerHTML = `
                <h5 class="mb-0 text-body-highlight">Options</h5>
                <button type="button" id="add-option-button-${variantIndex}" class="btn btn-falcon-default btn-sm">
                    <i class="fas fa-plus"></i> Add Option
                </button>
            `;

            // Clear existing options except the first one
            const attributeOptions = attributeContainer.querySelectorAll('.attribute-option');
            for (let i = 1; i < attributeOptions.length; i++) {
                attributeOptions[i].remove();
            }

            // Update the first option's IDs and names
            const firstOption = attributeContainer.querySelector('.attribute-option');
            firstOption.innerHTML = `
                <div class="col-11 col-sm-5">
                    <select id="attribute-select-${variantIndex}-1" class="form-select attribute-select" data-option-index="${variantIndex}-1" name="variants[${variantIndex}][attributes][1][attribute_id]">
                        <option value="">Select Attribute</option>
                    </select>
                </div>
                <div class="col-11 col-sm-5">
                    <select id="value-select-${variantIndex}-1" class="form-select value-select" data-option-index="${variantIndex}-1" name="variants[${variantIndex}][attributes][1][value_id]">
                        <option value="">Select Value</option>
                    </select>
                </div>
                <div class="col-1 col-sm-2">
                    <button type="button" class="btn btn-falcon-default btn-sm remove-option d-none">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;

            // Add the new variant before the last card
            const addVariantButtonContainer = document.getElementById('add-variant-button').closest('.d-flex');
            variantsContainer.insertBefore(newVariant, addVariantButtonContainer.nextSibling);

            // Initialize photo uploader for the new variant
            initPhotoUploader(`variant-${variantIndex}`, 4);

            // Add event listeners
            const attrSelect = newVariant.querySelector('.attribute-select');
            const addOptionBtn = newVariant.querySelector(`#add-option-button-${variantIndex}`);
            const removeVariantBtn = newVariant.querySelector('.remove-variant');

            // Add Option button event listener
            addOptionBtn.addEventListener('click', function() {
                addAttributeOption(variantIndex);
            });

            // Remove Variant button event listener
            removeVariantBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to remove this variant?')) {
                    newVariant.remove();
                }
            });

            // Populate attribute dropdown using stored data
            if (window.appData && window.appData.attributes) {
                populateAttributes(attrSelect, window.appData.attributes, variantIndex);
            }
        }

        // Function to initialize photo uploader
        function initPhotoUploader(id, maxPhotos) {
            const fileInput = document.getElementById(`file-input-${id}`);
            const previewContainer = document.getElementById(`preview-container-${id}`);
            const filePreviewContainer = document.getElementById(`file-preview-container-${id}`);
            const alertContainer = document.getElementById(`alert-container-${id}`);
            const fileBrowseButton = document.getElementById(`file-browse-button-${id}`);

            if (!fileInput || !previewContainer || !filePreviewContainer) {
                return;
            }

            // Make sure file browse button clicks the input
            if (fileBrowseButton) {
                fileBrowseButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.click();
                });
            }

            // Open file dialog when clicking the custom dropzone
            filePreviewContainer.addEventListener('click', () => fileInput.click());

            // Prevent default behavior for drag-and-drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                filePreviewContainer.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
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

                if (maxPhotos && totalFiles > maxPhotos) {
                    if (alertContainer) {
                        alertContainer.classList.remove('d-none'); // Show the alert
                    }
                    return;
                } else if (alertContainer) {
                    alertContainer.classList.add('d-none'); // Hide the alert
                }

                handleFiles(files, fileInput, previewContainer);
            });

            // Handle file input change
            fileInput.addEventListener('change', function() {
                const currentFiles = Array.from(fileInput.files);
                const totalFiles = previewContainer.children.length + currentFiles.length;

                if (maxPhotos && totalFiles > maxPhotos) {
                    if (alertContainer) {
                        alertContainer.classList.remove('d-none'); // Show the alert
                    }
                    fileInput.value = ''; // Clear the input to prevent excess files
                    return;
                } else if (alertContainer) {
                    alertContainer.classList.add('d-none'); // Hide the alert
                }

                handleFiles(currentFiles, fileInput, previewContainer);
            });
        }

        // Process uploaded files
        function handleFiles(files, fileInput, previewContainer) {
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'position-relative me-2';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-btn';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        previewDiv.remove();
                        removeFile(file, fileInput);
                    });

                    previewDiv.appendChild(img);
                    previewDiv.appendChild(removeBtn);
                    previewContainer.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        }

        // Remove file from input
        function removeFile(fileToRemove, fileInput) {
            if (!fileInput.files || fileInput.files.length === 0) return;

            try {
                const dataTransfer = new DataTransfer();
                Array.from(fileInput.files).forEach(file => {
                    if (file !== fileToRemove) {
                        dataTransfer.items.add(file);
                    }
                });
                fileInput.files = dataTransfer.files;
            } catch (error) {
                console.error('Error removing file:', error);
                // Fallback for browsers not supporting DataTransfer
                fileInput.value = '';
            }
        }

        // Function to add a new attribute option to a variant
        function addAttributeOption(variantIndex) {
            const attributeContainer = document.getElementById(`attribute-options-container-${variantIndex}`);
            const optionCount = attributeContainer.querySelectorAll('.attribute-option').length + 1;

            const newOption = document.createElement('div');
            newOption.className = 'row gx-3 align-items-center mb-3 attribute-option';
            newOption.innerHTML = `
                <div class="col-11 col-sm-5">
                    <select id="attribute-select-${variantIndex}-${optionCount}" class="form-select attribute-select" data-option-index="${variantIndex}-${optionCount}" name="variants[${variantIndex}][attributes][${optionCount}][attribute_id]">
                        <option value="">Select Attribute</option>
                    </select>
                </div>
                <div class="col-11 col-sm-5">
                    <select id="value-select-${variantIndex}-${optionCount}" class="form-select value-select" data-option-index="${variantIndex}-${optionCount}" name="variants[${variantIndex}][attributes][${optionCount}][value_id]">
                        <option value="">Select Value</option>
                    </select>
                </div>
                <div class="col-1 col-sm-2">
                    <button type="button" class="btn btn-falcon-default btn-sm remove-option">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            `;

            attributeContainer.appendChild(newOption);

            // Make the remove button visible on the first option if we have more than one option
            // if (optionCount > 1) {
            //     const firstOptionRemoveBtn = attributeContainer.querySelector('.attribute-option:first-child .remove-option');
            //     if (firstOptionRemoveBtn.classList.contains('d-none')) {
            //         firstOptionRemoveBtn.classList.remove('d-none');
            //     }
            // }

            // Populate the attribute dropdown using stored data
            const attrSelect = newOption.querySelector('.attribute-select');
            if (window.appData && window.appData.attributes) {
                populateAttributes(attrSelect, window.appData.attributes, variantIndex);
            }

            // Add event listener for option removal
            newOption.querySelector('.remove-option').addEventListener('click', function() {
                const removedSelect = newOption.querySelector('.attribute-select');
                const removedAttributeId = removedSelect.value;
                newOption.remove();

                // If only one option remains, hide its remove button
                const remainingOptions = attributeContainer.querySelectorAll('.attribute-option');
                if (remainingOptions.length === 1) {
                    remainingOptions[0].querySelector('.remove-option').classList.add('d-none');
                }

                // Renumber the remaining options for proper form submission
                remainingOptions.forEach((option, index) => {
                    const optNum = index + 1;
                    const attrSel = option.querySelector('.attribute-select');
                    const valSel = option.querySelector('.value-select');

                    attrSel.id = `attribute-select-${variantIndex}-${optNum}`;
                    attrSel.dataset.optionIndex = `${variantIndex}-${optNum}`;
                    attrSel.name = `variants[${variantIndex}][attributes][${optNum}][attribute_id]`;

                    valSel.id = `value-select-${variantIndex}-${optNum}`;
                    valSel.dataset.optionIndex = `${variantIndex}-${optNum}`;
                    valSel.name = `variants[${variantIndex}][attributes][${optNum}][value_id]`;
                });

                // Refresh attribute select options to show previously selected attribute
                if (removedAttributeId) {
                    refreshAttributeOptions(variantIndex);
                }
            });
        }

        // Function to populate attributes in a select, excluding already selected attributes in the same variant
        function populateAttributes(selectElement, attributes, variantIndex) {
            if (!attributes) return;

            // Get the currently selected value of this select (to preserve it on refresh)
            const currentValue = selectElement.value;

            // Clear the current options
            selectElement.innerHTML = '<option value="">Select Attribute</option>';

            // Get all currently selected attributes in this variant (except the current select)
            const selectedAttributes = [];
            if (variantIndex) {
                const container = document.getElementById(`attribute-options-container-${variantIndex}`);
                if (container) {
                    const attributeSelects = container.querySelectorAll('.attribute-select');
                    attributeSelects.forEach(select => {
                        // Skip the current select element when collecting selected values
                        if (select !== selectElement && select.value) {
                            selectedAttributes.push(select.value);
                        }
                    });
                }
            }

            // Add only attributes that haven't been selected in this variant
            attributes.forEach(attribute => {
                // Skip if this attribute is already selected in another option of the same variant
                if (!selectedAttributes.includes(attribute.attribute_id.toString())) {
                    const option = document.createElement('option');
                    option.value = attribute.attribute_id;
                    option.textContent = attribute.display_name;
                    selectElement.appendChild(option);
                }
            });

            // Restore the previously selected value if it still exists in the options
            if (currentValue) {
                const optionExists = Array.from(selectElement.options).some(option => option.value === currentValue);
                if (optionExists) {
                    selectElement.value = currentValue;
                }
            }
        }

        // Function to refresh all attribute dropdowns within a variant
        function refreshAttributeOptions(variantIndex) {
            const container = document.getElementById(`attribute-options-container-${variantIndex}`);
            if (container && window.appData && window.appData.attributes) {
                const attributeSelects = container.querySelectorAll('.attribute-select');
                attributeSelects.forEach(select => {
                    populateAttributes(select, window.appData.attributes, variantIndex);
                });
            }
        }

        // Function to populate attribute values
        function populateAttributeValues(attributeId, valueSelect) {
            if (window.appData && window.appData.attributes) {
                const attributes = window.appData.attributes;
                const selectedAttribute = attributes.find(attr => attr.attribute_id == attributeId);

                valueSelect.innerHTML = '<option value="">Select Value</option>';

                if (selectedAttribute && selectedAttribute.values) {
                    selectedAttribute.values.forEach(value => {
                        const option = document.createElement('option');
                        option.value = value.attribute_value_id;
                        option.textContent = value.value;
                        valueSelect.appendChild(option);
                    });
                }
            }
        }

        // Event delegation for attribute selection changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('attribute-select')) {
                const optionIndex = e.target.dataset.optionIndex;
                const [variantIndex, optionNumber] = optionIndex.split('-');
                const valueSelect = document.querySelector(`.value-select[data-option-index="${optionIndex}"]`);

                // Store old value before it's changed
                const oldValue = e.target.getAttribute('data-previous-value');

                // Update stored previous value
                e.target.setAttribute('data-previous-value', e.target.value);

                if (valueSelect) {
                    const attributeId = e.target.value;
                    if (attributeId) {
                        populateAttributeValues(attributeId, valueSelect);
                    } else {
                        valueSelect.innerHTML = '<option value="">Select Value</option>';
                    }
                }

                // Refresh attribute options in all selects of this variant
                refreshAttributeOptions(variantIndex);
            }
        });

        // Event delegation for remove option buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-option') ||
                e.target.closest('.remove-option')) {
                const removeBtn = e.target.classList.contains('remove-option') ?
                    e.target : e.target.closest('.remove-option');
                const optionElement = removeBtn.closest('.attribute-option');
                const container = optionElement.closest('[id^="attribute-options-container"]');
                const variantId = container.id.split('-').pop();

                // Get attribute ID before removing
                const attributeSelect = optionElement.querySelector('.attribute-select');
                const removedAttributeId = attributeSelect ? attributeSelect.value : null;

                optionElement.remove();

                // If only one option remains, hide its remove button
                const remainingOptions = container.querySelectorAll('.attribute-option');
                if (remainingOptions.length === 1) {
                    remainingOptions[0].querySelector('.remove-option').classList.add('d-none');
                }

                // Renumber remaining options
                remainingOptions.forEach((option, index) => {
                    const optNum = index + 1;
                    const attrSel = option.querySelector('.attribute-select');
                    const valSel = option.querySelector('.value-select');

                    attrSel.id = `attribute-select-${variantId}-${optNum}`;
                    attrSel.dataset.optionIndex = `${variantId}-${optNum}`;
                    attrSel.name = `variants[${variantId}][attributes][${optNum}][attribute_id]`;

                    valSel.id = `value-select-${variantId}-${optNum}`;
                    valSel.dataset.optionIndex = `${variantId}-${optNum}`;
                    valSel.name = `variants[${variantId}][attributes][${optNum}][value_id]`;
                });

                // Refresh attribute options to include the removed attribute
                if (removedAttributeId) {
                    refreshAttributeOptions(variantId);
                }
            }
        });

        // Add event listener for the first variant's Add Option button
        document.getElementById('add-option-button-1').addEventListener('click', function() {
            addAttributeOption(1);
        });

        // Form submission

    });
</script>
@endsection
{{-- PAGE JS END --}}
