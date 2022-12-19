@section('reports')
    <div class="container">
        @yield('section')
        <div class="row">
            <div class="col-sm-2 mt-3">
                <h1>Reports</h1>
            </div>
            <div class="col-sm-12 mt-3">
                <label class="mx-2">
                    <p>Filter By Project:</p>
                    <select class="form-select" id="projects">
                        <option selected disabled>Select Project</option>
                    </select>
                </label>
                <label class="mx-2">
                    <p>Filter By Name:</p>
                    <select id="name">
                        <option selected disabled>Select Employee Name</option>
                    </select>
                </label>
                <label class="mx-2">
                    <p>Generate Report By:</p>
                    <select name="report_type" id="report_type" class="form-select">
                        <option selected disabled>Select Report Type</option>
                        <option value="daily">Daily</option>
                        <option value="quarterly">Quarterly</option>
                    </select>
                </label>
                <label class="mx-2 daily">
                    <p>Filter By Date:</p>
                    <input type="date" class="form-control" id="date" name="date" disabled>
                </label>
                <label class="quarterly mx-2">
                    <p>Filter Quarterly:</p>
                    <select name="quarter" id="quarter" class="form-select" disabled>
                        <option selected disabled>Select Quarter</option>
                        <option value="quarter1">Q1 Jan 1 - March 31</option>
                        <option value="quarter2">Q2 April 1 - June 30</option>
                        <option value="quarter3">Q3 July 1 - September 30</option>
                        <option value="quarter4">Q4 October 1 - December 31</option>
                    </select>
                </label>

            </div>
            <div class="col-12 mt-2">
                <button class="btn btn-primary mx-2" id="download">DOWNLOAD REPORT</button>
                <button class="btn btn-secondary" onclick="clearFilter()">Clear</button>
            </div>
        </div>
        <hr>
        <table id="reportTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th class="d-none">ID</th>
                    <th>Project Name</th>
                    <th>Employee Name</th>
                    <th>Action</th>
                    <th>Role</th>
                    <th>Moved At</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@section('reports-scripts')
    <script>
        function clearFilter() {
            swal({
                title: 'Filter Cleared',
                text: 'All filter is cleared',
                type: 'success',
            }).then((result) => {
                if (result.value === true) {
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            });
            $('#reportTable').DataTable().clear().draw();
            $('#name').find('option').remove().end();
            $('#name').append("<option selected disabled>Select Employee Name</option>")
            $('#report_type').find('option').remove().end();
            $('#report_type').append("<option selected disabled>Select Report Type</option>")
            $('#report_type').append("<option value='daily'>Daily</option>")
            $('#report_type').append("<option selected='quarterly'>Quarterly</option>")
            $('#date').val('');
        }
        $(document).ready(function() {
            $('#reportTable').DataTable();

            fetchProject();

            function fetchProject() {
                $('#projects').val($('#projects option:first').val());
                $('#name').find('option').remove().end();
                $('#name').append("<option selected disabled>Select Employee Name</option>")
                $('#date').val('');
                $.ajax({
                    type: 'get',
                    url: '/admin/reports/fetch',
                    dataType: 'json',
                    success: function(data) {
                        $.each(data.projects, function(key, item) {
                            $('#projects').append("<option value='" + item.project_id + "'>" +
                                item.project_title + "</option>")
                        });
                    },
                    error: function() {

                    }
                });
            }

            function filterByNameAndDate() {
                var name = $('#name').val();
                var date = $('#date').val();
                var quarter = $('#quarter').val();
                $('#reportTable').DataTable().clear().draw();
                $.ajax({
                    type: 'get',
                    data: {
                        name: name,
                        date: date,
                        quarter: quarter
                    },
                    url: '/admin/reports/fetch/' + $('#projects option:selected').val(),
                    dataType: 'json',
                    success: function(data) {
                        $.each(data.project, function(key, item) {
                            let tableName = $('#reportTable').DataTable();
                            const tr = $('<tr><td class="d-none">' + item.report_id +
                                '</td><td>' + item.project_title + '</td><td>' + item.name +
                                '</td>' + '<td>' + item.username + item.message + '</td>' +
                                '<td>' + item.role.toUpperCase() + '</td>' + '<td>' + item
                                .report_date + '</td>');
                            tableName.row.add(tr[0]).draw();
                        });
                    },
                    error: function() {

                    }
                });
            }

            $('#projects').on('change', function() {
                $('#reportTable').DataTable().clear().draw();
                $.ajax({
                    type: 'get',
                    url: '/admin/reports/fetch/' + $('#projects option:selected').val(),
                    dataType: 'json',
                    success: function(data) {
                        $.each(data.project, function(key, item) {
                            let tableName = $('#reportTable').DataTable();
                            const tr = $('<tr><td class="d-none">' + item.report_id +
                                '</td><td>' + item.project_title + '</td><td>' +
                                item.name + '</td>' + '<td>' + item.username + item
                                .message + '</td>' + '<td>' + item.role
                                .toUpperCase() + '</td>' + '<td>' + item
                                .report_date + '</td>');
                            tableName.row.add(tr[0]).draw();
                        });
                    },
                    error: function() {

                    }
                });
                if ($('#projects').val() != null) {
                    $('#name').find('option').remove().end();
                    $('#name').append("<option selected disabled>Select Employee Name</option>")
                    $.ajax({
                        type: 'get',
                        url: '/admin/reports/fetch/project/' + $('#projects').val(),
                        dataType: 'json',
                        success: function(data) {
                            $.each(data.users, function(key, item) {
                                $('#name').append("<option value='" + item.id + "'>" +
                                    item.name + "</option>")
                            });
                        },
                        error: function() {

                        }
                    });
                }
            });
            $('#name').on('change', function() {
                $('#reportTable').DataTable().clear().draw();
                if ($('#projects').val() != null) {
                    filterByNameAndDate();
                } else {
                    swal(
                        'Select Project',
                        'Select project first before filter by name',
                        'error'
                    )
                }
            });
            $('#date').on('change', function() {
                $('#reportTable').DataTable().clear().draw();
                if ($('#projects').val() != null) {
                    filterByNameAndDate();
                } else {
                    swal(
                        'Select Project',
                        'Select project first before filter by date',
                        'error'
                    )
                }
            });

            $('#report_type').on('change', function(e) {
                e.preventDefault();
                $('#reportTable').DataTable().clear().draw();
                if ($(this).val() == "daily") {
                    $('#reportTable').DataTable().clear().draw();
                    $('#quarter').attr("disabled", true);
                    $("#quarter").val($("#quarter option:first").val());
                    $('#date').removeAttr("disabled");
                } else {
                    $("#quarter").val($("#quarter option:first").val());
                    $('#date').val('').attr('type', 'text').attr('type', 'date');
                    $('#date').attr("disabled", true);
                    $('#quarter').removeAttr("disabled");
                }
            });
            $('#date').change(function(e) {
                e.preventDefault();
                $('#reportTable').DataTable().clear().draw();
                if ($('#projects').val() != null) {
                    if (!$('#date').val()) {
                        filterByNameAndDate();
                        $('#date').val('').attr('type', 'text').attr('type', 'date');
                    }
                } else {
                    swal(
                        'Select Project',
                        'Select project first before filter by date',
                        'error'
                    ).then((value) => {
                        window.location.reload();
                    });
                }
            });
            $('#quarter').change(function(e) {
                e.preventDefault();
                $('#reportTable').DataTable().clear().draw();
                if ($('#projects').val() != null) {
                    if ($("#quarter option:selected").index() > 0) {
                        // fetch filtered report
                        filterByNameAndDate();
                    }
                } else {
                    swal(
                        'Select Project',
                        'Select project first before filter by date',
                        'error'
                    ).then((value) => {
                        window.location.reload();
                    });
                }
            });

            $('#download').on('click', function() {
                var name = $('#name').val() == null ? '' : $('#name').val();
                var date = $('#date').val() == null ? '' : $('#date').val();
                var quarter = $('#quarter').val() == null ? '' : $('#quarter').val();
                if ($('#projects').val() === null) {
                    swal(
                        'No reports',
                        'No reports to generate in the system',
                        'error'
                    )
                    return;
                }

                if ($('#reportTable').DataTable().rows().count() !== 0) {
                    $.ajax({
                        type: 'get',
                        url: '/admin/reports/generate-report/' + $('#projects').val(),
                        data: {
                            name: name,
                            date: date,
                            quarter: quarter
                        },
                        xhrFields: {
                            responseType: 'blob',
                        },
                        success: function(data) {
                            let blob = new Blob([data]);
                            let link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = "reports-" + new Date().valueOf() + ".pdf";
                            link.click();
                        },
                        error: function() {
                            swal(
                                'Something went wrong',
                                'An error occurred in generating of reports',
                                'error'
                            )
                        }
                    });
                } else {
                    swal(
                        'Unable to Generate',
                        'No data to generate!',
                        'error'
                    )
                }
            });
        });
    </script>
@endsection
