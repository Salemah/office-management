@push('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style>
        #client_table_filter,
        #client_table_paginate {
            float: right;
        }
        .dataTable {
            width: 100% !important;
            margin-bottom: 20px !important;
        }
        .table-responsive {
            /* overflow-x: true !important; */
        }
    </style>
@endpush
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0 " id="salary_table">
            <thead class="table-light fw-semibold dataTableHeader">
                <tr class="align-middle table">
                    <th>#</th>
                    <th>Date</th>
                    <th>Basic Salary</th>
                    <th>Gross Salary</th>
                    <th>Month</th>
                    <th>Status</th>
                   <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="salarySingleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Salary</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-show">

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="salaryEditSingleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
 aria-hidden="true">
<div class="modal-dialog  modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Salary edit Form</h5>
            <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body modal-edit">

        </div>
    </div>
</div>
</div>@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>

        $(document).ready(function() {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#salary_table').DataTable({
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
                    url: "{{ route('admin.salarylist', $employee->id) }}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex",name: "DT_RowIndex",orderable: false,searchable: false},
                    { data: 'date',name: 'date',orderable: true,searchable: true},
                    { data: 'basic_salary', name: 'basic_salary'},
                    { data: 'gross_salary', name: 'gross_salary'},
                    { data: 'salary_month', name: 'salary_month'},
                    {data: 'status', name: 'status'},
                    { data: 'action',name: 'action',orderable: false,searchable: false}
                ],
            });
        });

        // delete Confirm
        function salaryDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    SalaryDelete(id);
                }
            });
        };

        // Delete Button
        function SalaryDelete(id) {
            var url = '{{ route('admin.salary.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function(resp) {
                     console.log(resp);
                    // Reloade DataTable
                    $('#salary_table').DataTable().ajax.reload();
                     if (resp.success === true) {
                        // show toast message
                      toastr.success(resp.message);
                        //location.reload();
                    } else if (resp.errors) {
                         toastr.error(resp.errors[0]);
                     } else {
                       toastr.error(resp.message);
                    }
                }, // success end
                error: function(error) {
                    location.reload();
                } // Error
            })
        }



      $(document).on("click", ".mdlCreateBtn", function () {
            console.log($(this).attr('basic_salary'));
            console.log($(this).attr('gross_salary'));
            $('#salary_month').val($(this).attr('salary_month'));
            $('#allowance_salary').val($(this).attr('allowance_salary'));
            $('#salary_basics').val($(this).attr('basic_salary'));
            $('#gross_salaryEdit').val($(this).attr('gross_salary'));
            $('#descriptionEdit').val($(this).attr('description'));
            $('#salarEditId').val($(this).attr('salarEditId'));
        })

        $(document).ready(function(){
      $("#salary_basics").keyup(function(){
       $salary_basic =  $("#salary_basics").val();
       $allowance_salary =  $("#allowance_salary").val();
       $gross_salary = parseFloat($salary_basic)+parseFloat($allowance_salary);
       $("#gross_salaryEdit").val($gross_salary);
      });
    });

        // Status Change Confirm Alert
        function showStatusChangeAlert(id) {
            event.preventDefault();
            swal({
                title: `Are you sure?`,
                text: "You want to update the status?.",
                buttons: true,
                infoMode: true,
            }).then((willStatusChange) => {
                if (willStatusChange) {
                    statusChange(id);
                }
            });
        };

        // Status Change
        function statusChange(id) {
            var url = '{{ route("admin.salary.status.update",":id") }}';
            $.ajax({
                type: "POST",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#salary_table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        // show toast message
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
        function getSelectedSalaryData(id, type) {
            event.preventDefault();
            $.ajax({
                url: '{{ route('admin.salary-show') }}',
                type: "POST",
                data:{
                    id: id,
                    type: type,
                },
                success: function (resp) {
                    if(resp.type == 1){
                        $('.modal-edit').html(resp.data);

                    }else{
                        $('.modal-show').html(resp.data);
                    }

                },
                error: function () {
                   // location.reload();
                }
            });
        }
    </script>

@endpush
