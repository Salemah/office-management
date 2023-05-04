@extends('layouts.dashboard.app')

@section('title', 'monthly gross profit')

@section('breadcrumb')
    <nav aria-label="breadcrumb" class="d-flex align-items-center justify-content-between" style="width: 100%">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            {{--<li class="breadcrumb-item">--}}
                {{--<a href="#">Monthly Gross Profit</a>--}}
            {{--</li>--}}
        </ol>
    </nav>
@endsection

@section('content')

    <!--Start Alert -->
    @include('layouts.dashboard.partials.alert')
    <!--End Alert -->

    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4" style="margin-top: 5px">
                        <div class="form-group">
                            <label for="warehouse"><b>Warehouse</b><span class="text-danger"> *</span></label>
                            <select class="form-control warehouse " id="warehouse" name="warehouse" required onchange="getEmployees()">

                                @if($user_role->name == 'Super Admin' || $user_role->name == 'Admin')
                                    <option value="" selected>--Select Warehouse--</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endforeach
                                @elseif($user_role->name == 'Employee')
                                    {{$auth = Auth::user()}}
                                    {{$emp_ware=\App\Models\Employee\Employee::where('id',$auth->user_id)->with('ware_house')->first()}}
                                    <option value="{{$emp_ware->warehouse}} readonly">{{$emp_ware->ware_house->name}}</option>
                                @endif
                            </select>
                            @error('warehouse')
                            <span class="text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="start_date"><b>Month</b>  <span class="text-danger">*</span></label>
                            <input type="month" name="month" id="month" value="{{ old('month') }}" class="form-control mt-1 @error('month') is-invalid @enderror" required>

                            @error('month')
                            <span class="text-danger" role="alert">
                                <p>{{ $message }}</p>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group">
                            <button onclick="getData()" title="Submit Button" type="submit" id="search" class="btn btn-sm btn-primary float-left search" style="margin-top: 30px"> <i class="bx bxs-eye"></i>Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="table" class="table table-hover table-bordered ">
                            <thead>
                            <tr class="align-middle table" style="text-align: center">
                                <th>Total Product Purchase</th>
                                <th>Total Sell</th>
                                <th>Gross Profit</th>
                            </tr>
                            </thead>
                            <tbody class="" id="table-body" style="text-align: center">

                            </tbody>
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

        function getData(){
            var month = $('#month').val();
            var warehouse = $('#warehouse').val();
            var url = '{{ route('admin.gross.profit',['month' => ':month', 'warehouse' => ':warehouse']) }}';
            url= url.replace(':month',month);
            url= url.replace(':warehouse',warehouse)
            console.log(url);
            $.ajax({
                type: "GET",
                url: url,
                success: function (data) {
                    $('#table-body').empty();
                    $('#table-body').append('<tr><td>' + data.purchase +' tk</td><td>' + data.sell +' tk</td><td>' + data.profit +' tk</td></tr>')
                }
            })
        }
    </script>
@endpush
