@extends('layouts.dashboard.app')

@section('title', 'Customer Details')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Customer</a>
            </li>

        </ol>

        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.inventory.customers.customer.create') }}">
            <i class='bx bx-plus'></i> Create
        </a>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr class="text-left text-white">
                            <td colspan="2"><b>Customer Details</b></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="50%">Name</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Customer Priority</td>
                            <td width="50%" style="word-break: break-word;">
                                @if($customer->customer_type_priority == 1)
                                  <span class="btn btn-sm btn-success mb-2 text-white" title="First">First</span>
                                @elseif($customer->customer_type_priority == 2)
                                  <span class="btn btn-sm btn-success mb-2 text-white" title="Second">Second</span>
                                @elseif($customer->customer_type_priority == 3)
                                    <span class="btn btn-sm btn-success mb-2 text-white" title="Third">Third</span>
                                @endif
                            </tr>

                        <tr>
                            <td width="50%">Phone</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->phone ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Email</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->email ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Postal Code</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->postal_code ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Tax Number</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->tax_number ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Country</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->countries->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">State</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->states->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">City</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->cities->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Area</td>
                            <td width="50%" style="word-break: break-word;">{{ $customer->areas->name ?? '--' }}</td>
                        </tr>

                        <tr>
                            <td width="50%">Status</td>
                            <td width="50%" style="word-break: break-word;">
                                @if($customer->status == 1)
                                  <span class="btn btn-sm btn-success mb-2 text-white" title="Active">Active</span>
                                @elseif($customer->status == 0)
                                  <span class="btn btn-sm btn-danger mb-2 text-white" title="Inactive">Inactive</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td width="50%">Address</td>
                            <td width="50%" style="word-break: break-word;">{!! $customer->address ?? '--' !!}</td>
                        </tr>
                        <tr>
                            <td width="50%">Description</td>
                            <td width="50%" style="word-break: break-word;">{!! $customer->description ?? '--' !!}</td>
                        </tr>
                    </tbody>
                  </table>
            </div>
        </div>
    </div>

@endsection
