<a href="{{route('admin.loan-authority.show',$authority->id)}}" class="btn btn-sm ">General</a>
<a href="{{route('admin.loan.show',$authority->id)}}" class="btn btn-sm  ">Loan</a>
<a href="" class="btn btn-sm @if(\Request::route()->getName() == 'admin.loan-return.show') btn-primary @endif">Loan Return</a>
<a href="" class="btn btn-sm ">Deeds</a>
<a href="" class="btn btn-sm ">Project & Task</a>


