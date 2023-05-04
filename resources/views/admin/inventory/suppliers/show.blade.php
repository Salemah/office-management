@extends('layouts.dashboard.app')

@section('title', 'Supplier Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Supplier</a>
            </li>

        </ol>

        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.inventory.suppliers.supplier.create') }}">
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
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr class="text-left text-white">
                            <td colspan="2"><b>Supplier Details</b></td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td width="50%">Name</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Supplier Priority</td>
                            <td width="50%" style="word-break: break-word;">

                                @if($supplier->customer_type_priority == 1)
                                  <span class="btn btn-sm btn-success mb-2 text-white" title="First">First</span>
                                @elseif($supplier->customer_type_priority == 2)
                                  <span class="btn btn-sm btn-success mb-2 text-white" title="Second">Second</span>
                                @elseif($supplier->customer_type_priority == 3)
                                    <span class="btn btn-sm btn-success mb-2 text-white" title="Third">Third</span>
                                @endif
                        </tr>

                        <tr>
                            <td width="50%">Phone</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->phone ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Email</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->email ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Postal Code</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->postal_code ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Tax Number</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->tax_number ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Country</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->countries->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">State</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->states->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">City</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->cities->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Area</td>
                            <td width="50%" style="word-break: break-word;">{{ $supplier->areas->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Status</td>
                            <td width="50%" style="word-break: break-word;">
                                @if($supplier->status == 1)
                                  <span class="btn btn-sm btn-success mb-2 text-white" title="Active">Active</span>
                                @elseif($supplier->status == 0)
                                  <span class="btn btn-sm btn-danger mb-2 text-white" title="Inactive">Inactive</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td width="50%">Address</td>
                            <td width="50%" style="word-break: break-word;">{!! $supplier->address ?? '--' !!}</td>
                        </tr>
                        <tr>
                            <td width="50%">Description</td>
                            <td width="50%" style="word-break: break-word;">{!! $supplier->description ?? '--' !!}</td>
                        </tr>

                    </tbody>
                  </table>
            </div>
        </div>
    </div>

@endsection
