@extends('layouts.admin')

@section('title', 'Admin : Parties')

@section('vendor-style')
@endsection

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
            <li class="breadcrumb-item active">Parties</li>
        </ol>
    </nav>
    <div class="mb-9">
      <div class="row g-3 mb-4">
        <div class="col-auto">
          <h2 class="mb-0">Parties</h2>
        </div>
      </div>
      <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
                <span>All </span><span class="text-body-tertiary fw-semibold">({{ $total }})</span>
            </a>
        </li>
      </ul>
      <div id="parties" data-list='{"valueNames":["name","invoices"],"page":10,"pagination":true}'>
        <div class="mb-4">
          <div class="d-flex flex-wrap gap-3">
            <div class="search-box">
              <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search parties" aria-label="Search" />
                <span class="fas fa-search search-box-icon"></span>
              </form>
            </div>
            <div class="ms-xxl-auto">
                <button class="btn btn-primary" id="addBtn" type="button" data-bs-toggle="modal" data-bs-target="#addPartyModal"><span class="fas fa-plus me-2"></span>Add new party</button>

                <!-- Add Party Modal -->
                <div class="modal fade" id="addPartyModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addPartyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header justify-content-between bg-primary">
                          <h5 class="modal-title text-white dark__text-gray-1100" id="addPartyModalLabel">Add new party</h5>
                          <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close">
                            <span class="fas fa-times fs-9 text-white dark__text-gray-1100"></span>
                          </button>
                        </div>
                        <form class="needs-validation" novalidate method="post" action="{{ route('admin.add-party') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label" for="PartyName">Name*</label>
                                        <input class="form-control" id="PartyName" type="text" name="name" placeholder="Party name" required />
                                        <div class="invalid-feedback">Please enter a valid name.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyPhone">Phone*</label>
                                        <input class="form-control" id="PartyPhone" type="text" name="phone" placeholder="Phone number" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyEmail">Email</label>
                                        <input class="form-control" id="PartyEmail" type="email" name="email" placeholder="Email address" />
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label" for="PartyAddress">Address</label>
                                        <textarea class="form-control" id="PartyAddress" name="address" placeholder="Address" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyState">State</label>
                                        <input class="form-control" id="PartyState" type="text" name="state" placeholder="State" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyGSTIN">GSTIN</label>
                                        <input class="form-control" id="PartyGSTIN" type="text" name="gstin" placeholder="GSTIN" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyPAN">PAN</label>
                                        <input class="form-control" id="PartyPAN" type="text" name="pan" placeholder="PAN" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyBank">Bank Name</label>
                                        <input class="form-control" id="PartyBank" type="text" name="bank" placeholder="Bank name" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyIFSC">IFSC Code</label>
                                        <input class="form-control" id="PartyIFSC" type="text" name="ifsc" placeholder="IFSC code" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="PartyAccount">Account Number</label>
                                        <input class="form-control" id="PartyAccount" type="text" name="ac_no" placeholder="Account number" />
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
            </div>
          </div>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
          <div class="table-responsive scrollbar mx-n1 px-1">
            <table class="table fs-9 mb-0">
                <thead>
                    <tr>
                        <th class="sort align-middle ps-4" scope="col" data-sort="name">PARTY INFO</th>
                        <th class="sort align-middle ps-4" scope="col">PURCHASED</th>
                        <th class="sort align-middle ps-4" scope="col">SOLD</th>
                        <th class="sort align-middle ps-4" scope="col">INVOICES</th>
                        <th class="sort align-middle ps-4" scope="col">ACTION</th>
                    </tr>
                </thead>
                <tbody class="list" id="parties-table-body">
                    @foreach($parties as $party)
                    <tr class="position-static">
                        <td class="name align-middle ps-4">
                            <h6 class="mb-0 fw-semibold">{{ $party->name }}</h6>
                            <p class="mb-0 text-body-secondary">{{ $party->phone ?? 'N/A' }}</p>
                            <p class="mb-0 text-body-secondary">{{ $party->email ?? 'N/A' }}</p>
                            <p class="mb-0 text-body-secondary">{{ $party->gstin ?? 'N/A' }}</p>
                        </td>
                        <td class="align-middle ps-4 text-danger">
                            ₹{{ number_format($party->purchase_amount, 2) }}
                        </td>
                        <td class="align-middle ps-4 text-success">
                            ₹{{ number_format($party->sale_amount, 2) }}
                        </td>
                        <td class="align-middle ps-4">
                            {{ $party->purchase_invoices_count + $party->sale_invoices_count }} invoices
                        </td>
                        <td class="align-middle ps-4">
                            <button class="btn btn-phoenix-primary me-1 mb-1" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPartyModal-{{ $party->party_id }}"
                                    title="Edit Party">
                                <span data-feather="edit-3"></span>
                            </button>
                            <button class="btn btn-danger me-1 mb-1" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deletePartyModal-{{ $party->party_id }}"
                                    title="Delete Party">
                                <span data-feather="trash-2"></span>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editPartyModal-{{ $party->party_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Party</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('admin.update-party', $party->party_id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label" for="EditPartyName">Name*</label>
                                                <input class="form-control" id="EditPartyName" type="text" name="name"
                                                       value="{{ $party->name }}" required />
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label" for="EditPartyPhone">Phone*</label>
                                                <input class="form-control" id="EditPartyPhone" type="text" name="phone"
                                                       value="{{ $party->phone }}" required />
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label" for="EditPartyEmail">Email</label>
                                                <input class="form-control" id="EditPartyEmail" type="email" name="email"
                                                       value="{{ $party->email }}" />
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label" for="EditPartyAddress">Address</label>
                                                <textarea class="form-control" id="EditPartyAddress" name="address"
                                                          rows="2">{{ $party->address }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="EditPartyState">State</label>
                                                <input class="form-control" id="EditPartyState" type="text" name="state"
                                                       value="{{ $party->state }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="EditPartyGSTIN">GSTIN</label>
                                                <input class="form-control" id="EditPartyGSTIN" type="text" name="gstin"
                                                       value="{{ $party->gstin }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="EditPartyPAN">PAN</label>
                                                <input class="form-control" id="EditPartyPAN" type="text" name="pan"
                                                       value="{{ $party->pan }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="EditPartyBank">Bank Name</label>
                                                <input class="form-control" id="EditPartyBank" type="text" name="bank"
                                                       value="{{ $party->bank }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="EditPartyIFSC">IFSC Code</label>
                                                <input class="form-control" id="EditPartyIFSC" type="text" name="ifsc"
                                                       value="{{ $party->ifsc }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="EditPartyAccount">Account Number</label>
                                                <input class="form-control" id="EditPartyAccount" type="text" name="ac_no"
                                                       value="{{ $party->ac_no }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deletePartyModal-{{ $party->party_id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Party</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to delete <strong>"{{ $party->name }}"</strong>?</p>
                                    <ul class="text-danger">
                                        <li>This will delete {{ $party->purchaseInvoices->count() + $party->saleInvoices->count() }} associated invoices</li>
                                        <li>This action cannot be undone</li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" action="{{ route('admin.destroy-party', $party->party_id) }}">
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
              <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p><a class="fw-semibold" href="#" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semibold d-none" href="#" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
            </div>
            <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
              <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
            </div>
          </div>
        </div>
      </div>
    </div>
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
<script src="../../vendors/prism/prism.js"></script>
<script src="../../../assets/js/phoenix.js"></script>
@endsection
