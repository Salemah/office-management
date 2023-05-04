@push('css')
    <style>
        #module_list_table_filter,
        #module_list_table_paginate {
            float: right;
        }
        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table-responsive {
            overflow-x: hidden !important;
        }
    </style>
@endpush


<button type="button" class="btn btn-primary" data-coreui-toggle="modal" data-coreui-target="#exampleModal">
    Launch demo modal
</button>

<div class="table-responsive">
    <table class="table border mb-0" id="module_list_table">
        <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th >#</th>
                <th >Module Name</th>
                <th >Staus</th>
                <th >Added By</th>
                <th >Action</th>
            </tr>
        </thead>
    </table>
</div>

      <!-- Modal -->
 <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Module Hold History</h5>
          <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body modal-edit">

        </div>
      </div>
    </div>
</div>


@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        // function editModuleCheckDate() {
        //     var date1 = new Date($('#edit_module_start_date').val());
        //     var date2 = new Date($('#edit_module_end_date').val());
        //     var date3 = new Date($('#edit_project_start_date').val());
        //     var date4 = new Date($('#edit_project_end_date').val());
        //     if (date1 < date3) {
        //         $('#edit_module_start_date').val('');
        //         swal({
        //             title: `Please Select Correct Date`,
        //             text: date1 + "  Less Than " + date3,
        //             buttons: true,
        //             dangerMode: true,
        //         }).then((willDelete) => {
        //             if (willDelete) {
        //                 $('#edit_module_start_date').val('');
        //             }
        //         });
        //     } else if (date2 > date4) {
        //         $('#edit_module_end_date').val('');
        //         $('#edit_module_estimate_day').val('');
        //         $('#module_estimate_hour').val('');
        //         $('#edit_module_final_hour').val('');
        //         $('#edit_module_total_hour').val('');
        //         $('#edit_estimated_hour_day').val('');
        //         $('#edit_module_total_day').val('');
        //         swal({
        //             title: `Please Select Correct Date`,
        //             text: date2 + "Greater Than " + date4,
        //             buttons: true,
        //             dangerMode: true,
        //         }).then((willDelete) => {
        //             if (willDelete) {
        //                 $('#edit_module_end_date').val('');
        //                 $('#edit_module_estimate_day').val('');
        //                 $('#module_estimate_hour').val('');
        //             }
        //         });
        //     } else if (date2 < date1) {
        //         $('#edit_module_end_date').val('');
        //         swal({
        //             title: `Please Select Correct Date`,
        //             text: date2 + "  Less Than " + date1,
        //             buttons: true,
        //             dangerMode: true,
        //         }).then((willDelete) => {
        //             if (willDelete) {
        //                 $('#edit_module_end_date').val('');
        //             }
        //         });
        //     } else {

        //         editModulecalculateDay();
        //     }

        // }

        // function editModulecalculateDay(id) {
        //     var vacationDay = $('#edit_module_vacation_day').val();
        //     var date1 = new Date($('#edit_module_start_date').val());
        //     var date2 = new Date($('#edit_module_end_date').val());
        //     var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) + 1;
        //     if(diffDays<vacationDay){
        //             $('#edit_module_vacation_day').val('');
        //             $('#edit_module_final_day').val(diffDays);
        //             swal({
        //                 title: `Please Write Valid Vacation`,
        //                 text: `Vacation Extend Total Project Day`,
        //                 buttons: true,
        //                 dangerMode: true,
        //             }).then((willDelete) => {
        //                 if (willDelete) {
        //                     $('#edit_module_vacation_day').val('');
        //                 }
        //             });
        //             getTotalHourModule();
        //         }
        //         else{
        //             $('#edit_module_final_day').val(diffDays - vacationDay);
        //             $('#edit_module_estimate_day').val(diffDays);
        //             $('#edit_module_total_day').val(diffDays);
        //             $('#module_estimate_hour').val(diffDays - vacationDay * 8);
        //             getTotalHourModule();
        //         }
        // };
        // $(document).on("click", "#edit_module_adjustment-btn", function () {
        //     if ($('#edit_module_adjustment-btn').is(":checked"))
        //         $(".adjustment").show();
        //     else
        //         $(".adjustment").hide();
        //         $('#edit_module_adjustment_hour').val(0);
        //         getTotalHourModule();
        // });

        // function getTotalHourModule() {
        //     var totalDay = $("#edit_module_final_day").val();
        //     var dayHour = $("#edit_estimated_hour_day").val();
        //     $('#edit_module_total_hour').val(totalDay * dayHour);
        //     $('#edit_module_final_hour').val(totalDay * dayHour);
        //     $('#edit_module_adjustment_hour').val();
        //     adjustmentHourCountModule();
        // };

        // function adjustmentHourCountModule() {
        //     var adjustmentType = $('#edit_module_adjustment_type').val();
        //     var adjustmentHour = $('#edit_module_adjustment_hour').val();
        //     var dayHour = $("#edit_estimated_hour_day").val();
        //     var totaltHour = $("#edit_module_final_day").val();
        //     if (adjustmentType == 1) {
        //         if (adjustmentHour) {
        //             var finalHour = (parseFloat(totaltHour) * dayHour) + parseFloat(adjustmentHour);
        //             $("#edit_module_final_hour").val(finalHour);
        //         }
        //     } else if (adjustmentType == 2) {
        //         if (adjustmentHour) {
        //             var finalBalance = (parseFloat(totaltHour) * dayHour) - parseFloat(adjustmentHour)
        //             $("#edit_module_final_hour").val(finalBalance);
        //         }
        //     }
        // }
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#module_list_table').DataTable({
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
                    url: "{{ route('admin.project.module.show',$project->id)}}",
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'project_title', name: 'project_title', orderable: true, searchable: true},
                    {data: 'status', name: 'status', orderable: true, searchable: true},
                    {data: 'createdBy', name: 'createdBy', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function showHoldConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to hold this module?`,
                text: "If you hold this, it will be gone hold list.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    durationHold(id);
                }
            });
        };

        // // Delete Button
        function durationHold(id) {
            var url = '{{ route('admin.project.module.hold',':id') }}';
            $.ajax({
                type: "get",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#module_list_table').DataTable().ajax.reload();
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
        // $(document).on("submit", "#edit-module-submit", function () {
        //     $('#your_form').attr('action', 'http://uri-for-button1.com');
        // });
        function getSelectedLeaveData(id) {
            event.preventDefault();
            var url = '{{ route('admin.project.module.hold.list',':id') }}';
            $.ajax({
                url: url.replace(':id', id),
                type: "GET",
                success: function (resp) {
                    // if(resp.type == 1){
                      $('.modal-edit').html(resp.data);

                    // }else{
                    //     $('.modal-show').html(resp.data);
                    // }

                },
                error: function () {
                   // location.reload();
                }
            });
        }
    </script>
@endpush
