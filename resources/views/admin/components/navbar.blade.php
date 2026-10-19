<!-- SIDE BAR START -->
<nav class="navbar navbar-vertical navbar-expand-lg">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <!-- scrollbar removed-->
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.dashboard') }}" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="pie-chart"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span></div>
                        </a>
                    </div>
                    {{-- <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="shopping-bag"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Sell Product</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="check-square"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Verify Product</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="#" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="shopping-cart"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Purchase Product</span></span></div>
                        </a>
                    </div> --}}
                </li>
                {{-- <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Orders Management</p>
                    <hr class="navbar-vertical-line" />

                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="apps/chat.html" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="archive"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Orders</span></span></div>
                        </a>
                    </div>

                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="apps/chat.html" role="button"
                            data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="box"></span></span><span
                                    class="nav-link-text-wrapper"><span class="nav-link-text">New Orders</span></span></div>
                        </a>
                    </div>

                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="apps/chat.html" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="clock"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Pending Orders</span></span></div>
                        </a>
                    </div>

                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="apps/chat.html" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="check-circle"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Delivered Orders</span></span></div>
                        </a>
                    </div>

                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="apps/chat.html" role="button"
                        data-bs-toggle="" aria-expanded="false">
                        <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                    data-feather="alert-circle"></span></span><span
                                class="nav-link-text-wrapper"><span class="nav-link-text">Problematic Orders</span></span></div>
                        </a>
                    </div>
                </li> --}}
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Products Management</p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.products') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="align-left"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Products</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.products', ['status' => 'active']) }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="globe"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Live Products</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.add-product') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="plus-square"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Add New Product</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.products', ['stock' => 'out']) }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="alert-triangle"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Out Of Stock Products</span></span></div>
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Utility & App Management</p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.brands') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="briefcase"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Brands</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.parties') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="meh"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Parties</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.attributes') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="sliders"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Attributes</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.categories') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="grid"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Categories</span></span></div>
                        </a>
                    </div>
                </li>
                <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Shops & Users Management</p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.banners')}}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="layout"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">App Management</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.distributors') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="trello"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Shops Management</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    {{-- <div class="nav-item-wrapper"><a class="nav-link label-1" href="{{ route('admin.users') }}"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="users"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Customers Management</span></span></div>
                        </a>
                    </div> --}}
                </li>
                {{-- <li class="nav-item">
                    <!-- label-->
                    <p class="navbar-vertical-label">Transaction Reports & Invoices</p>
                    <hr class="navbar-vertical-line" />
                    <!-- parent pages-->
                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="pages/starter.html"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="bar-chart-2"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Sell Reports</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="pages/starter.html"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="bar-chart-2"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Loan Reports</span></span></div>
                        </a>
                    </div>
                    <!-- parent pages-->

                    <div class="nav-item-wrapper"><a class="nav-link label-1" href="pages/starter.html"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center"><span class="nav-link-icon"><span
                                        data-feather="bar-chart-2"></span></span><span class="nav-link-text-wrapper"><span
                                        class="nav-link-text">Purchase Reports</span></span></div>
                        </a>
                    </div>
                </li> --}}
            </ul>
        </div>
    </div>
    <div class="navbar-vertical-footer"><button
            class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center"><span
                class="uil uil-left-arrow-to-left fs-8"></span><span
                class="uil uil-arrow-from-right fs-8"></span><span
                class="navbar-vertical-footer-text ms-2">Collapsed View</span></button></div>
</nav>
<!-- SIDE BAR END -->

<!-- TOP_NAV START -->
<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
    <div class="collapse navbar-collapse justify-content-between">

        <div class="navbar-logo">
            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse"
                aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span
                    class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="{{ route('admin.dashboard') }}">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center">
                        <img src="../../assets/img/icons/logo.png" alt="SMART TECH" width="27" />
                        <h5 class="logo-text bold ms-2 d-none d-sm-block">SMART TECH</h5>
                    </div>
                </div>
            </a>
        </div>

        <ul class="navbar-nav navbar-nav-icons flex-row">
            <li class="nav-item">
                <div class="theme-control-toggle fa-icon-wait px-2"><input
                        class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                        data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label
                        class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle"
                        data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme"
                        style="height:32px;width:32px;"><span class="icon"
                            data-feather="moon"></span></label><label
                        class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle"
                        data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="Switch theme"
                        style="height:32px;width:32px;"><span class="icon" data-feather="sun"></span></label>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" href="index.html#" style="min-width: 2.25rem" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    data-bs-auto-close="outside"><span class="d-block" style="height:20px;width:20px;"><span
                            data-feather="bell" style="height:20px;width:20px;"></span></span></a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret"
                    id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                    <div class="card position-relative border-0">
                        <div class="card-header p-2">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-body-emphasis mb-0">Notifications</h5><button
                                    class="btn btn-link p-0 fs-9 fw-normal" type="button">Mark all as read</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="scrollbar-overlay" style="height: 27rem;">

                                {{-- <div class="px-2 px-sm-3 py-3 notification-card position-relative read border-bottom">
                                    <div class="d-flex align-items-center justify-content-between position-relative">
                                        <div class="d-flex">
                                            <div class="avatar avatar-m status-online me-3"><img
                                                    class="rounded-circle" src="../../assets/img/team/40x40/30.webp"
                                                    alt="" /></div>
                                            <div class="flex-1 me-sm-3">
                                                <h4 class="fs-9 text-body-emphasis">Jessie Samson</h4>
                                                <p class="fs-9 text-body-highlight mb-2 mb-sm-3 fw-normal"><span
                                                        class='me-1 fs-10'>💬</span>Mentioned you in a comment.<span
                                                        class="ms-2 text-body-quaternary text-opacity-75 fw-bold fs-10">10m</span>
                                                </p>
                                                <p class="text-body-secondary fs-9 mb-0"><span
                                                        class="me-1 fas fa-clock"></span><span class="fw-bold">10:41
                                                        AM </span>August 7,2021</p>
                                            </div>
                                        </div>
                                        <div class="dropdown notification-dropdown"><button
                                                class="btn fs-10 btn-sm dropdown-toggle dropdown-caret-none transition-none"
                                                type="button" data-bs-toggle="dropdown" data-boundary="window"
                                                aria-haspopup="true" aria-expanded="false"
                                                data-bs-reference="parent"><span
                                                    class="fas fa-ellipsis-h fs-10 text-body"></span></button>
                                            <div class="dropdown-menu py-2"><a class="dropdown-item"
                                                    href="index.html#!">Mark as unread</a></div>
                                        </div>
                                    </div>
                                </div> --}}

                            </div>
                        </div>
                        <div class="card-footer p-0 border-top border-translucent border-0">
                            <div class="my-2 text-center fw-bold fs-10 text-body-tertiary text-opactity-85"><a
                                    class="fw-bolder" href="pages/notifications.html">Notification history</a></div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" id="navbarDropdownNindeDots" href="index.html#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" data-bs-auto-close="outside"
                    aria-expanded="false"><svg width="16" height="16" viewbox="0 0 16 16"
                        fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="2" cy="2" r="2" fill="currentColor"></circle>
                        <circle cx="2" cy="8" r="2" fill="currentColor"></circle>
                        <circle cx="2" cy="14" r="2" fill="currentColor"></circle>
                        <circle cx="8" cy="8" r="2" fill="currentColor"></circle>
                        <circle cx="8" cy="14" r="2" fill="currentColor"></circle>
                        <circle cx="14" cy="8" r="2" fill="currentColor"></circle>
                        <circle cx="14" cy="14" r="2" fill="currentColor"></circle>
                        <circle cx="8" cy="2" r="2" fill="currentColor"></circle>
                        <circle cx="14" cy="2" r="2" fill="currentColor"></circle>
                    </svg></a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-nine-dots shadow border"
                    aria-labelledby="navbarDropdownNindeDots">
                    <div class="card bg-body-emphasis position-relative border-0">
                        <div class="card-body pt-3 px-3 pb-0 overflow-auto scrollbar" style="height: 20rem;">
                            <div class="row text-center align-items-center gx-0 gy-0">
                                {{-- <div class="col-4"><a
                                        class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                        href="index.html#!"><img src="../../assets/img/nav-icons/behance.webp"
                                            alt="" width="30" />
                                        <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Behance</p>
                                    </a></div>
                                <div class="col-4"><a
                                        class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                        href="index.html#!"><img src="../../assets/img/nav-icons/google-cloud.webp"
                                            alt="" width="30" />
                                        <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Cloud</p>
                                    </a></div>
                                <div class="col-4"><a
                                        class="d-block bg-body-secondary-hover p-2 rounded-3 text-center text-decoration-none mb-3"
                                        href="index.html#!"><img src="../../assets/img/nav-icons/slack.webp"
                                            alt="" width="30" />
                                        <p class="mb-0 text-body-emphasis text-truncate fs-10 mt-1 pt-1">Slack</p>
                                    </a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser"
                    href="index.html#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-l ">
                        <img class="rounded-circle " src="../../assets/img/team/avatar-placeholder.webp" alt="" />
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border"
                    aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="card-body p-0">
                            <div class="text-center pt-4 pb-3">
                                <div class="avatar avatar-xl ">
                                    <img class="rounded-circle " src="../../assets/img/team/avatar-placeholder.webp"
                                        alt="" />
                                </div>
                                <h6 class="mt-2 text-body-emphasis">{{ auth()->user()->name }}</h6>
                            </div>
                        </div>
                        <div class="overflow-auto scrollbar" style="height: 4rem;">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-block" href="{{ route('admin.profile') }}"> <span
                                            class="me-2 text-body align-bottom"
                                            data-feather="user"></span><span>Profile</span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link px-3 d-block" href="{{ route('admin.dashboard') }}"><span
                                            class="me-2 text-body align-bottom"
                                            data-feather="pie-chart"></span>Dashboard</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer p-0">
                            <hr />
                            <div class="px-3"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100"
                                    href="{{ route('logout') }}"> <span class="me-2" data-feather="log-out"> </span>Sign
                                    out</a></div>
                            <div class="my-2 text-center fw-bold fs-10 text-body-quaternary"><a
                                    class="text-body-quaternary me-1" href="{{ route('privacy') }}">Privacy policy</a>&bull;<a
                                    class="text-body-quaternary mx-1" href="{{ route('terms') }}">Terms</a>&bull;<a
                                    class="text-body-quaternary ms-1" href="{{ route('terms') }}">Cookies</a></div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- TOP_NAV END -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var currentUrl = window.location.href;
        var navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(function(link) {
            var href = link.href;

            if (href === currentUrl) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>
