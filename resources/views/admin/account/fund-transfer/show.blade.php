@extends('layouts.dashboard.app')
@section('title', 'Fund-Transfer Details')
@push('css')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
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
                <a href="{{ route('admin.account.fund-transfer.index') }}">Fund-Transfer</a>
            </li>
        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.account.fund-transfer.create') }}">
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
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="text-left text-white">
                                    <td colspan="2"><b>Fund-Transfer Details</b></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="50%">Date</td>
                                    <td width="50%" style="word-break: break-word;">{{ date('d F, Y
                                        ', strtotime($data->date)) }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Account From</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->cashOutBankAccount->name?? '--' }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">WareHouse</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->warehouses->name?? '--' }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Account To</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->cashInBankAccount->name ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Amount</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->amount ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Fund Transfer Title</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->balanceTransfer->transaction_title ?? '--' }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Fund Transfered By</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->createdBy->name  }}</td>
                                </tr>
                                <tr>
                                    <td width="50%">Cheque Number</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->balanceTransfer->cheque_number ?? '--' }}</td>
                                </tr>

                                <tr>
                                    <td width="50%">Description</td>
                                    <td width="50%" style="word-break: break-word;">{{ $data->balanceTransfer->description ?? '--' }}</td>
                                </tr>
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
