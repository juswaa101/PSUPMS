<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <script>
        window.laravel = {
            csrfToken: '{{ csrf_token() }}'
        }
    </script>

    <title>@yield('title')</title>

    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <!-- Default theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <!-- JavaScript -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
        crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://kit.fontawesome.com/6d6b82be0b.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

    {{-- FilePond styles --}}
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jscharting.com/latest/jscharting.js"></script>
    <script type="text/javascript" src="https://code.jscharting.com/latest/modules/toolbar.js"></script>

    {{--  Icons  --}}
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/master.css') }}" rel="stylesheet">
    @yield('meta')
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
            height: 60vh;
            overflow-y: auto;
        }

        .card-h {
            background-color: #00305F;
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
            display: inline-block;
            left: 260px;
            width: calc(100% - 260px);
            transition: all 0.5s ease;
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
            padding-top: 18px;
        }

        .plus {
            padding-left: 985px;
        }

        .date {
            font-size: 11px
        }

        .comment-text {
            font-size: 12px
        }

        .dropdown-menu.comment-option {
            top: 3em;
        }

        .countBadge {
            margin-top: -25px;
            border-radius: 30px;
            margin-left: -10px;
        }

        .box {
            width: 1050px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 25px;
        }

        #page_list li {
            padding: 16px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: move;
            margin-top: 12px;
        }

        #page_list li.ui-state-highlight {
            padding: 24px;
            background-color: #ffffcc;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: move;
            margin-top: 12px;
        }

        #board_list li {
            padding: 16px;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: move;
            margin-top: 12px;
        }

        #board_list li.ui-state-highlight {
            padding: 24px;
            background-color: #ffffcc;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: move;
            margin-top: 12px;
        }
    </style>
</head>

<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    {{--  Emojione scripts  --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"></script>

    {{-- Select2 scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- FilePond scripts -->
    <script src="https://unpkg.com/filepond-plugin-file-metadata/dist/filepond-plugin-file-metadata.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
</body>

</html>
<script type="text/javascript">
    let is_invited = [];

    function isInvited() {
        $.ajax({
            type: "get",
            url: "/head/check-if-invited",
            dataType: "json",
            async: false,
            success: function(response) {
                is_invited.push(response.invitation);
            }
        });
    }

    isInvited();

    $(document).ready(function() {
        $('#selectMembers').select2({
            placeholder: 'Select Project Members',
            maximumSelectionLength: 20,
        });
        $('#selectHeads').select2({
            placeholder: 'Select Project Head',
            maximumSelectionLength: 1,
        });
        $('#offcanvasViewTask').on('shown.bs.offcanvas', function() {
            $('#commentArea').emojioneArea({
                autocomplete: false,
                placeholder: 'Type a comment here',
                pickerPosition: 'bottom',
                filtersPosition: 'bottom',
            });
        });
        $("#page_list").sortable({
            placeholder: "ui-state-highlight",
            update: (event, ui) => {
                let boardIds = [];
                $('#page_list li').each(function() {
                    boardIds.push($(this).attr("id"));
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "/head/update-order",
                    method: "PUT",
                    data: {
                        boardIds: boardIds,
                    },
                    success: function(data) {
                        alertify.notify(data, 'success', '5', () => {});
                        location.reload();
                    }
                });
            }
        });
        $("#board_list").sortable({
            placeholder: "ui-state-highlight",
            update: (event, ui) => {
                let boardIds = [];
                $('tbody tr').each(function() {
                    boardIds.push($(this).attr("id"));
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "/head/update-order",
                    method: "PUT",
                    data: {
                        boardIds: boardIds,
                    },
                    success: function(data) {
                        alertify.notify(data, 'success', '5', () => {});
                        location.reload();
                    }
                });
            }
        });

        let currentId = is_invited[0];
        currentId.sort();
        currentId.forEach(el => {
            $('#selectMembers').children('option[value="' + el + '"]').append(` - INVITED`).prop('disabled', true);
        });

        loadNotification();
        loadInvitation();

        function loadInvitation() {
            $.ajax({
                type: "get",
                url: "/count/invitations",
                dataType: "json",
                success: (response) => {
                    if (response.invitation.length != 0) {
                        $.each(response.invitation, function(key, value) {
                            $('#pop_up').css("display", "");
                            $('#pop_up').html(
                                '<div class="toast-container position-absolute top-0 end-0 p-3">' +
                                '<div role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="false">' +
                                '<div class="toast-header">' +
                                '<img src="{{ asset('assets/login/psu.png') }}" class="rounded mr-2" alt="psu" height="25px" width="25px">' +
                                '<strong class="mx-3">Invitation</strong>' +
                                '<small>' + moment(value.created).fromNow() +
                                '</small>' +
                                '<button type="button" class="mx-2 btn btn-secondary float-end" data-bs-dismiss="toast" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                                '</button>' +
                                '</div>' +
                                '<div class="toast-body">' +
                                'Someone has invited you to a project, please check your invitations' +
                                '</div>' +
                                '</div>' +
                                '</div>');
                            $('.toast').toast('show');
                            return;
                        });
                    }
                }
            });
        }
    });

    function loadNotification() {
        let http = new XMLHttpRequest();
        http.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById("countNotification")
                    .innerHTML = parseInt(this.responseText) > 99 ? "99+" : this.responseText;
            }
        };
        http.open("GET", "/count/notification", true);
        http.send();
    }
</script>
