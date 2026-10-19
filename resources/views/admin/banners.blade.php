@extends('layouts.admin')

{{-- PAGE TITLE HERE --}}
@section('title', 'Admin : Banners')

{{-- VENDOR CSS START --}}
@section('vendor-style')
@endsection
{{-- VENDOR CSS END --}}

{{-- PAGE CSS START --}}
@section('page-style')
<link href="../../vendors/prism/prism-okaidia.css" rel="stylesheet">
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
@endsection
{{-- PAGE CSS END --}}

{{-- CONTENT START --}}
@section('content')
<div class="content">
    @if ($errors->any())
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div class="toast show text-white bg-danger bg-opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-white">
                <strong class="me-auto">Error</strong>
                <small class="text-light">Just now</small>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
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
        <div class="toast show text-white bg-success bg-opacity-75 border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-white">
                <strong class="me-auto">Success</strong>
                <small class="text-light">Just now</small>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
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
            <li class="breadcrumb-item active">Banners</li>
        </ol>
    </nav>
    <div class="mb-9">
      <div class="row g-3 mb-4">
        <div class="col-auto">
          <h2 class="mb-0">Website Banners</h2>
        </div>
      </div>
      <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
                <span>All </span><span class="text-body-tertiary fw-semibold">({{ $total }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Active </span><span class="text-body-tertiary fw-semibold">({{ $active }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Main </span><span class="text-body-tertiary fw-semibold">({{ $banners->where('type', 'main')->count() }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Promo </span><span class="text-body-tertiary fw-semibold">({{ $banners->where('type', 'promo')->count() }})</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Category </span><span class="text-body-tertiary fw-semibold">({{ $banners->where('type', 'category')->count() }})</span>
            </a>
        </li>
      </ul>
      <div id="banners" data-list='{"valueNames":["title","type","status"],"page":10,"pagination":true}'>
        <div class="mb-4">
          <div class="d-flex flex-wrap gap-3">
            <div class="search-box">
              <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search banners" aria-label="Search" />
                <span class="fas fa-search search-box-icon"></span>
              </form>
            </div>
            <div class="ms-xxl-auto">
                <button class="btn btn-primary" id="addBtn" type="button" data-bs-toggle="modal" data-bs-target="#addBannerModal"><span class="fas fa-plus me-2"></span>Add new banner</button>
            </div>
          </div>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
          <div class="table-responsive scrollbar mx-n1 px-1">
            <table class="table fs-9 mb-0">
                <thead>
                    <tr>
                        <th class="sort align-middle" scope="col" style="width:150px;">IMAGE</th>
                        <th class="sort align-middle ps-4" scope="col" data-sort="title">TITLE</th>
                        <th class="sort align-middle ps-4" scope="col" data-sort="type">TYPE</th>
                        <th class="sort align-middle ps-4" scope="col">TARGET</th>
                        <th class="sort align-middle ps-4" scope="col" data-sort="status">STATUS</th>
                        <th class="sort align-middle ps-4" scope="col">ORDER</th>
                        <th class="sort align-middle ps-4" scope="col">ACTION</th>
                    </tr>
                </thead>
              <tbody class="list" id="banners-table-body">
                @foreach($banners as $banner)
                <tr class="position-static">
                    <td class="align-middle white-space-nowrap py-0">
                        <a class="d-block border rounded-2" href="#">
                            <img src="{{ url('storage/' . $banner->image_url) }}" width="150" class="img-fluid" />
                        </a>
                    </td>
                    <td class="title align-middle ps-4">
                        <b>{{ $banner->title }}</b><br>
                        @if($banner->start_date && $banner->end_date)
                        <small class="text-muted">{{ $banner->start_date->format('M d, Y') }} - {{ $banner->end_date->format('M d, Y') }}</small>
                        @endif
                    </td>
                    <td class="type align-middle ps-4 text-capitalize">
                        {{ $banner->type }}
                        @if($banner->type === 'category' && $banner->category)
                        <br><small class="text-muted">{{ $banner->category->name }}</small>
                        @endif
                    </td>
                    <td class="align-middle ps-4">
                        @if($banner->target_url)
                        <a href="{{ $banner->target_url }}" target="_blank" class="text-truncate d-block" style="max-width: 150px;">{{ $banner->target_url }}</a>
                        @else
                        <span class="text-muted">None</span>
                        @endif
                    </td>
                    <td class="status align-middle ps-4">
                        <span class="badge badge-phoenix fs-10 {{ $banner->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $banner->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="align-middle ps-4">
                        {{ $banner->display_order }}
                    </td>
                    <td class="align-middle ps-4">
                        <button class="btn btn-phoenix-primary me-1 mb-1" type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#editBannerModal-{{ $banner->banner_id }}">
                            <span data-feather="edit"></span>
                        </button>
                        <button class="btn btn-danger me-1 mb-1" type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteBannerModal-{{ $banner->banner_id }}">
                            <span data-feather="trash"></span>
                        </button>
                    </td>
                </tr>

                <!-- Edit Banner Modal -->
                <div class="modal fade" id="editBannerModal-{{ $banner->banner_id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header justify-content-between bg-primary">
                                <h5 class="modal-title text-white dark__text-gray-1100">Edit Banner</h5>
                                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="fas fa-times fs-9 text-white dark__text-gray-1100"></span>
                                </button>
                            </div>
                            <form method="post" action="{{ route('admin.banners.update', $banner->banner_id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-12 text-center">
                                            <label for="editImage-{{ $banner->banner_id }}" class="d-block">
                                                <div class="border border-2 border-dashed rounded p-4" style="cursor: pointer; max-width: 300px; margin: auto; background-color: #f8f9fa;">
                                                    <img id="editImagePreview-{{ $banner->banner_id }}" src="{{ url('storage/' . $banner->image_url) }}" alt="banner image" class="img-fluid mb-3" style="max-height: 150px;" />
                                                    <p class="text-muted">Click to change image</p>
                                                </div>
                                            </label>
                                            <input class="form-control d-none" id="editImage-{{ $banner->banner_id }}" type="file" accept=".jpg, .jpeg, .png, .webp" name="image" />
                                        </div>
                                        <div class="col-md-12">
                                            <input class="form-control" type="text" name="title" placeholder="Banner title" value="{{ $banner->title }}" required />
                                        </div>
                                        <div class="col-md-12">
                                            <select class="form-select" name="type" id="bannerType-{{ $banner->banner_id }}" required>
                                                <option value="main" {{ $banner->type === 'main' ? 'selected' : '' }}>Main Banner</option>
                                                <option value="promo" {{ $banner->type === 'promo' ? 'selected' : '' }}>Promo Banner</option>
                                                <option value="category" {{ $banner->type === 'category' ? 'selected' : '' }}>Category Banner</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12" id="categoryField-{{ $banner->banner_id }}" style="{{ $banner->type !== 'category' ? 'display: none;' : '' }}">
                                            <select class="form-select" name="category_id">
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{ $category->category_id }}" {{ $banner->category_id == $category->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <input class="form-control" type="url" name="target_url" placeholder="Target URL (optional)" value="{{ $banner->target_url }}" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Start Date</label>
                                            <input class="form-control datetimepicker" type="text" name="start_date" placeholder="d/m/y" data-options='{"dateFormat":"d/m/Y"}' value="{{ $banner->start_date ? $banner->start_date->format('d/m/Y') : '' }}" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End Date</label>
                                            <input class="form-control datetimepicker" type="text" name="end_date" placeholder="d/m/y" data-options='{"dateFormat":"d/m/Y"}' value="{{ $banner->end_date ? $banner->end_date->format('d/m/Y') : '' }}" />
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" id="flexSwitchCheckDefault" type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} />
                                                <label class="form-check-label" for="flexSwitchCheckDefault">Active</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input class="form-control" type="number" name="display_order" placeholder="Display order" value="{{ $banner->display_order }}" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary" type="submit">Update</button>
                                    <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Banner Modal -->
                <div class="modal fade" id="deleteBannerModal-{{ $banner->banner_id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Banner</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete "{{ $banner->title }}"? This action cannot be undone.
                            </div>
                            <div class="modal-footer">
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner->banner_id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
              <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
              <a class="fw-semibold" href="#" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
              <a class="fw-semibold d-none" href="#" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
            </div>
            <div class="col-auto d-flex">
              <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
              <ul class="mb-0 pagination"></ul>
              <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Banner Modal -->
    <div class="modal fade" id="addBannerModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addBannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header justify-content-between bg-primary">
                    <h5 class="modal-title text-white dark__text-gray-1100" id="addBannerModalLabel">Add new banner</h5>
                    <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fas fa-times fs-9 text-white dark__text-gray-1100"></span>
                    </button>
                </div>
                <form class="needs-validation" novalidate method="post" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12 text-center">
                                <label for="addBannerImage" class="d-block">
                                    <div class="border border-2 border-dashed rounded p-4" style="cursor: pointer; max-width: 300px; margin: auto; background-color: #f8f9fa;">
                                        <img id="addBannerImagePreview" src="../../../assets/img/icons/cloud-upload.svg" alt="banner image" class="img-fluid mb-3" style="max-height: 150px;" />
                                        <p class="text-muted">Click or drag to upload</p>
                                    </div>
                                </label>
                                <input class="form-control d-none" id="addBannerImage" type="file" accept=".jpg, .jpeg, .png, .webp" name="image" required />
                                <div class="invalid-feedback">Please select a valid image.</div>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="title" placeholder="Banner title" required />
                            </div>
                            <div class="col-md-12">
                                <select class="form-select" name="type" id="bannerType" required>
                                    <option value="">Select Banner Type</option>
                                    <option value="main">Main Banner</option>
                                    <option value="promo">Promo Banner</option>
                                    <option value="category">Category Banner</option>
                                </select>
                            </div>
                            <div class="col-md-12" id="categoryField" style="display: none;">
                                <select class="form-select" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control" type="url" name="target_url" placeholder="Target URL (optional)" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input class="form-control datetimepicker" type="text" name="start_date" placeholder="d/m/y" data-options='{"dateFormat":"d/m/Y"}' />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input class="form-control datetimepicker" type="text" name="end_date" placeholder="d/m/y" data-options='{"dateFormat":"d/m/Y"}' />
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" id="flexSwitchCheckDefaultNew" type="checkbox" name="is_active" value="1" checked />
                                    <label class="form-check-label" for="flexSwitchCheckDefaultNew">Active</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input class="form-control" type="number" name="display_order" placeholder="Display order" value="0" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Cancel</button>
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
<script src="../../../vendors/popper/popper.min.js"></script>
<script src="../../../vendors/bootstrap/bootstrap.min.js"></script>
<script src="../../../vendors/anchorjs/anchor.min.js"></script>
<script src="../../../vendors/is/is.min.js"></script>
<script src="../../../vendors/fontawesome/all.min.js"></script>
<script src="../../../vendors/lodash/lodash.min.js"></script>
<script src="../../../vendors/list.js/list.min.js"></script>
<script src="../../../vendors/feather-icons/feather.min.js"></script>
<script src="../../../vendors/dayjs/dayjs.min.js"></script>
<script src="../../vendors/prism/prism.js"></script>
<script src="../../../assets/js/phoenix.js"></script>
<script>
    // Image preview for add banner
    const addBannerImage = document.getElementById('addBannerImage');
    const addBannerImagePreview = document.getElementById('addBannerImagePreview');

    addBannerImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                addBannerImagePreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Image preview for edit banners
    @foreach($banners as $banner)
    const editImage{{ $banner->banner_id }} = document.getElementById('editImage-{{ $banner->banner_id }}');
    const editImagePreview{{ $banner->banner_id }} = document.getElementById('editImagePreview-{{ $banner->banner_id }}');

    if (editImage{{ $banner->banner_id }}) {
        editImage{{ $banner->banner_id }}.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    editImagePreview{{ $banner->banner_id }}.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
    @endforeach

    // Show/hide category field based on banner type
    document.getElementById('bannerType').addEventListener('change', function() {
        const categoryField = document.getElementById('categoryField');
        if (this.value === 'category') {
            categoryField.style.display = 'block';
        } else {
            categoryField.style.display = 'none';
        }
    });

    // For edit modals
    @foreach($banners as $banner)
    document.getElementById('bannerType-{{ $banner->banner_id }}').addEventListener('change', function() {
        const categoryField = document.getElementById('categoryField-{{ $banner->banner_id }}');
        if (this.value === 'category') {
            categoryField.style.display = 'block';
        } else {
            categoryField.style.display = 'none';
        }
    });
    @endforeach
</script>
@endsection
{{-- PAGE JS END --}}
