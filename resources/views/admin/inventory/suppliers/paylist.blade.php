@extends('layouts.dashboard.app')

@section('title', 'Supplier Pay List')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Supplier Pay List</a>
            </li>
        </ol>
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
                            <thead class="table-light fw-semibold">
                            <tr class="align-middle table">
                                <th>#</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Invoice No</th>
                                <th>Grand Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                            </thead>
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

    <script>
        $(document).ready(function () {
            var url = $(this).attr('href');
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
                    url: url,
                    type: "get"
                },
                columns: [
                    {data: "DT_RowIndex",name: "DT_RowIndex", orderable: false,  searchable: false},
                    {data: 'date',name: 'date', orderable: true,   searchable: true},
                    {data: 'reference_no', name: 'reference_no', orderable: true,   searchable: true},
                    {data: 'invoice_no',name: 'invoice_no', orderable: true,   searchable: true},
                    {data: 'grand_total',name: 'grand_total',orderable: true,   searchable: true},
                    {data: 'paid',name: 'paid', orderable: true,   searchable: true},
                    {data: 'due',name: 'due', orderable: true,   searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });

    </script>
@endpush
