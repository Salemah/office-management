@push('css')
    <style>
        #loan_table_filter,
        #loan_table_paginate {
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
<div class="table-responsive">
    <div class="table-responsive">
        <table class="table border mb-0" id="loan_table">
            <thead class="table-light fw-semibold dataTableHeader">
                <tr class="align-middle table">
                    <th>#</th>
                    <th>Date</th>
                    <th>Loan Title</th>
                    <th>Loan Type</th>
                    <th>Transaction Type</th>
                    <th>Loan Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@push('script')
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#loan_table').DataTable({
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
                    url: "{{ route('admin.loan.list', $authority->id) }}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    { data: 'loan_date', name: 'loan_date', orderable: true, searchable: true},
                    { data: 'loan_title', name: 'loan_title', orderable: true, searchable: true},
                    { data: 'loan_type', name: 'loan_type', orderable: true, searchable: true},
                    { data: 'transaction_way', name: 'transaction_way', orderable: true, searchable: true},
                    { data: 'loan_amount', name: 'loan_amount', orderable: true, searchable: true},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });
        // delete Confirm
        function loanDeleteConfirm(id) {
            event.preventDefault();
            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    loanDelete(id);
                }
            });
        };

        // Delete Button
        function loanDelete(id) {
            var url = '{{ route('admin.loan.destroy', ':id') }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function(resp) {
                     console.log(resp);
                    // // Reloade DataTable
                    $('#loan_table').DataTable().ajax.reload();
                     if (resp.success === true) {
                    //     // show toast message
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
    </script>
@endpush
