@push('css')
    <style>
        #reporting_table_filter,
        #reporting_table_paginate {
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
<input type="hidden" name="project_id" value="{{$project->id}}">
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="reporting_table">
            <thead class="table-light fw-semibold">
                <tr class="align-middle table">
                    <th>#</th>
                    <th>assignTo</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#reporting_table').DataTable({
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
                    url: "{{route('admin.project.reporting.show',$project->id)}}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex",name: "DT_RowIndex",orderable: false,searchable: false},
                    { data: "assignTo",name: "assignTo",orderable: false,searchable: false},
                    { data: 'action',name: 'action',orderable: false,searchable: false}
                ],
            });
        });
        // delete Confirm
        function reportingDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    reportingDelete(id);
                }
            });
        };

        // Delete Button
        function reportingDelete(id) {
            var projectId = $('#project_id').val();
            var url = '{{ route('admin.project.reporting.delete',':id') }}';
            $.ajax({
                type: "get",
                url: url.replace(':id', id),
                data : {projectId : projectId},
                success: function(resp) {
                    console.log(resp);
                    $('#reporting_table').DataTable().ajax.reload();
                     if (resp.success === true) {
                    //     // show toast message
                      toastr.success(resp.message);
                        //location.reload();
                    } else if (resp.error) {
                         toastr.error(error);
                     } else {
                       toastr.error(resp.error);
                    }
                }, // success end
                error: function(error) {
                    console.log(error);
                   //location.reload();
                } // Error
            })
        }
    </script>
@endpush
