@extends('layouts.dashboard.app')

@section('title', 'Bank Account Transaction')

@push('css')
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
                <a href="#">Bank</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Account</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.account.bank-account.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')
<div class="row card">
    <div class="d-flex mt-3">
        <p>Account Info : <b>{{ $accountInfo->bankAccount->name }} || {{ $accountInfo->bankAccount->account_number }}</b></p>
        <p style="margin-left:50px">Total Debit : <b>{{ number_format($totalDebit) }}</b></p>
        <p style="margin-left:50px">Total Credit : <b>{{ number_format($totalCredit) }}</b></p>
        <p style="margin-left:50px">Current Balance : <b>{{ number_format($currentBalance) }}</b></p>
    </div>
</div>

    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table-responsive">
                        <table class="table border mb-0" id="table">
                            <thead class="table-light fw-semibold dataTableHeader">
                                <tr class="align-middle table">
                                    <th width="5%">SL#</th>
                                    <th width="10%">Date</th>
                                    <th width="15%">Narration</th>
                                    <th width="10%">Debit</th>
                                    <th width="10%">Credit</th>
                                    <th width="10%">Current Balance</th>
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
       $(document).ready(function() {
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
                    {data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false},
                    {data: 'date', name: 'date', orderable: true, searchable: true},
                    {data: 'purpose', name: 'purpose', orderable: true, searchable: true},
                    {data: 'debit_amount', name: 'debit_amount', orderable: true, searchable: true},
                    {data: 'credit_amount', name: 'credit_amount', orderable: true, searchable: true},
                    {data: 'current_balance', name: 'current_balance', orderable: true, searchable: true},

                ],
            });
        });
    </script>
@endpush
