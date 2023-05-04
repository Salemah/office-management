@extends('layouts.dashboard.app')

@section('title', 'Inventory Product Details')

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
                <a href="#">Inventory Product Details </a>
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
                        <h2>{{$inventoryProduct->products->name}} @if ($inventoryProduct->variant)- {{$inventoryProduct->variant->value}} {{$productVariant->name}}@endif</h2>
                        <table class="table border mb-0" id="table">
                            <thead class="table-light fw-semibold">
                            <tr class="align-middle table">
                                <th>#</th>
                                <th>Date</th>
                                <th>Stock In</th>
                                <th>Stock Out</th>
                                <th>Current Price</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventories as $key=> $inventory)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$inventory->date}}</td>
                                    <td>{{$inventory->stock_in ? $inventory->stock_in : 0 }}</td>
                                    <td>{{$inventory->stock_out ? $inventory->stock_out : 0 }}</td>
                                    <td>{{$price[$key]->price ?? 0}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>



    </script>
@endpush
