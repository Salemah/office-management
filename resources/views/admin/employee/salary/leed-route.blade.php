<a href="{{route('admin.employee.show',$employee->id) }}" class="btn btn-sm @if(\Request::route()->getName() == 'admin.employee.show') btn-primary @endif">Generale</a>
<a href="#" class="btn btn-sm ">Project & Task</a>
<a href="#" class="btn btn-sm ">Attendance</a>
<a href="#" class="btn btn-sm ">Leave</a>
<a href="{{route('admin.salary.show', $employee->id)}}" class="btn btn-sm ">Salary</a>
<a href="{{route('admin.employee-leeds.show',$employee->id) }}" class="btn btn-sm @if(\Request::route()->getName() == 'admin.employee-leeds.show') btn-primary @endif">Leeds</a>
<a href="" class="btn btn-sm ">Promotions</a>


