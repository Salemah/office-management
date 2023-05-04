<header class="header header-sticky mb-2" >
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button"
                onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
            </svg>
        </button>

        <a class="header-brand d-md-none" href="#">
            <svg width="118" height="46" alt="CoreUI Logo">
                <use xlink:href="{{ asset('dashboard/assets/brand/coreui.svg#full') }}"></use>
            </svg>
        </a>
        {{-- <ul class="header-nav d-none d-md-flex">
            <li class="nav-item">
                <a class="nav-link text-primary" href="{{ route('admin.crm.client-reminder.index') }}">
                    <button type="button" class="btn btn-sm btn-info text-light">Reminder</button>
                </a>
            </li>

        </ul> --}}
        <ul class="header-nav ms-auto" style="color: black">
            {{-- <li class="nav-item"><a class="nav-link " href="#" >
                    <svg class="icon icon-lg " style="color: black">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-bell" style="color: black"></use>
                    </svg>
                </a></li>
            <li class="nav-item"><a class="nav-link" href="#">
                    <svg class="icon icon-lg">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-list-rich"></use>
                    </svg>
                </a></li>
            <li class="nav-item"><a class="nav-link" href="#">
                    <svg class="icon icon-lg">
                        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-envelope-open"></use>
                    </svg>
                </a></li> --}}
        </ul>
        @php
        if (Auth::id() == 1) {
            $reminders = \Illuminate\Support\Facades\DB::table('client_reminders')->where('status',0)->where('deleted_at',null)->get()->count();
        }
        else{
            $reminders = \Illuminate\Support\Facades\DB::table('client_reminders')->where('status',0)->where('created_by', Auth::id())->where('deleted_at',null)->get()->count();
        }
        @endphp
        <ul class="header-nav ms-3">
            <li class="nav-item">
                <a class="nav-link text-primary" href="{{ route('admin.crm.client-reminder.index') }}">
                    <button type="button" class="btn  position-relative" title="Reminder">
                        <i class='bx bxs-bell-ring' style="font-size: 26px"></i>
                        <span class="position-absolute top-0 start-90 translate-middle badge rounded-pill bg-danger">
                          {{$reminders}}
                          <span class="visually-hidden">unread messages</span>
                        </span>
                    </button>
                </a>
            </li>
            <li class="nav-item dropdown"><a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button"
                                             aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-md">
                        <i class='bx bxs-flag-checkered text-primary' style="font-size: 30px"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <a class="dropdown-item " href="{{route('changeLang','en')}}">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                        </svg>
                        English
                    </a>
                    <a class="dropdown-item" href="{{route('changeLang','bn')}}">
                        <svg class="icon me-2">
                            <use xlink:href="{{asset('dashboard/vendors/@coreui/flag/cif-bd.svg#cil-settings')}}"></use>
                        </svg>
                        Bangla
                    </a>
                    <a class="dropdown-item" href="{{route('changeLang','fr')}}">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg>
                        French
                    </a>
                    <a class="dropdown-item" href="{{route('changeLang','sp')}}">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg>
                        Spanish
                    </a>
                    <a class="dropdown-item" href="{{route('changeLang','gr')}}">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg>
                        German
                    </a>
                    <a class="dropdown-item" href="{{route('changeLang','hin')}}">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg>
                        Hindi
                    </a>
                </div>
            </li>
        </ul>
        @php
            $employee = \Illuminate\Support\Facades\DB::table('employees')->find($user->user_id);
        @endphp
        {{ Auth::user()->name }}
        <ul class="header-nav ms-3">
            <li class="nav-item dropdown">
                <a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button"aria-haspopup="true"aria-expanded="false">
                    <div class="avatar avatar-md">
                        <img class="avatar-img"src="@if(isset($employee->image)){{ asset('img/employee/'.$employee->image)}} @else {{asset('img/no-image/noman.jpg')}}  @endif"alt="user@email.com">
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="dropdown-header bg-light py-2">
                        <div class="fw-semibold">Settings</div>
                    </div>
                    <a class="dropdown-item" href="@if($employee){{route('admin.employee.show', $employee->id)}} @else {{route('admin.dashboard')}}  @endif">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                        </svg>
                        Profile
                    </a>
                    <a class="dropdown-item" href="#">
                        <svg class="icon me-2">
                            <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-settings"></use>
                        </svg>
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" style="border: none; background: transparent; margin-left: 40px">
                            <use
                                xlink:href="{{ asset('dashboard/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}"></use>
                            Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
    <div class="header-divider"></div>
    <div class="container-fluid" style="background: white">
        @yield('breadcrumb')
    </div>


</header>

