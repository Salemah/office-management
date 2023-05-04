<a href="{{route('admin.crm.client.show',$Client->id) }}" class="btn btn-sm @if(\Request::route()->getName() == 'admin.crm.client.show') btn-primary @endif">Generale</a>
<a href="{{route('admin.crm.client.comment',$Client->id) }}" class="btn btn-sm @if(\Request::route()->getName() == 'admin.crm.client.comment') btn-primary @endif">Comments</a>
<a href="{{route('admin.crm.client.reminder',$Client->id) }}" class="btn btn-sm  @if(\Request::route()->getName() == 'admin.crm.client.reminder') btn-primary @endif">Reminder</a>
<a href="" class="btn btn-sm ">Deeds</a>
<a href="" class="btn btn-sm ">Project & Task</a>
<a href="{{route('admin.crm.client.client.assignto',$Client->id) }}" class="btn btn-sm @if(\Request::route()->getName() == 'admin.crm.client.client.assignto') btn-primary @endif"  >Assign To</a>

