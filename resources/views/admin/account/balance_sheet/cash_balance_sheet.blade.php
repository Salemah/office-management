@extends('layouts.dashboard.app')

@section('title', 'Cash Balance Sheet')

@push('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <style>
        #table_filter,
        #table_paginate {
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
                <a href="{{route('admin.account.cash.balance.sheet.index')}}">Cash Balance Sheet</a>
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
                            <thead class="table-light fw-semibold dataTableHeader">
                                <tr class="align-middle table">
                                    <th >SL#</th>
                                    <th >Bank Account</th>
                                    {{-- <th >Purpose</th> --}}
                                    <th >Debit</th>
                                    <th >Credit</th>
                                    <th >Balance</th>
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
            var dTable = $('#table').DataTable({
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
                // dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
                ajax: {
                    url: "{{ route('admin.account.cash.balance.sheet.index') }}",
                    type: "get"
                },
                columns: [
                    { data: "DT_RowIndex",name: "DT_RowIndex",orderable: false,searchable: false},
                    { data: 'bankinfo',name: 'bankinfo',orderable: true,searchable: true},
                    // { data: 'transaction_purpose',name: 'transaction_purpose',orderable: true,searchable: true},
                    { data: 'debit',name: 'debit', orderable: true,searchable: true},
                    { data: 'credit',name: 'credit',orderable: true,searchable: true },
                    { data: 'balance',name: 'balance',orderable: true,searchable: true},
                ],
            });
        });

    </script>
@endpush
