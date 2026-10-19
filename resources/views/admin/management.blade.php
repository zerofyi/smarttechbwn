@extends('layouts.admin')

{{-- PAGE TITLE HERE --}}
@section('title', 'App Management')

{{-- VENDOR CSS START --}}
@section('vendor-style')
    <link href="../../vendors/choices/choices.min.css" rel="stylesheet" />
@endsection
{{-- VENDOR CSS END --}}

{{-- PAGE CSS START --}}
@section('page-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link href="../../vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="../../assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="../../assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
    <link href="../../assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="../../assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
    <style>
        .banner-preview,
        .product-preview {
            max-height: 100px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
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
@endsection
{{-- PAGE CSS END --}}

{{-- CONTENT START --}}
@section('content')
    <div class="content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <nav class="mb-3" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">App Management</li>
            </ol>
        </nav>

        <h2 class="text-bold text-body-emphasis mb-5">App Content Management</h2>

        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col col-auto">
                <div class="search-box">
                    <form class="position-relative">
                        <input class="form-control search-input search" type="search" placeholder="Search content"
                            aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </form>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                        <span class="fas fa-plus me-2"></span>Add Banner
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductListModal">
                        <span class="fas fa-plus me-2"></span>Add Product List
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Table -->
        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
            <div class="table-responsive scrollbar ms-n1 ps-1">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap fs-9 align-middle ps-0">
                                <div class="form-check mb-0 fs-8">
                                    <input class="form-check-input" type="checkbox" />
                                </div>
                            </th>
                            <th class="sort align-middle" scope="col">TYPE</th>
                            <th class="sort align-middle" scope="col">SUBTYPE</th>
                            <th class="sort align-middle" scope="col">TITLE</th>
                            <th class="sort align-middle" scope="col">CONTENT PREVIEW</th>
                            <th class="sort align-middle" scope="col">ORDER</th>
                            <th class="sort align-middle" scope="col">STATUS</th>
                            <th class="sort align-middle text-end pe-0" scope="col">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="list" id="management-table-body">
                        @foreach ($contents as $content)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                <td class="fs-9 align-middle ps-0 py-3">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox" />
                                    </div>
                                </td>
                                <td class="align-middle white-space-nowrap text-capitalize">
                                    {{ $content->type }}
                                </td>
                                <td class="align-middle white-space-nowrap text-capitalize">
                                    {{ str_replace('_', ' ', $content->sub_type) }}
                                </td>
                                <td class="align-middle white-space-nowrap">
                                    <h6 class="mb-0 fw-semibold">{{ $content->title }}</h6>
                                    @if ($content->target_url)
                                        <p class="mb-0 text-body-secondary">
                                            <small>Link: {{ $content->target_url }}</small>
                                        </p>
                                    @endif
                                </td>
                                <td class="align-middle white-space-nowrap">
                                    @if ($content->type === 'product')
                                        @php
                                            $variants = \App\Models\ProductVariant::whereIn(
                                                'variant_id',
                                                $content->data,
                                            )->get();
                                        @endphp
                                        @foreach ($variants as $variant)
                                            <span class="badge bg-primary me-1">{{ $variant->product->name }}</span>
                                        @endforeach
                                    @else
                                        @foreach ($content->data as $image)
                                            <img src="{{ asset('storage/Banner/' . basename($image)) }}"
                                                class="banner-preview" alt="Banner">
                                        @endforeach
                                    @endif
                                </td>
                                <td class="align-middle white-space-nowrap">
                                    {{ $content->display_order }}
                                </td>
                                <td class="align-middle white-space-nowrap">
                                    <span class="badge bg-{{ $content->is_active ? 'success' : 'danger' }}">
                                        {{ $content->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="align-middle white-space-nowrap text-body-tertiary text-end">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-subtle-primary me-1 mb-1" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit{{ ucfirst($content->type) }}Modal-{{ $content->id }}"
                                            title="Edit">
                                            <span data-feather="edit-3"></span>
                                        </button>
                                        <button class="btn btn-subtle-danger mb-1" type="button" data-bs-toggle="modal"
                                            data-bs-target="#deleteContentModal-{{ $content->id }}" title="Delete">
                                            <span data-feather="trash-2"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Delete Modal (Generic for both types) -->
                            <div class="modal fade" id="deleteContentModal-{{ $content->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Deletion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.app-management.destroy', $content->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this {{ $content->type }} content?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="editProductModal-{{ $content->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Product List</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.product-lists.update', $content->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">List Type</label>
                                                            <select class="form-select" name="sub_type" required>
                                                                @foreach(\App\Models\AppManagement::getProductSubTypes() as $value => $label)
                                                                    <option value="{{ $value }}" {{ $content->sub_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" class="form-control" name="title" value="{{ $content->title }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Target URL (Optional)</label>
                                                            <input type="url" class="form-control" name="target_url" value="{{ $content->target_url }}" placeholder="https://example.com">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Display Order</label>
                                                            <input type="number" class="form-control" name="display_order" value="{{ $content->display_order }}" min="0">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="is_active" required>
                                                                <option value="1" {{ $content->is_active ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ !$content->is_active ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Select Products</label>
                                                            <select class="form-control choices-products" name="product_variants[]" multiple required>
                                                                @foreach($productVariants as $variant)
                                                                    <option value="{{ $variant->variant_id }}" {{ in_array($variant->variant_id, $content->data) ? 'selected' : '' }}>
                                                                        {{ $variant->product->name }} - ₹{{ $variant->price }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Update Product List</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="editBannerModal-{{ $content->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Banner</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.banners.update', $content->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Banner Type</label>
                                                            <select class="form-select" name="sub_type" required>
                                                                @foreach(\App\Models\AppManagement::getBannerSubTypes() as $value => $label)
                                                                    <option value="{{ $value }}" {{ $content->sub_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" class="form-control" name="title" value="{{ $content->title }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Target URL (Optional)</label>
                                                            <input type="url" class="form-control" name="target_url" value="{{ $content->target_url }}" placeholder="https://example.com">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Display Order</label>
                                                            <input type="number" class="form-control" name="display_order" value="{{ $content->display_order }}" min="0">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select class="form-select" name="is_active" required>
                                                                <option value="1" {{ $content->is_active ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ !$content->is_active ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Current Banners</label>
                                                            <div class="d-flex flex-wrap mb-2">
                                                                @foreach($content->data as $image)
                                                                    <div class="position-relative me-2 mb-2">
                                                                        <img src="{{ asset('storage/Banner/' . basename($image)) }}" class="banner-preview" alt="Banner">
                                                                        <input type="hidden" name="existing_banners[]" value="{{ basename($image) }}">
                                                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 remove-banner" style="width:20px;height:20px;line-height:20px;">
                                                                            ×
                                                                        </button>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Add More Banners</label>
                                                            <input type="file" class="form-control" name="banners[]" multiple accept="image/*">
                                                            <small class="text-muted">Select additional banners to upload</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Update Banner</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body">
                        Showing {{ $contents->firstItem() }} to {{ $contents->lastItem() }} of
                        {{ $contents->total() }} entries
                    </p>
                </div>
                <div class="col-auto d-flex">
                    {{ $contents->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>

        <!-- Add Banner Modal -->
        <div class="modal fade" id="addBannerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Banner Type</label>
                                        <select class="form-select" name="sub_type" required>
                                            @foreach (\App\Models\AppManagement::getBannerSubTypes() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Target URL (Optional)</label>
                                        <input type="url" class="form-control" name="target_url"
                                            placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" class="form-control" name="display_order" min="0"
                                            value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="is_active" required>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Banner Images</label>
                                        <input type="file" class="form-control" name="banners[]" multiple
                                            accept="image/*" required>
                                        <small class="text-muted">Upload one or multiple banner images</small>
                                    </div>
                                    <div class="mb-3" id="bannerPreviews"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Banner</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Product List Modal -->
        <div class="modal fade" id="addProductListModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Product List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.product-lists.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">List Type</label>
                                        <select class="form-select" name="sub_type" required>
                                            @foreach (\App\Models\AppManagement::getProductSubTypes() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Target URL (Optional)</label>
                                        <input type="url" class="form-control" name="target_url"
                                            placeholder="https://example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" class="form-control" name="display_order" min="0"
                                            value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="is_active" required>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Select Products</label>
                                        <select class="form-control choices-products" name="product_variants[]" multiple
                                            required>
                                            @foreach ($productVariants as $variant)
                                                <option value="{{ $variant->variant_id }}">{{ $variant->product->name }}
                                                    - ₹{{ $variant->price }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Product List</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
    <script src="../../vendors/popper/popper.min.js"></script>
    <script src="../../vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../../vendors/anchorjs/anchor.min.js"></script>
    <script src="../../vendors/is/is.min.js"></script>
    <script src="../../vendors/fontawesome/all.min.js"></script>
    <script src="../../vendors/lodash/lodash.min.js"></script>
    <script src="../../vendors/list.js/list.min.js"></script>
    <script src="../../vendors/feather-icons/feather.min.js"></script>
    <script src="../../vendors/dayjs/dayjs.min.js"></script>
    <script src="../../vendors/choices/choices.min.js"></script>
    <script src="../../assets/js/phoenix.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Choices for product variants
            const productSelects = document.querySelectorAll('.choices-products');
            productSelects.forEach(select => {
                new Choices(select, {
                    removeItemButton: true,
                    placeholder: true,
                    searchEnabled: true,
                    shouldSort: false,
                    duplicateItemsAllowed: false
                });
            });

            // Preview banner images before upload
            const bannerInput = document.querySelector('#addBannerModal input[name="banners[]"]');
            if (bannerInput) {
                bannerInput.addEventListener('change', function() {
                    const previewContainer = document.getElementById('bannerPreviews');
                    previewContainer.innerHTML = '';

                    Array.from(this.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'banner-preview';
                            img.alt = 'Preview';
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            }
        });
    </script>
@endsection
{{-- PAGE JS END --}}
