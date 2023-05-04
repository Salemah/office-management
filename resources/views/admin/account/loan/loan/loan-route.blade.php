<a href="{{route('admin.loan-authority.show',$authority->id)}}" class="btn btn-sm ">General</a>
<a href="" class="btn btn-sm @if(\Request::route()->getName() == 'admin.loan.show') btn-primary @endif ">Loan</a>
<a href="" class="btn btn-sm ">Loan Return</a>
<a href="" class="btn btn-sm ">Deeds</a>
<a href="" class="btn btn-sm ">Project & Task</a>


