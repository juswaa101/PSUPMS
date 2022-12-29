<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Icon -->
    <link rel="icon" href="{{ asset('assets/login/psu.png') }}" type="image/gif/png/jpg/jpeg">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://kit.fontawesome.com/6d6b82be0b.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css" />
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>

    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <title>Admin - @yield('title')</title>
    @yield('style')
    <style>
        /* Google Fonts Import Link */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #E4E9F7;
        }

        a {
            text-decoration: none;
        }

        .modal-dialog {
            overflow-y: initial !important
        }

        .modal-body {
            height: auto;
            overflow-y: auto;
        }


        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 260px;
            background: #11101d;
            z-index: 100;
            transition: all 0.5s ease;
        }

        .sidebar.close {
            width: 78px;
        }

        .sidebar .logo-details {
            height: 60px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .sidebar .logo-details i {
            font-size: 30px;
            color: #fff;
            height: 50px;
            min-width: 78px;
            text-align: center;
            line-height: 50px;
        }

        .sidebar .logo-details .logo_name {
            font-size: 18px;
            color: #fff;
            padding-top: 20px;
            font-weight: 600;
            transition: 0.3s ease;
            transition-delay: 0.1s;
        }

        .sidebar.close .logo-details .logo_name {
            transition-delay: 0s;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar .nav-links {
            height: 100%;
            padding: 30px 0 150px 0;
            overflow: auto;
        }

        .sidebar.close .nav-links {
            overflow: visible;
        }

        .sidebar .nav-links::-webkit-scrollbar {
            display: none;
        }

        .sidebar .nav-links li {
            position: relative;
            list-style: none;
            transition: all 0.4s ease;
        }

        .sidebar .nav-links li:hover {
            background: #1d1b31;
        }

        .sidebar .nav-links li .icon-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar.close .nav-links li .icon-link {
            display: block
        }

        .sidebar .nav-links li i {
            height: 50px;
            min-width: 78px;
            text-align: center;
            line-height: 50px;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar .nav-links li.showMenu i.arrow {
            transform: rotate(-180deg);
        }

        .sidebar.close .nav-links i.arrow {
            display: none;
        }

        .sidebar .nav-links li a {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar .nav-links li a .link_name {
            font-size: 18px;
            font-weight: 400;
            color: #fff;
            transition: all 0.4s ease;
        }

        .sidebar.close .nav-links li a .link_name {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar .nav-links li .sub-menu {
            padding: 6px 6px 14px 80px;
            margin-top: -10px;
            background: #1d1b31;
            display: none;
        }

        .sidebar .nav-links li.showMenu .sub-menu {
            display: block;
        }

        .sidebar .nav-links li .sub-menu a {
            color: #fff;
            font-size: 15px;
            padding: 5px 0;
            white-space: nowrap;
            opacity: 0.6;
            transition: all 0.3s ease;
        }

        .sidebar .nav-links li .sub-menu a:hover {
            opacity: 1;
        }

        .sidebar.close .nav-links li .sub-menu {
            position: absolute;
            left: 100%;
            top: -10px;
            margin-top: 0;
            padding: 10px 20px;
            border-radius: 0 6px 6px 0;
            opacity: 0;
            display: block;
            pointer-events: none;
            transition: 0s;
        }

        .sidebar.close .nav-links li:hover .sub-menu {
            top: 0;
            opacity: 1;
            pointer-events: auto;
            transition: all 0.4s ease;
        }

        .sidebar .nav-links li .sub-menu .link_name {
            display: none;
        }

        .sidebar.close .nav-links li .sub-menu .link_name {
            font-size: 18px;
            opacity: 1;
            display: block;
        }

        .sidebar .nav-links li .sub-menu.blank {
            opacity: 1;
            pointer-events: auto;
            padding: 3px 20px 6px 16px;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar .nav-links li:hover .sub-menu.blank {
            top: 50%;
            transform: translateY(-50%);
        }

        .sidebar .profile-details {
            position: fixed;
            bottom: 0;
            width: 260px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1d1b31;
            padding: 12px 0;
            transition: all 0.5s ease;
        }

        .sidebar.close .profile-details {
            background: none;
        }

        .sidebar.close .profile-details {
            width: 78px;
        }

        .sidebar .profile-details img {
            height: 52px;
            width: 52px;
            object-fit: cover;
            border-radius: 16px;
            margin: 0 14px 0 12px;
            background: #1d1b31;
            transition: all 0.5s ease;
        }

        .sidebar.close .profile-details img {
            padding: 10px;
        }

        .sidebar .profile-details .logout,
        .sidebar .profile-details .job {
            color: #fff;
            font-size: 18px;
            font-weight: 500;
            white-space: nowrap;
            padding-left: 20px;
        }

        .sidebar.close .profile-details i,
        .sidebar.close .profile-details .logout,
        .sidebar.close .profile-details .job {
            display: none;
        }

        .sidebar .profile-details .job {
            font-size: 12px;
        }

        .home-section {
            position: relative;
            background: #E4E9F7;
            height: auto;
            left: 260px;
            width: calc(100% - 260px);
            transition: all 0.5s ease;
            display: inline-block;
        }

        .sidebar.close~.home-section {
            left: 78px;
            width: calc(100% - 78px);
        }

        .home-section .home-content {
            height: 60px;
            display: flex;
            align-items: center;
        }

        .home-section .home-content .bx-menu,
        .home-section .home-content .text {
            color: #11101d;
            font-size: 35px;
        }

        .home-section .home-content .bx-menu {
            margin: 0 15px;
            cursor: pointer;
        }

        .home-section .home-content .text {
            font-size: 26px;
            font-weight: 600;
        }

        @media (max-width: 400px) {
            .sidebar.close .nav-links li .sub-menu {
                display: none;
            }

            .sidebar {
                width: 78px;
            }

            .sidebar.close {
                width: 0;
            }

            .home-section {
                left: 78px;
                width: calc(100% - 78px);
                z-index: 100;
            }

            .sidebar.close~.home-section {
                width: 100%;
                left: 0;
            }
        }

        .imgs {
            padding-top: 10px;
        }

        .logout {
            padding-top: 18px
        }

        .offcanvas {
            width: 900px;
        }

        .notification-invitation {
            .card-body {
                .card-title:before {
                    color: #90CAF9;
                    border-color: #90CAF9;
                    content: "\f007";
                }
            }
        }

        .countBadge {
            margin-top: -25px;
            border-radius: 30px;
            margin-left: -10px;
        }
    </style>
</head>

<body oncontextmenu="return false;">
    @yield('content')
    @if (isset(Auth::user()->email))

        <!-- Sidebar -->
        <div class="sidebar close">
            <div class="logo-details">
                <i><img src="{{ asset('assets/login/psu.png') }}" width="55" height="61" class="imgs"></i>
                <span class="logo_name">PMS</span>
            </div>


            <ul class="nav-links">
                <!-- Dashboard -->
                <li>
                    <a href="/admin/dashboard">
                        <i class='bx bx-grid-alt'></i>
                        <span class="link_name">Dashboard</span>
                    </a>

                    <!-- hover -->
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="/admin/dashboard">Dashboard</a></li>
                    </ul>
                </li>

                <!-- User Management -->
                <li>
                    <a href="/admin/user-management">
                        <i class='bx bx-user'></i>
                        <span class="link_name">User Management</span>
                    </a>

                    <!-- hover -->
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="/admin/user-management">User Management</a></li>
                    </ul>
                </li>


                <!-- Project -->
                <li>
                    <div class="icon-link">
                        <a href="/admin/project">
                            <i class='bx bx-collection'></i>
                            <span class="link_name">Projects</span>
                        </a>
                        <i class='bx bxs-chevron-down arrow'></i>
                    </div>

                    <!-- hover -->
                    <ul class="sub-menu">
                        <li><a class="link_name" href="/admin/project/">Projects</a></li>
                        @if ($fetchLimitProject != null)
                            @foreach ($fetchLimitProject as $item)
                                <li><a href="/admin/project/{{ $item->uuid }}">{{ $item->project_title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </li>


                <li>
                    <!-- Reports -->
                    <a href="/admin/reports">
                        <i class='bx bx-pie-chart-alt-2'></i>
                        <span class="link_name">Reports</span>
                    </a>

                    <!-- hover -->
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="/admin/reports">Reports</a></li>
                    </ul>
                </li>

                <!-- Logout -->
                <li>
                    <div class="profile-details">
                        <div class="name-job">
                            <div class="logout"><a href="/logout">
                                    <p style="color:white;">Logout</p>
                                </a></div>
                        </div>
                        <a href="/logout"><i class='bx bx-log-out'></i></a>
                    </div>
                </li>
            </ul>
        </div>

        <!-- End Sidebar -->

        <section class="home-section">
            <div class="home-content">
                <!-- menu icon -->
                <i class='bx bx-menu'></i>

                <!-- bell icon -->
                <i class='bx bx-bell bx-sm bx-tada-hover bx-border-circle' data-bs-target="#offcanvasNotification"
                    data-bs-toggle="offcanvas"></i><span class="badge bg-danger countBadge"
                    id="countNotification"></span>&nbsp;&nbsp;

                <!-- user icon -->
                <i class='bx bx-user bx-sm bx-border-circle' data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions"></i>
                <!-- <i class='bx bx-user bx-sm bx-border-circle' data-bs-toggle="modal" data-bs-target="#profileModal"></i> -->
            </div>
            @yield('time')
            @yield('user-management')
            @yield('scripts')
            @yield('reports')
            @yield('reports-scripts')
        </section>

        <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Profile</h4>


                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                @foreach ($user_profile as $profile)
                    <div class="row">
                        <div class="form-group">
                            <div style="text-align: center;">
                                @if ($profile->image == null)
                                    <img src="{{ asset('assets/login/psu.png') }}" height="120" width="120"
                                        class="img-responsive mt-3" alt="Profile" style="border-radius: 500px;">
                                @else
                                    <img src="{{ asset('assets/users/' . $profile->image) }}" height="120"
                                        width="120" class="img-responsive mt-3" alt="Profile"
                                        style="border-radius: 500px;">
                                @endif
                                <div class="form-group mt-3">
                                    <p><b style="color: #0a53be">{{ Str::upper($profile->role) }}</b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ps-2 row text">

                        <div class="row text-start mt-3">
                            <div class="col-md-1">
                                <i class='bx bx-user-pin bx-sm'></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="fw-bold">{{ $profile->name }}</h6>
                            </div>
                        </div>


                        <div class="row text-start mt-3">
                            <div class="col-md-1">
                                <i class='bx bx-user-circle bx-sm'></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="fw-bold">{{ $profile->username }}</h6>
                            </div>
                        </div>


                        <div class="row text-start mt-3">
                            <div class="col-md-1">
                                <i class='bx bx-envelope bx-sm'></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="fw-bold">{{ $profile->email }}</h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div id="offcanvasNotification" aria-labelledby="offcanvasWithBothOptionsLabel"
            class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Notifications</h4>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                @if (count($notification) != 0)
                    <a href="/read-all/notification" class="text text-primary">Mark as all read</a>
                @endif
                @forelse($notification as $notify)
                    <div class="card notification-card notification-invitation mb-2 mt-2">
                        <div class="card-body">
                            <table>
                                <tr>
                                    <td>
                                        @if ($notify->image != null)
                                            <img class="rounded-circle shadow-4 float-start"
                                                src="{{ url('assets/users/' . $notify->image) }}" alt="user_image"
                                                height="50px" width="50px" style="margin-right: 10px;">
                                            <div class="card-title fw-bold">{{ $notify->name }}</div>
                                            <div class="card-title">{{ $notify->notification_message }}</div>
                                        @else
                                            <img class="rounded-circle shadow-4 float-start"
                                                src="{{ url('assets/login/psu.png') }}" alt="user_image"
                                                height="50px" width="50px" style="margin-right: 10px;">
                                            <div class="card-title fw-bold">{{ $notify->name }}</div>
                                            <div class="card-title">{{ $notify->notification_message }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:30%" class="mt-2">
                                        <div class="row">
                                            @if ($notify->has_read == 0)
                                                <div class="col-md-6">
                                                    <a href="/read/notification/{{ $notify->notify_id }}"
                                                        class="btn btn-primary w-100">Mark as Read</a>
                                                </div>
                                            @else
                                                <div class="col-md-6">
                                                    <a href="/unread/notification/{{ $notify->notify_id }}"
                                                        class="btn btn-secondary w-100">Mark Unread</a>
                                                </div>
                                            @endif
                                            <div class="col-md-6">
                                                <a href="/delete/notification/{{ $notify->notify_id }}"
                                                    class="btn btn-danger w-100">Dismiss</a>
                                            </div>
                                        </div>
                                        <p class="text-muted float-end mt-3">{{ $notify->created }}</p>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="container p-3">
                        <h4 class="text-center">No Notifications Yet</h4>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</body>

</html>

<script>
    document.onkeydown = function(e) {
        if (event.keyCode == 123) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
            return false;
        }
        if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
            return false;
        }
        if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
            return false;
        }
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>

<!--  Select2  -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Javascript -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#name').select2({
            width: '200px',
            dropdownCssClass: "bigdrop"
        });
        $('#projects').select2({
            width: '200px',
            dropdownCssClass: "bigdrop"
        });
    });

    function loadNotification() {
        let http = new XMLHttpRequest();
        http.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                let countNotification = document.getElementById("countNotification");
                countNotification.innerHTML =
                    parseInt(this.responseText) > 99 ? "99+" : parseInt(this.responseText) == 0 ?
                    countNotification.style.display = "none" :
                    countNotification.innerHTML = this.responseText;
            }
        };
        http.open("GET", "/count/notification", true);
        http.send();
    }

    loadNotification();
</script>

{{-- Sidebar Script --}}
<script type="text/javascript">
    let arrow = document.querySelectorAll(".arrow");
    for (var i = 0; i < arrow.length; i++) {
        arrow[i].addEventListener("click", (e) => {
            let arrowParent = e.target.parentElement.parentElement; //selecting main parent of arrow
            arrowParent.classList.toggle("showMenu");
        });
    }
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".bx-menu");
    sidebarBtn.addEventListener("click", () => {
        sidebar.classList.toggle("close");
    });

    function display_ct6() {
        let x = new Date();
        let ampm = x.getHours() >= 12 ? ' PM' : ' AM';
        hours = x.getHours() % 12;
        hours = hours ? hours : 12;
        let x1 = x.toDateString();
        let checkSeconds = x.getSeconds() < 10 && x.getSeconds() >= 0 ? "0" + x.getSeconds() : x.getSeconds();
        let checkMinute = x.getMinutes() < 10 && x.getMinutes() >= 0 ? "0" + x.getMinutes() : x.getMinutes();
        x1 = x1 + " - " + hours + ":" + checkMinute + ":" + checkSeconds + ampm;
        if (document.getElementById('time') != null) {
            document.getElementById('time').innerHTML = x1;
            display_c6();
        }
    }

    function display_c6() {
        var refresh = 1000; // Refresh rate in milli seconds
        mytime = setTimeout('display_ct6()', refresh)
    }

    display_c6()
</script>
