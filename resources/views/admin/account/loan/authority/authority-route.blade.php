<a href="" class="btn btn-sm @if(\Request::route()->getName() == 'admin.loan-authority.show') btn-primary @endif">General</a>
<a href="{{route('admin.loan.show',$authority->id)}}" class="btn btn-sm ">Loan</a>
<a href="" class="btn btn-sm ">Loan Return</a>
<a href="" class="btn btn-sm ">Deeds</a>
<a href="" class="btn btn-sm ">Project & Task</a>


