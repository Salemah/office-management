@extends('layouts.dashboard.app')

@section('title', 'Purchase Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Purchase</a>
            </li>

        </ol>
        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.inventory.purchase.create') }}">
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
                            <td colspan="10">Purchase</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>Warehouse</th>
                            <th>Supplier</th>
                            <th>Reference No</th>
                            <th>Invoice No</th>
                            <th>Cash Memo</th>
                            <th>Total Quantity</th>
                            <th>Total Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ Carbon\Carbon::parse($purchase->date)->format('d F, Y') ?? '--' }}</th>
                            <th>{{ $purchase->warehouses->name ?? '--' }}</th>
                            <th>{{ $purchase->suppliers->name ?? '--' }}</th>
                            <th>{{ $purchase->reference_no ?? '--' }}</th>
                            <th>{{ $purchase->invoice_no ?? '--' }}</th>
                            <th>{{ $purchase->cash_memo ?? '--' }}</th>
                            <th>{{ $purchase->total_qty ?? '--' }}</th>
                            <th>{{ $purchase->total_discount }}</th>

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
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>{{ $purchase->total_tax ?? '--' }}</th>
                            <th>{{ $purchase->order_tax_rate ?? '--' }}</th>
                            <th>{{ $purchase->order_discount ?? '--' }}</th>
                            <th>{{ $purchase->order_tax ?? '--' }}</th>
                            <th>{{ $purchase->shipping_cost ?? '--' }}</th>
                            <th>{{ $purchase->grand_total ?? '--' }}</th>
                            <th>{{ $purchase->paid_amount ?? '--' }}</th>
                            <th>
                                @if($purchase->status == 1)
                                    Received
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
                            <th width="50%">{{ $purchase->document ?? '--' }}</th>
                            <th width="50%">{{ $purchase->note ?? '--' }}</th>
                        </tr>

                    </tbody>
                </table>
                <br>

                <table class="table table-bordered table-striped table-hover col-md-12">
                    <thead>
                        <tr class="table-primary text-center">
                            <td colspan="6">Purchase Details</td>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Purchase Quantity</th>
                            <th>Purchase Price</th>
                            <th>Unit Cost</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseDetails as $key => $purchaseDetail)
                            <tr>
                                <th>{{ $key + 1 }}</th>
                                <th>{{ $purchaseDetail->products->name ?? '--' }}</th>
                                <th>{{ $purchaseDetail->purchase_qty ?? '--'  }}</th>
                                <th>{{ $purchaseDetail->purchase_price ?? '--'  }}</th>
                                <th>{{ $purchaseDetail->net_unit_cost ?? '--'  }}</th>
                                <th>{{ $purchaseDetail->total ?? '--'  }}</th>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
            </div>

        </div>
    </div>

@endsection
