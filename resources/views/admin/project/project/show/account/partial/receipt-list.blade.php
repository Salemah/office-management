@push('css')
    <style>
        #receive_table_filter,
        #receive_table_paginate {
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
    <span class="badge rounded-pill bg-success fs-6"><span class="">Due Amount:</span> {{number_format($total_due,2)}}</span>
    <div class="table-responsive">
        <table class="table border mb-0" id="receive_table">
            <thead class="table-light fw-semibold dataTableHeader">
            <tr class="align-middle table">
                <th width="1%">#</th>
                <th width="10%">Date</th>
                <th width="60%">Details</th>
                <th width="10%">Amount</th>
                <th width="11%">Received By</th>
                <th width="8%">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="allowanceEditSingleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
 aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Download Document</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-edit">

            </div>
        </div>
    </div>
</div>

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.20.0/ckeditor.js"integrity="sha512-BcYkQlDTKkWL0Unn6RhsIyd2TMm3CcaPf0Aw1vsV28Dj4tpodobCPiriytfnnndBmiqnbpi2EelwYHHATr04Kg=="crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>

        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#receive_table').DataTable({
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
                    url: "{{route('admin.project.budget-receipt.show',$project->id)}}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    { data: 'time', name: 'time', orderable: true, searchable: true},
                    { data: 'details', name: 'details', orderable: true, searchable: true},
                    { data: 'amount', name: 'amount', orderable: true, searchable: true,class:'text-end'},
                    { data: 'createdBy', name: 'createdBy', orderable: true, searchable: true},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

        // delete Confirm
        function showReceiveDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    receiveDelete(id);
                }
            });
        };

        // Download Document
        function getSelectedAllowanceData(id) {
            event.preventDefault();
            $.ajax({
                url: '{{ route('admin.project.budget-receipt.document') }}',
                type: "POST",
                data:{
                    id: id,
                },
                success: function (resp) {
                    console.log(resp.data)
                        $('.modal-edit').html(resp.data);
                },
                error: function () {
                   // location.reload();
                }
            });
        };

        // Delete Button
        function receiveDelete(id) {
            var url = '{{ route('admin.project.budget-receipt.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    $('#receive_table').DataTable().ajax.reload();
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
