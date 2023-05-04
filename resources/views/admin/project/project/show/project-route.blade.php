<a href="{{route('admin.projects.show', $project->id)}}" class="btn btn-sm @if(\Request::route()->getName() == 'admin.projects.show') btn-primary @endif">General</a>
<a href="{{route('admin.project.task.view',$project->id)}}" class="btn btn-sm  @if(\Request::route()->getName() == 'admin.project.task.view') btn-primary @endif">Task</a>
<a href="{{route('admin.project.account-budget.view',$project->id)}}" class="btn btn-sm  @if(\Request::route()->getName() == 'admin.project.account-budget.view') btn-primary @endif">Accounts</a>
<a href="{{route('admin.project.module.add',$project->id)}}" class="btn btn-sm  @if(\Request::route()->getName() == 'admin.project.module.add') btn-primary @endif">Module</a>
<a href="{{route('admin.project.duration',$project->id)}}" class="btn btn-sm  @if(\Request::route()->getName() == 'admin.project.duration') btn-primary @endif">Duration</a>
<a href="{{route('admin.project.employee.assign.to',$project->id)}}" class="btn btn-sm  @if(\Request::route()->getName() == 'admin.project.employee.assign.to') btn-primary @endif">Assignt To</a>
<a href="{{route('admin.project.resource', $project->id)}}" href="" class="btn btn-sm ">Resource</a>




