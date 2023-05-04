@extends('layouts.dashboard.app')

@section('title', 'Loan Status')

@push('css')
    <style>
        #table_filter, #table_paginate {
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

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Loan Status</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.loan.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table border mb-0" id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                                <tr class="align-middle table">
                                    <th width="1%">#</th>
                                    <th width="12%">Date</th>
                                    <th width="40%">Loan Title</th>
                                    <th width="15%">Loan Author</th>
                                    <th width="5%">Loan Type</th>
                                    <th width="8%">Loan Amount</th>
                                    <th width="8%">Return Amount</th>
                                    <th width="8%">Due Amount</th>
                                    <th width="8%">Action</th>
                                </tr>
                            </thead>
                            <tfoot class="tfoot active">
                                    <th></th>
                                    <th>Page Total</th>
                                    <!-- <th></th> -->
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>

                                </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- sweetalert -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script>
        $(document).ready(function () {
            var searchable = [];
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                }
            });
            var dTable = $('#table').DataTable({
                order: [],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{route('admin.loan.list.status')}}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    { data: 'loan_date', name: 'loan_date', orderable: true, searchable: true},
                    { data: 'loan_title', name: 'loan_title', orderable: true, searchable: true},
                    { data: 'author', name: 'author', orderable: true, searchable: true},
                    { data: 'loan_type', name: 'loan_type', orderable: true, searchable: true},
                    { data: 'loan_amount', name: 'loan_amount', orderable: true, searchable: true,class:"text-end"},
                    { data: 'return_amount', name: 'return_amount', orderable: true, searchable: true,class:"text-end"},
                    { data: 'due_amount', name: 'due_amount', orderable: true, searchable: true,class:"text-end"},
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                footerCallback: function (row, data, start, end, display) {
                        var api = this.api();

                        // Remove the formatting to get integer data for summation
                        var intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };

                        // Total over all pages
                        total = api
                            .column(6)
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // Total over this page
                        pageTotal = api
                            .column(5, { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                            balance = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'BDT' }).format(pageTotal);
                        pageTotalReturn = api
                            .column(6, { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                            balanceReturn = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'BDT' }).format(pageTotalReturn);
                        pageTotalDue = api
                            .column(7, { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
                            balanceDue = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'BDT' }).format(pageTotalDue);
                        // Update footer
                        $(api.column(5).footer()).html( balance);
                        $(api.column(6).footer()).html( balanceReturn);
                        $(api.column(7).footer()).html( balanceDue);
                    },
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
                    loandeleteItem(id);
                }
            });
        };

        // Delete Button
        function loandeleteItem(id) {
            var url = '{{ route("admin.loan.destroy",":id") }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                success: function (resp) {
                    console.log(resp);
                    // Reloade DataTable
                    $('#table').DataTable().ajax.reload();
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

    </script>
@endpush
