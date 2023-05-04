@extends('layouts.dashboard.app')

@section('title', 'Expense')

@push('css')
    <style>
        .text-right{
            text-align: end;
        }
        .invoice-container {
            padding: 1rem;
        }
        .invoice-container .invoice-header .invoice-type{
            width: 158px;
            text-align: center;
            margin: auto;
            border: 1px solid gray;
            display: block;
            padding: 2px 4px;
            text-decoration: none;
            margin-top: 8px;
        }
        .invoice-container .invoice-header .invoice-logo {
            margin: 0.8rem 0 0 0;
            display: inline-block;
            font-size: 1.6rem;
            font-weight: 700;
            color: #2e323c;
        }
        .invoice-container .invoice-header .invoice-logo img {
            max-width: 130px;
        }
        .invoice-container .invoice-header address {
            font-size: 0.8rem;
            color: #9fa8b9;
            margin: 0;
        }
        .invoice-container .invoice-details {
            margin: 1rem 0 0 0;
            padding: 1rem;
            line-height: 180%;
            background: #f5f6fa;
        }
        .invoice-container .invoice-details .invoice-num {
            text-align: right;
            font-size: 0.8rem;
        }
        .invoice-container .invoice-body {
            padding: 1rem 0 0 0;
        }
        .invoice-container .invoice-footer {
            text-align: center;
            font-size: 0.7rem;
            margin: 5px 0 0 0;
        }

        .invoice-status {
            text-align: center;
            padding: 1rem;
            background: #ffffff;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .invoice-status h2.status {
            margin: 0 0 0.8rem 0;
        }
        .invoice-status h5.status-title {
            margin: 0 0 0.8rem 0;
            color: #9fa8b9;
        }
        .invoice-status p.status-type {
            margin: 0.5rem 0 0 0;
            padding: 0;
            line-height: 150%;
        }
        .invoice-status i {
            font-size: 1.5rem;
            margin: 0 0 1rem 0;
            display: inline-block;
            padding: 1rem;
            background: #f5f6fa;
            -webkit-border-radius: 50px;
            -moz-border-radius: 50px;
            border-radius: 50px;
        }
        .invoice-status .badge {
            text-transform: uppercase;
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 1rem;
            }
        }


        .custom-table {
            border: 1px solid #e0e3ec;
        }
        .custom-table thead {
            background: #007ae1;
        }
        .custom-table thead th {
            border: 0;
            color: #ffffff;
        }
        .custom-table > tbody tr:hover {
            background: #fafafa;
        }
        .custom-table > tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        .custom-table > tbody td {
            border: 1px solid #e6e9f0;
        }


        .card {
            background: #ffffff;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            border: 0;
            margin-bottom: 1rem;
        }

        .text-success {
            color: #00bb42 !important;
        }

        .text-muted {
            color: #9fa8b9 !important;
        }

        .custom-actions-btns {
            margin: auto;
            display: flex;
            justify-content: flex-end;
        }

        .custom-actions-btns .btn {
            margin: .3rem 0 .3rem .3rem;
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
                <span>Expense</span>
            </li>
        </ol>
        <a href="{{ route('admin.expense.expense.index') }}" class="btn btn-sm btn-dark">Back to list</a>
    </nav>
@endsection

@section('content')

    <!-- Alert -->
    @include('layouts.dashboard.partials.alert')
    <!-- End:Alert -->
        <div class="row gutters">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="invoice-container">
                            <a href="{{route('admin.expense.print.invoice', $expenseInvoice->id)}}" class="btn btn-sm btn-dark text-white mb-3" style="cursor:pointer" title="Print"><i class="bx bx-printer"></i></a>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead class="table-primary">
                                          <tr>
                                            <th scope="col">Expense Invoice No</th>
                                            <th scope="col">Expense Invoice Date</th>
                                            <th scope="col">Expense By</th>
                                            <th scope="col">Description</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          <tr>
                                            <th >{{$expenseInvoice->expense_invoice_no}}</th>
                                            <td>{{$expenseInvoice->expense_invoice_date	}}</td>
                                            <td>@if(isset($expenseInvoice->expenseBy)){{$expenseInvoice->expenseBy->name}} @endif</td>
                                            <td>{!! $expenseInvoice->description !!} </td>
                                          </tr>
                                        </tbody>

                                      </table>
                                </div>
                            </div>
                            <h4 class="text-primary">Expense Details : </h4>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered ">
                                        <thead class="table-primary">
                                          <tr>
                                            <th scope="col">Expense Category</th>
                                            <th scope="col">Expense Date</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Amount</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($expense_details as $expenseDetails)
                                                <tr>
                                                    <th >{{$expenseDetails->expenseCategory->name}}</th>
                                                    <td>{{$expenseDetails->expense_date	}}</td>
                                                    <td>{{$expenseDetails->description	}}</td>
                                                    <td>{{$expenseDetails->amount}}</td>
                                                 </tr>
                                            @endforeach

                                        </tbody>
                                      </table>
                                </div>
                            </div>
                            {{-- @if ($documents)
                                <h4 class="text-primary">Documents : </h4>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table table-bordered ">
                                            <thead class="table-primary">
                                            <tr>
                                                <th scope="col">Document Title</th>
                                                <th scope="col">Document</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($documents as $key => $document)
                                                    <tr>
                                                        <th >{{$documents_title[$key]}}</th>
                                                        <td><button type="button" class="btn btn-outline-primary " ><a href="{{ asset('img/expense/documents/'.$document) }}" style="text-decoration: none;color:black"><i class='bx bx-down-arrow-alt'></i>Download</a></button></td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif --}}
                            @if ($transaction)
                                @if ($transaction->bankAccount)
                                    <h4 class="text-primary">Transaction : </h4>
                                    <div class="row">
                                        <div class="col-12">
                                            <table class="table table-bordered">
                                                <thead class="table-info">
                                                <tr>
                                                    <th scope="col">Transaction Way</th>
                                                    <th scope="col">Bank</th>
                                                    <th scope="col">Banch Name</th>
                                                    <th scope="col">Account Name</th>
                                                    <th scope="col">Account no</th>
                                                    <th scope="col">Check</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <th >{{$expenseInvoice->transaction_way == 1 ? 'Cash' : 'Bank'}}</th>
                                                    <td> @if(isset($bank->bank_name)){{$bank->bank_name}}@endif</td>
                                                    <td>{{$transaction->bankAccount->branch_name}}</td>
                                                    <td>{{$transaction->bankAccount->name}}</td>
                                                    <td>{{$transaction->bankAccount->account_number}}</td>
                                                    <td>{{$transaction->cheque_number}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endif
                            <div class="invoice-body">
                                <div class="row gutters">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table custom-table m-0">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="1">
                                                            <p>Subtotal<br>
                                                                Adjustment Type<br>
                                                                Adjustment Balance<br>
                                                            </p>
                                                            <h5 class="text-success"><strong>Grand Total</strong></h5>
                                                        </td>
                                                        <td>
                                                            <p>
                                                                {{ $totalBalance }}<br>
                                                                @if($expenseInvoice->adjustment_type == 1)
                                                                    Addition
                                                                @elseif($expenseInvoice->adjustment_type == 2)
                                                                    Subtraction
                                                                @else
                                                                    --
                                                                @endif
                                                                <br>
                                                                @if($expenseInvoice->adjustment_balance)
                                                                {{ $expenseInvoice->adjustment_balance }}
                                                                @else
                                                                    --
                                                                @endif
                                                                <br>
                                                            </p>
                                                            <h5 class="text-success"><strong>{{ $expenseInvoice->total}}</strong></h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('script')

@endpush

