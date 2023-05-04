@push('css')
    <style>
        #project_duration_table_filter,
        #project_duration_table_paginate {
            float: right;
        }

        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }

        .table-responsive {
            overflow-x: hidden !important;
        }
        .card{
            border: none;
        }
    </style>
@endpush

<div class="table-responsive">
    <div class="table-responsive">
        {{-- <span class="badge bg-primary">Total Hour : <b> <span class="text-warning">{{$total_hour}}</span>  </b></span>
        <span class="badge bg-success">Total Hour : <b> <span class="text-danger">{{$total_hour}}</span>  </b></span>
        <span class="badge bg-danger">Total Hour : <b> <span class="text-lite">{{$total_hour}}</span>  </b></span>
        <span class="badge bg-warning text-dark">Total Hour : <b> <span lass="text-dark">{{$total_hour}}</span>  </b></span> --}}
        <table class="table border mb-0" id="project_duration_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th>#</th>
                <th>Type</th>
                <th>Status</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Estimate Day</th>
                <th>Final Hour</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="durationViewModal" data-coreui-backdrop="static" data-coreui-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Project Details</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead class="bg-primary text-light">
                      <tr>
                        <th scope="col" class="text-light">#</th>
                        <th scope="col" class="text-light">Project Name</th>
                        <th scope="col" class="text-light">Start Date</th>
                        <th scope="col" class="text-light">End Date</th>.
                        <th scope="col" class="text-light">Project Type</th>
                        <th scope="col" class="text-light">Project Priority</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td id="module-id"></td>
                        <td id="module-name"></td>
                        <td id="project-start-date"></td>
                        <td id="project-end-date"></td>
                        <td id="project-type"></td>
                        <td id="project-priority"></td>
                        <td id="project-total-module"></td>
                      </tr>
                    </tbody>
                  </table>

                    <div class="row align-items-start">
                        <div class="col-12 col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Description :</h5>
                                   <div id="project-description"></div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        $(document).on("click", ".mdlCreateBtn", function () {
            $('#project_duration_id').val($(this).attr('project_duration_id'));
            $('#project_start_date').val($(this).attr('start_date'));
            $('#project_end_date').val($(this).attr('end_date'));

            $('#moudle-ppp-name').html($(this).attr('project_priority'));
            $('#module-id').html($(this).attr('project_duration_id'));
            $('#module-name').html($(this).attr('project_title'));
            $('#project-start-date').html($(this).attr('start_date'));
            $('#project-end-date').html($(this).attr('end_date'));
            $('#project-type').html($(this).attr('project_type'));
            $('#project-priority').html($(this).attr('project_priority'));
            let aa = $(this).attr('project-description');
            $('#project-description').html(aa);

        })

        function moduleCheckDate() {
            var date1 = new Date($('#module_start_date').val());
            var date2 = new Date($('#module_end_date').val());
            var date3 = new Date($('#project_start_date').val());
            var date4 = new Date($('#project_end_date').val());
            if (date1 < date3) {
                $('#module_start_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date1 + "  Less Than " + date3,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_start_date').val('');
                    }
                });
            } else if (date2 > date4) {
                $('#module_end_date').val('');
                $('#module_estimate_day').val('');
                $('#module_estimate_hour').val('');
                $('#final_hour').val('');
                $('#module_total_hour').val('');
                $('#estimated_hour_day').val('');
                $('#module_total_day').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date2 + "Greater Than " + date4,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_end_date').val('');
                        $('#module_estimate_day').val('');
                        $('#module_estimate_hour').val('');
                    }
                });
            } else if (date2 < date1) {
                $('#module_end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date2 + "  Less Than " + date1,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#module_end_date').val('');
                    }
                });
            } else {
                modulecalculateDay();
            }

        }

        function modulecalculateDay(id) {
            var vacationDay = $('#module_vacation_day').val();
            var date1 = new Date($('#module_start_date').val());
            var date2 = new Date($('#module_end_date').val());
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) + 1;
            if(diffDays<vacationDay){
                    $('#module_vacation_day').val('');
                    $('#module_final_day').val(diffDays);

                    swal({
                        title: `Please Write Valid Vacation`,
                        text: `Vacation Extend Total Project Day`,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#module_vacation_day').val('');
                        }
                    });
                    getTotalHour();
                }
                else{
                    $('#module_estimate_day').val(diffDays);
                    $('#module_final_day').val(diffDays - vacationDay);
                    $('#module_total_day').val(diffDays);
                    $('#module_estimate_hour').val(diffDays - vacationDay* 8);
                    getTotalHour();
                }



        }

        function getTotalHour() {
            var totalDay = $("#module_final_day").val();
            var dayHour = $("#estimated_hour_day").val();

            $('#estimated_hour').val(totalDay * dayHour);
            $('#module_total_hour').val(totalDay * dayHour);
            $('#final_hour').val(totalDay * dayHour);
            $('#module_adjustment_hour').val(0);
            adjustmentHourCount();
        };

        function adjustmentHourCount() {
            var adjustmentType = $('#module_adjustment_type').val();
            var adjustmentHour = $('#module_adjustment_hour').val();
            var dayHour = $("#estimated_hour_day").val();
            var totaltHour = $("#module_final_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour) * dayHour) + parseFloat(adjustmentHour);
                    $("#final_hour").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour) * dayHour) - parseFloat(adjustmentHour)
                    $("#final_hour").val(finalBalance);
                }
            }
        }

        $(document).on("click", "#module_adjustment-btn", function () {
            if ($('#module_adjustment-btn').is(":checked"))
                $(".adjustment").show();
            else
                $(".adjustment").hide();
            $('#module_adjustment_hour').val(0);
            getTotalHour();
        });

        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#project_duration_table').DataTable({
                order: [],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                processing: true,
                responsive: false,
                serverSide: true,
                language: {
                    processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
                },
                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                ajax: {
                    url: "{{route('admin.project.duration.show',$project->id)}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'type', name: 'type', orderable: true, searchable: true},
                    {data: 'status', name: 'status', orderable: true, searchable: true},
                    {data: 'start_date', name: 'start_date', orderable: true, searchable: true},
                    {data: 'end_date', name: 'end_date', orderable: true, searchable: true},
                    {data: 'total_day', name: 'total_day', orderable: true, searchable: true},
                    {data: 'total_hour', name: 'total_hour', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function showDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    durationDelete(id);
                }
            });
        };

        // Delete Button
        function durationDelete(id) {
            var url = '{{ route('admin.project.duration.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#project_duration_table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        toastr.success(resp.message);
                    } else if (resp.errors) {
                        toastr.error(resp.errors[0]);
                    } else {
                        toastr.error(resp.message);
                    }
                }, // success end
                error: function (error) {
                    location.reload();
                } // Error
            })
        }


    </script>
@endpush
