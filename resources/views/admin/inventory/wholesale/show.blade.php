@extends('layouts.dashboard.app')

@section('title', 'Whole Sale Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Whole Sale</a>
            </li>

        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.inventory.wholesale.create') }}">
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

                <table class="table table-bordered table-striped table-hover col-md-12 ">
                    <thead>
                        <tr class="table-info text-center">
                            <td colspan="10">Whole Sale</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>Warehouse</th>
                            <th>Customer</th>
                            <th>Reference No</th>
                            <th>Invoice No</th>
                            <th>Cash Memo</th>
                            <th>Total Quantity</th>
                            <th>Total Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ Carbon\Carbon::parse($sale->date)->format('d F, Y') ?? '--' }}</th>
                            <th>{{ $sale->warehouses->name ?? '--' }}</th>
                            <th>{{ $sale->customers->name ?? '--'}}</th>
                            <th>{{ $sale->reference_no ?? '--' }}</th>
                            <th>{{ $sale->invoice_no ?? '--' }}</th>
                            <th>{{ $sale->cash_memo ?? '--' }}</th>
                            <th>{{ $sale->total_qty ?? '--' }}</th>
                            <th>{{ $sale->total_discount ?? '--' }}</th>

                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered table-striped table-hover col-md-12 ">
                    <thead>
                        <tr>
                            <th>Total Tax</th>
                            <th>Order Tax Rate</th>
                            <th>Order Discount</th>
                            <th>Order Tax</th>
                            <th>Shipping Cost</th>
                            <th>Grand Total</th>
                            <th>Paid Amount</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ $sale->total_tax ?? '--' }}</th>
                            <th>{{ $sale->order_tax_rate ?? '--' }}</th>
                            <th>{{ $sale->order_discount ?? '--' }}</th>
                            <th>{{ $sale->order_tax ?? '--' }}</th>
                            <th>{{ $sale->shipping_cost ?? '--' }}</th>
                            <th>{{ $sale->grand_total ?? '--' }}</th>
                            <th>{{ $sale->paid_amount ?? '--' }}</th>
                            <th>
                                @if($sale->payment_status == 1)
                                    Received
                                    @else
                                    --
                                @endif
                            </th>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered table-striped table-hover col-md-12 ">
                    <thead>
                        <tr>
                            <th width="50%">Document</th>
                            <th width="50%">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th width="50%">{{ $sale->document ?? '--' }}</th>
                            <th width="50%">{{ $sale->note ?? '--' }}</th>
                        </tr>

                    </tbody>
                </table>
                <br>

                <table class="table table-bordered table-striped table-hover col-md-12">
                    <thead>
                        <tr class="table-primary text-center">
                            <td colspan="6">Whole Sale Details</td>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Sale Quantity</th>
                            <th>Sale Price</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saleDetails as $key => $saleDetail)
                            <tr>
                                <th>{{ $key + 1 }}</th>
                                <th>{{ $saleDetail->products->name ?? '--' }}</th>
                                <th>{{ $saleDetail->sale_qty ?? '--'  }}</th>
                                <th>{{ $saleDetail->selling_price ?? '--'  }}</th>
                                <th>{{ $saleDetail->net_unit_cost ?? '--'  }}</th>
                                <th>{{ $saleDetail->total ?? '--'  }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
            </div>

        </div>
    </div>

@endsection
