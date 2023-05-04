@push('css')
    <style>
        #module_table_filter,
        #module_table_paginate {
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
    <table class="table border mb-0" id="module_table">
        <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th >#</th>
                <th >Start Date</th>
                <th >End Date</th>
                <th >Estimate Hour </th>
                <th >Estimate Day </th>
                <th >Final Hour</th>
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
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <h3 class="text-center text-success ">Project Duration</h3>
            <span class="badge bg-success my-3 py-2">Total Spent Day : <b> <span class="text-dark" id="spent_day" ></span>  </b></span>
            <span class="badge bg-warning my-3 py-2">Total Due Day : <b> <span class="text-dark" id="due_day">{{$total_hour}}</span>  </b></span>
            <span class="badge bg-success my-3 py-2">Total Hour : <b> <span class="text-danger">{{$total_hour}}</span>  </b></span>
            <span class="badge bg-danger my-3 py-2">Total Hour : <b> <span class="text-lite">{{$total_hour}}</span>  </b></span>
    <table class="table table-bordered   table-hover  ">
        <thead class="table-primary ">
          <tr>
            <th scope="col">Type</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Estimate Day</th>
            <th scope="col">Estimate Hour</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($projects as $key=>$projectt)
            <tr>
                <td>@if($key == 0) <p class="text-primary">Initial</p> @else <p class="text-danger"> Extend  </p> @endif </td>
                <td>{{$projectt->start_date}} </td>
                <td>{{$projectt->end_date}}</td>
                <td>{{$projectt->total_day}}</td>
                <td>{{$projectt->total_hour}}</td>
              </tr>
            @endforeach
               <tr>
                <td colspan="4" style="text-align: right;font-weight:bold"> Total Project Day:{{$total_day}}</td>
                <td colspan="1" style="text-align: right;font-weight:bold"> Total Project Hour :{{$total_hour}}</td>
              </tr>
        </tbody>
    </table>

    <h5>Module Report</h5>
    <table class="table table-bordered   table-hover  ">
        <thead class="table-success ">
          <tr>
            <th scope="col">Module Name</th>
            <th scope="col">Start Date</th>
            <th scope="col">End Date</th>
            <th scope="col">Status</th>
            <th scope="col">Estimate Day</th>
            <th scope="col">Estimate Hour</th>
            <th scope="col">Total Hour</th>
            <th scope="col" style="background: green;color:white">Spent Day</th>
            <th scope="col" style="background: red">Due Day</th>
          </tr>
        </thead>
        <tbody>

           @foreach ($projectModules  as $key=>$projectModule)
           <tr >
                <td>{{$projectModule->module_name}}</td>
                <td>{{$projectModule->module_start_date}}</td>
                <td>{{$projectModule->module_end_date}}</td>
                <td>@if ($projectModule->status ==1)
                    Up Coming
                    @elseif ($projectModule->status == 2)
                    On Going
                    @elseif ($projectModule->status == 3)
                    Complete
                    @elseif ($projectModule->status == 4)
                    Cancel
                    @elseif ($projectModule->status == 5)
                    On Hold
                    @endif
                </td>
                <td>{{$projectModule->module_total_day }}</td>
                <td>{{$projectModule->module_estimate_hour}}</td>
                <td>{{$projectModule->module_final_hour}}</td>
                <td>{{$projectModule->module_final_hour}}</td>
                <td>{{$projectModule->module_final_hour}}</td>
            </tr>

         @endforeach
            <tr>
                <td colspan="4" style="text-align: right;font-weight:bold"> Total Estmate Day : {{$module_day}}</td>
                <td colspan="2" style="text-align: right;font-weight:bold">Total Estmate Hour :{{$module_hour}}</td>
            </tr>
        </tbody>
    </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
</div>

<!-- Button trigger modal -->
<!-- Modal -->
{{-- <div class="modal fade" id="editModuleModal" data-coreui-backdrop="static" data-coreui-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Module Edit</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="add-client-document" enctype="multipart/form-data"
                      action="{{ route('admin.project.module.project-update') }}"
                      method="POST">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="project_id" value="{{$project->id}}">
                        <input type="hidden" name="edit_project_module_id" id="edit_project_module_id" value="">
                        <input type="hidden" name="project_duration_id" id="edit_project_duration_id" value="">
                        <input type="hidden" name="project_start_date" id="edit_project_start_date" value="">
                        <input type="hidden" name="project_end_date" id="edit_project_end_date" value="">

                        <div class="form-group col-12 col-sm-12 col-md-12 mb-2 module_duration">
                            <label for="module_name"><b>Module Name</b><span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('module_name') is-invalid @enderror"
                                   name="module_name" id="edit_module_name" placeholder="Enter Module Name">
                            @error('module_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_start_date"> <b>Module Start Date </b><span class="text-danger">*</span></label>
                            <input type="date" id="edit_module_start_date" value="{{ old('module_start_date')}}"class="form-control " name="module_start_date" placeholder="Enter Start Date"onchange="editModuleCheckDate()">
                            @error('module_start_date')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_end_date"><b>Module End Date</b><span class="text-danger">*</span></label>
                            <input type="date" id="edit_module_end_date" value="{{ old('module_end_date')}}" class="form-control " name="module_end_date" placeholder="Enter End Date" onchange="editModuleCheckDate()">
                            @error('module_end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <input type="hidden" id="edit_module_estimate_day" value="{{ old('module_estimate_day')}}"class="form-control " name="module_estimate_day" placeholder="Enter Estimate Day"onchange="editModuleCheckDate()" readonly>
                            <label for="status"><b>Status</b><span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="">--Select Status--</option>
                                <option value="1" selected>Up Coming</option>
                                <option value="2">On Going</option>
                                <option value="3">Complete</option>
                                <option value="4">Cancel</option>
                                <option value="5">On Hold</option>
                            </select>
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
                            <label for="estimated_hour_day"><b>Estimated Hour Per Day</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('estimated_hour_day') is-invalid @enderror" name="estimated_hour_day" id="edit_estimated_hour_day" placeholder="0.0" onkeyup="getTotalHourModule()">
                            @error('estimated_hour_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">
                            <label for="edit_module_vacation_day"><b>Vacation Day<</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('edit_module_vacation_day') is-invalid @enderror" name="edit_module_vacation_day" id="edit_module_vacation_day" placeholder="0.0" onkeyup="editModulecalculateDay()">
                            @error('edit_module_vacation_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_total_day"><b>Module Total Day</b><span
                                    class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('module_total_day') is-invalid @enderror" name="module_total_day" id="edit_module_total_day" placeholder="0.0" readonly>
                            @error('module_total_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">

                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="module_total_hour"><b>Module Total Hour</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('module_total_hour') is-invalid @enderror" name="module_total_hour" id="edit_module_total_hour" placeholder="0.0" readonly>
                            @error('module_total_hour')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 "  >

                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 module_duration">
                            <label for="edit_module_final_day"><b>Total Day</b><span class="text-danger">*</span></label>
                            <input type="num" class="form-control @error('edit_final_day') is-invalid @enderror" name="edit_module_final_day" id="edit_module_final_day" placeholder="0.0" readonly>
                            @error('edit_module_final_day')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group col-12 col-sm-12 col-md-6 mb-2 ">

                        </div>
                        <div class="col-12 mb-2">
                            <div class="form-group">
                                <label><b>Adjustment</b></label>
                                <input class="form-check-input " type="checkbox" id="edit_module_adjustment-btn"   >
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-group adjustment" style="display: none">
                                <label><b>Adjustment Type</b></label>
                                <select class="form-control" id="edit_module_adjustment_type" name="adjustment_type" onchange="adjustmentHourCountModule()">
                                    <option value="" selected>--Select--</option>
                                    <option value="1">Addition</option>
                                    <option value="2">Subtraction</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-3 mb-2">
                            <div class="form-group adjustment" style="display: none">
                                <label><b>Adjustment hour</b></label>
                                <input type="number" name="adjustment_hour" id="edit_module_adjustment_hour" class="form-control " placeholder="0" onkeyup="adjustmentHourCountModule()">
                                @error('adjustment_hour')
                                    <span class="text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 mb-2">
                            <div class="form-group">
                                <label for="final_hour"><b>Final Hour</b><span class="text-danger">*</span></label>
                                <input type="num" class="form-control @error('final_hour') is-invalid @enderror" name="final_hour" id="edit_module_final_hour" placeholder="0.0" readonly>
                                @error('final_hour')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group my-3">
                            <button type="submit" class="btn btn-sm btn-primary mb-3 ">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="edit-module-submit" class="btn btn-secondary" data-coreui-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}
@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).on("click", ".mdlEditBtn", function () {
                $('#edit_module_name').val($(this).attr('module_name'));
                $('#edit_project_module_id').val($(this).attr('project_module_id'));
                $('#edit_estimated_hour_day').val($(this).attr('estimated_hour_day'));
                $('#edit_module_total_day').val($(this).attr('module_total_day'));
                $('#edit_module_total_hour').val($(this).attr('module_total_hour'));
                $('#edit_module_final_hour').val($(this).attr('module_final_hour'));
                $('#edit_project_duration_id').val($(this).attr('project_duration_id'));
                $('#edit_module_start_date').val($(this).attr('module_start_date'));
                $('#edit_module_end_date').val($(this).attr('module_end_date'));
                $('#edit_project_start_date').val($(this).attr('project_start_date'));
                $('#edit_project_end_date').val($(this).attr('project_end_date'));
                $('#edit_module_vacation_day').val($(this).attr('vacation_day'));
                $('#edit_module_final_day').val($(this).attr('final_day'));
        });
        function editModuleCheckDate() {
            var date1 = new Date($('#edit_module_start_date').val());
            var date2 = new Date($('#edit_module_end_date').val());
            var date3 = new Date($('#edit_project_start_date').val());
            var date4 = new Date($('#edit_project_end_date').val());
            if (date1 < date3) {
                $('#edit_module_start_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date1 + "  Less Than " + date3,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#edit_module_start_date').val('');
                    }
                });
            } else if (date2 > date4) {
                $('#edit_module_end_date').val('');
                $('#edit_module_estimate_day').val('');
                $('#module_estimate_hour').val('');
                $('#edit_module_final_hour').val('');
                $('#edit_module_total_hour').val('');
                $('#edit_estimated_hour_day').val('');
                $('#edit_module_total_day').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date2 + "Greater Than " + date4,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#edit_module_end_date').val('');
                        $('#edit_module_estimate_day').val('');
                        $('#module_estimate_hour').val('');
                    }
                });
            } else if (date2 < date1) {
                $('#edit_module_end_date').val('');
                swal({
                    title: `Please Select Correct Date`,
                    text: date2 + "  Less Than " + date1,
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $('#edit_module_end_date').val('');
                    }
                });
            } else {

                editModulecalculateDay();
            }

        }

        function editModulecalculateDay(id) {
            var vacationDay = $('#edit_module_vacation_day').val();
            var date1 = new Date($('#edit_module_start_date').val());
            var date2 = new Date($('#edit_module_end_date').val());
            var diffDays = parseInt((date2 - date1) / (1000 * 60 * 60 * 24), 10) + 1;
            if(diffDays<vacationDay){
                    $('#edit_module_vacation_day').val('');
                    $('#edit_module_final_day').val(diffDays);
                    swal({
                        title: `Please Write Valid Vacation`,
                        text: `Vacation Extend Total Project Day`,
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $('#edit_module_vacation_day').val('');
                        }
                    });
                    getTotalHourModule();
                }
                else{
                    $('#edit_module_final_day').val(diffDays - vacationDay);
                    $('#edit_module_estimate_day').val(diffDays);
                    $('#edit_module_total_day').val(diffDays);
                    $('#module_estimate_hour').val(diffDays - vacationDay * 8);
                    getTotalHourModule();
                }
        };
        $(document).on("click", "#edit_module_adjustment-btn", function () {
            if ($('#edit_module_adjustment-btn').is(":checked"))
                $(".adjustment").show();
            else
                $(".adjustment").hide();
                $('#edit_module_adjustment_hour').val(0);
                getTotalHourModule();
        });

        function getTotalHourModule() {
            var totalDay = $("#edit_module_final_day").val();
            var dayHour = $("#edit_estimated_hour_day").val();
            $('#edit_module_total_hour').val(totalDay * dayHour);
            $('#edit_module_final_hour').val(totalDay * dayHour);
            $('#edit_module_adjustment_hour').val();
            adjustmentHourCountModule();
        };

        function adjustmentHourCountModule() {
            var adjustmentType = $('#edit_module_adjustment_type').val();
            var adjustmentHour = $('#edit_module_adjustment_hour').val();
            var dayHour = $("#edit_estimated_hour_day").val();
            var totaltHour = $("#edit_module_final_day").val();
            if (adjustmentType == 1) {
                if (adjustmentHour) {
                    var finalHour = (parseFloat(totaltHour) * dayHour) + parseFloat(adjustmentHour);
                    $("#edit_module_final_hour").val(finalHour);
                }
            } else if (adjustmentType == 2) {
                if (adjustmentHour) {
                    var finalBalance = (parseFloat(totaltHour) * dayHour) - parseFloat(adjustmentHour)
                    $("#edit_module_final_hour").val(finalBalance);
                }
            }
        }
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#module_table').DataTable({
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
                    {data: 'module_start_date', name: 'module_start_date', orderable: true, searchable: true},
                    {data: 'module_end_date', name: 'module_end_date', orderable: true, searchable: true},
                    {data: 'module_estimate_hour', name: 'module_estimate_hour', orderable: true, searchable: true},
                    {data: 'final_day', name: 'final_day', orderable: true, searchable: true},
                    {data: 'module_final_hour', name: 'module_final_hour', orderable: true, searchable: true},
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
            var url = '{{ route('admin.project.module.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#module_table').DataTable().ajax.reload();
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
    </script>
@endpush
