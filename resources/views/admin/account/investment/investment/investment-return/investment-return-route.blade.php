<a href="{{route('admin.investor.show',$investor->id)}}" class="btn btn-sm ">General</a>
<a href="{{route('admin.investment.show',$investor->id)}}" class="btn btn-sm  ">Investment</a>
<a href="" class="btn btn-sm @if(\Request::route()->getName() == 'admin.investment-return.show') btn-primary @endif">Investment Return</a>
<a href="" class="btn btn-sm ">Deeds</a>
<a href="" class="btn btn-sm ">Project & Task</a>


