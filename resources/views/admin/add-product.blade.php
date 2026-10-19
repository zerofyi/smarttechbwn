@extends('layouts.admin')

@section('title', 'Admin : Add Product')

@section('vendor-style')
@endsection

@section('page-style')
    <link href="../../../vendors/choices/choices.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
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
    <script src="https://cdn.tiny.cloud/1/pgcvgcc9s50spwp07jmzz6c5my1q386ir6uuhfufs6zdltp0/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#myeditorinstance',
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table'
        });
    </script>
    <style>
        .photo-upload-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .photo-upload-item {
            position: relative;
            width: 100px;
            height: 100px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            background-color: #f9f9f9;
        }

        .photo-upload-item:hover {
            border-color: #007bff;
        }

        .photo-upload-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .photo-upload-item .remove-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .photo-upload-item .upload-icon {
            font-size: 24px;
            color: #999;
        }

        .photo-upload-item input {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        @if ($errors->any())
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
                <div class="toast show text-white bg-danger bg-opacity-75 border-0" role="alert" aria-live="assertive"
                    aria-atomic="true">
                    <div class="toast-header text-white">
                        <strong class="me-auto">Error</strong>
                        <small class="text-light">Just now</small>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
                <div class="toast show text-white bg-success bg-opacity-75 border-0" role="alert" aria-live="assertive"
                    aria-atomic="true">
                    <div class="toast-header text-white">
                        <strong class="me-auto">Success</strong>
                        <small class="text-light">Just now</small>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif
        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products') }}">Products</a></li>
                <li class="breadcrumb-item active">Add New Product</li>
            </ol>
        </nav>

        <form class="mb-9" id="product-form" method="POST" action="{{ route('admin.products.store') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="row g-3 flex-between-end mb-5">
                <div class="col-auto">
                    <h2 class="mb-2">Add a product</h2>
                    <h5 class="text-body-tertiary fw-semibold">Add new product to your store</h5>
                </div>
                <div class="col-auto">
                    <button class="btn btn-phoenix-secondary me-2 mb-2 mb-sm-0" type="button">Discard</button>
                    <button class="btn btn-primary mb-2 mb-sm-0" type="submit">Save Product</button>
                </div>
            </div>

            <div class="row g-5">
                <div class="col-12 col-xl-8">
                    <h4 class="mb-3">Product Title<span class="text-danger"> *</span></h4>
                    <input class="form-control mb-5" type="text" name="title" required
                        placeholder="Write title here..." />

                    <div class="mb-6">
                        <h4 class="mb-3">Product Description</h4>
                        <textarea class="tinymce" name="description"
                            data-tinymce='{"height":"20rem","placeholder":"Write a description here..."}'></textarea>
                    </div>

                    <div class="mb-6">
                        <h4 class="mb-3">Product Images</h4>
                        <div class="photo-upload-container">
                            <!-- Image 1 -->
                            <div class="photo-upload-item" id="photo-upload-1">
                                <input type="file" name="image_one" id="image-input-1" accept="image/*">
                                <div class="upload-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <img id="image-preview-1" style="display: none;">
                                <button type="button" class="remove-btn" style="display: none;">×</button>
                            </div>

                            <!-- Image 2 -->
                            <div class="photo-upload-item" id="photo-upload-2">
                                <input type="file" name="image_two" id="image-input-2" accept="image/*">
                                <div class="upload-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <img id="image-preview-2" style="display: none;">
                                <button type="button" class="remove-btn" style="display: none;">×</button>
                            </div>

                            <!-- Image 3 -->
                            <div class="photo-upload-item" id="photo-upload-3">
                                <input type="file" name="image_three" id="image-input-3" accept="image/*">
                                <div class="upload-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <img id="image-preview-3" style="display: none;">
                                <button type="button" class="remove-btn" style="display: none;">×</button>
                            </div>

                            <!-- Image 4 -->
                            <div class="photo-upload-item" id="photo-upload-4">
                                <input type="file" name="image_four" id="image-input-4" accept="image/*">
                                <div class="upload-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <img id="image-preview-4" style="display: none;">
                                <button type="button" class="remove-btn" style="display: none;">×</button>
                            </div>

                            <!-- Image 5 -->
                            <div class="photo-upload-item" id="photo-upload-5">
                                <input type="file" name="image_five" id="image-input-5" accept="image/*">
                                <div class="upload-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <img id="image-preview-5" style="display: none;">
                                <button type="button" class="remove-btn" style="display: none;">×</button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="mb-3">Video URL</h4>
                        <input class="form-control mb-5" type="url" name="vid_url"
                            placeholder="https://example.com/video.mp4" />
                    </div>

                    <div class="mb-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Product Attributes</h4>
                            <button type="button" id="add-attribute-btn" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Add Attribute
                            </button>
                        </div>

                        <div id="attribute-container">
                            <!-- RAM and Storage will be added here automatically if they exist -->
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="row g-2">
                        <div class="col-12 col-xl-12">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Product Information</h4>
                                    <div class="row gx-3">
                                        <div class="col-12 mb-1">
                                            <div class="d-flex flex-wrap mb-2">
                                                <h5 class="mb-0 text-body-highlight me-2">SKU<span class="text-danger"> *</span></h5>
                                            </div>
                                            <input type="text" class="form-control mb-3" placeholder="SKU"
                                                name="sku" required />
                                        </div>
                                        <div class="col-12 mb-1">
                                            <div class="d-flex flex-wrap mb-2">
                                                <h5 class="mb-0 text-body-highlight me-2">Color</h5>
                                            </div>
                                            <input type="text" class="form-control mb-3" placeholder="Color"
                                                name="color"/>
                                        </div>

                                        {{-- <div class="col-12 mb-1">
                                        <div class="d-flex flex-wrap mb-2">
                                            <h5 class="mb-0 text-body-highlight me-2">Barcode</h5>
                                        </div>
                                        <input type="text" class="form-control mb-3" placeholder="Barcode" name="barcode" />
                                    </div> --}}

                                        <div class="col-12 mb-1">
                                            <div class="d-flex flex-wrap mb-2">
                                                <h5 class="mb-0 text-body-highlight me-2">Retail Price<span class="text-danger"> *</span></h5>
                                            </div>
                                            <input type="number" step="0.01" class="form-control mb-3"
                                                placeholder="Price" name="retail_price" required />
                                        </div>

                                        <div class="col-12 mb-1">
                                            <div class="d-flex flex-wrap mb-2">
                                                <h5 class="mb-0 text-body-highlight me-2">MRP<span class="text-danger"> *</span></h5>
                                            </div>
                                            <input type="number" step="0.01" class="form-control mb-3"
                                                placeholder="MRP" name="mrp" />
                                        </div>
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
                                                    <h5 class="mb-0 text-body-highlight me-2">Category<span class="text-danger"> *</span></h5>
                                                    <a class="fw-bold fs-9" target="_blank"
                                                        href="{{ route('admin.categories') }}">Add new category</a>
                                                </div>
                                                <select class="form-select mb-3" name="category_id" aria-label="category"
                                                    required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->category_id }}">{{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-xl-12">
                                            <div class="mb-4">
                                                <div class="d-flex flex-wrap mb-2">
                                                    <h5 class="mb-0 text-body-highlight me-2">Brand<span class="text-danger"> *</span></h5>
                                                    <a class="fw-bold fs-9" target="_blank"
                                                        href="{{ route('admin.brands') }}">Add new brand</a>
                                                </div>
                                                <select class="form-select mb-3" name="brand_id" aria-label="brand"
                                                    required>
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->brand_id }}">{{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6 col-xl-12">
                                            <div class="mb-4">
                                                <div class="d-flex flex-wrap mb-2">
                                                    <h5 class="mb-0 text-body-highlight me-2">Status<span class="text-danger"> *</span></h5>
                                                </div>
                                                <select class="form-select" name="is_active" aria-label="status"
                                                    required>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-12 col-xl-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="card-title mb-4">Attributes</h4>
                                <div id="attribute-container">
                                    <!-- Attributes will be added here dynamically -->
                                </div>
                                <button type="button" id="add-attribute-btn" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Add Attribute
                                </button>
                            </div>
                        </div>
                    </div> --}}
                    </div>
                </div>
            </div>
        </form>

        <x-admin.footer />
    </div>
@endsection

@section('vendor-script')
@endsection

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
    <script src="../../../vendors/choices/choices.min.js"></script>
    <script src="../../../assets/js/phoenix.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize image uploaders
            initImageUploaders();

            // Fetch attributes and values from server
            let attributesData = [];
            let ramAttribute = null;
            let storageAttribute = null;

            fetch("{{ route('admin.get-data') }}")
                .then(response => response.json())
                .then(data => {
                    attributesData = data.attributes || [];
                    window.attributesData = attributesData; // Make available globally

                    // Find RAM and Storage attributes
                    ramAttribute = attributesData.find(attr =>
                        attr.display_name.toLowerCase().includes('ram'));
                    storageAttribute = attributesData.find(attr =>
                        attr.display_name.toLowerCase().includes('storage'));

                    // Add RAM and Storage attributes if they exist
                    if (ramAttribute) {
                        addPredefinedAttribute(ramAttribute, 'Ram', 0);
                    }
                    if (storageAttribute) {
                        addPredefinedAttribute(storageAttribute, 'Storage', 1);
                    }
                })
                .catch(error => {
                    console.error('Error fetching attributes:', error);
                });

            // Add attribute button click handler
            let attributeCounter = 2;
            const attributeContainer = document.getElementById('attribute-container');
            const addAttributeBtn = document.getElementById('add-attribute-btn');

            addAttributeBtn.addEventListener('click', function() {
                attributeCounter++;
                addAttributeRow(attributeCounter);
            });

            // Function to add predefined attributes (RAM/Storage)
            function addPredefinedAttribute(attribute, displayName, daIndex) {
                const index = daIndex;
                const attributeRow = document.createElement('div');
                attributeRow.className = 'row gx-3 align-items-center mb-3 attribute-option';
                attributeRow.dataset.index = index;

                attributeRow.innerHTML = `
            <div class="col-11 col-sm-5">
                <select class="form-select attribute-select" name="attributes[${index}][attribute_id]" required>
                    <option value="${attribute.attribute_id}" selected>${displayName}</option>
                </select>
            </div>
            <div class="col-11 col-sm-5">
                <select class="form-select value-select" name="attributes[${index}][attribute_value_id]" required>
                    <option value="">Select Value</option>
                    ${attribute.values.map(value =>
                        `<option value="${value.attribute_value_id}">${value.value}</option>`
                    ).join('')}
                </select>
            </div>
            <div class="col-1 col-sm-2">
                <button type="button" class="btn btn-falcon-default btn-sm remove-option">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

                attributeContainer.appendChild(attributeRow);

                // Add event listener for remove button
                const removeBtn = attributeRow.querySelector('.remove-option');
                removeBtn.addEventListener('click', function() {
                    attributeRow.remove();
                    refreshAttributeSelects();
                });
            }

            // Function to add a new attribute row
            function addAttributeRow(index) {
                const attributeRow = document.createElement('div');
                attributeRow.className = 'row gx-3 align-items-center mb-3 attribute-option';
                attributeRow.dataset.index = index;

                attributeRow.innerHTML = `
            <div class="col-11 col-sm-5">
                <select class="form-select attribute-select" name="attributes[${index}][attribute_id]" required>
                    <option value="">Select Attribute</option>
                    ${getAvailableAttributesOptions()}
                </select>
            </div>
            <div class="col-11 col-sm-5">
                <select class="form-select value-select" name="attributes[${index}][attribute_value_id]" required>
                    <option value="">Select Value</option>
                </select>
            </div>
            <div class="col-1 col-sm-2">
                <button type="button" class="btn btn-falcon-default btn-sm remove-option">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        `;

                attributeContainer.appendChild(attributeRow);

                // Add event listener for attribute selection change
                const attributeSelect = attributeRow.querySelector('.attribute-select');
                attributeSelect.addEventListener('change', function() {
                    const selectedAttributeId = this.value;
                    const valueSelect = attributeRow.querySelector('.value-select');

                    // Clear previous options
                    valueSelect.innerHTML = '<option value="">Select Value</option>';

                    if (selectedAttributeId) {
                        // Find the selected attribute
                        const selectedAttribute = attributesData.find(attr => attr.attribute_id ==
                            selectedAttributeId);

                        if (selectedAttribute && selectedAttribute.values) {
                            // Add values to the value select
                            selectedAttribute.values.forEach(value => {
                                const option = document.createElement('option');
                                option.value = value.attribute_value_id;
                                option.textContent = value.value;
                                valueSelect.appendChild(option);
                            });
                        }
                    }

                    // Refresh available attributes in all selects
                    refreshAttributeSelects();
                });

                // Add event listener for remove button
                const removeBtn = attributeRow.querySelector('.remove-option');
                removeBtn.addEventListener('click', function() {
                    attributeRow.remove();
                    refreshAttributeSelects();
                });
            }

            // Function to get available attributes (excluding already selected ones)
            function getAvailableAttributesOptions() {
                const selectedAttributes = Array.from(document.querySelectorAll('.attribute-select'))
                    .map(select => select.value)
                    .filter(value => value);

                return attributesData
                    .filter(attr => !selectedAttributes.includes(attr.attribute_id.toString()))
                    .map(attr => `<option value="${attr.attribute_id}">${attr.display_name}</option>`)
                    .join('');
            }

            // Function to refresh all attribute selects
            function refreshAttributeSelects() {
                const attributeSelects = document.querySelectorAll('.attribute-select');

                attributeSelects.forEach(select => {
                    const currentValue = select.value;
                    const optionsHtml = getAvailableAttributesOptions();

                    // Add the current selected option back if it exists
                    if (currentValue) {
                        const currentAttribute = attributesData.find(attr => attr.attribute_id ==
                            currentValue);
                        if (currentAttribute) {
                            optionsHtml +=
                                `<option value="${currentAttribute.attribute_id}" selected>${currentAttribute.display_name}</option>`;
                        }
                    }

                    select.innerHTML = `<option value="">Select Attribute</option>${optionsHtml}`;
                });
            }

            // Function to initialize image uploaders
            function initImageUploaders() {
                for (let i = 1; i <= 5; i++) {
                    const uploadItem = document.getElementById(`photo-upload-${i}`);
                    const fileInput = document.getElementById(`image-input-${i}`);
                    const preview = document.getElementById(`image-preview-${i}`);
                    const removeBtn = uploadItem.querySelector('.remove-btn');
                    const uploadIcon = uploadItem.querySelector('.upload-icon');

                    // Click handler for the upload area
                    uploadItem.addEventListener('click', function(e) {
                        if (e.target !== removeBtn) {
                            fileInput.click();
                        }
                    });

                    // File input change handler
                    fileInput.addEventListener('change', function(e) {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                uploadIcon.style.display = 'none';
                                removeBtn.style.display = 'block';
                            };

                            reader.readAsDataURL(this.files[0]);
                        }
                    });

                    // Remove button handler
                    removeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        fileInput.value = '';
                        preview.src = '';
                        preview.style.display = 'none';
                        uploadIcon.style.display = 'block';
                        removeBtn.style.display = 'none';
                    });
                }
            }
        });
    </script>
@endsection
