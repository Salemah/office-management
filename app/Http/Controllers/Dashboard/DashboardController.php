<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('dashboard.main');
    }
     public function testrole()
    {
        //Role::create(['name' => 'superAdmin']);
    // Permission::create(['name' => 'edit_employee']);
    $role = Role::findById(1);
    $role->givePermissionTo(['edit_client','delete_employee','edit_employee']);
       //$permission->assignRole($role);
    $user =User::findOrFail(2);
    $user->assignRole('Employee');
    }
}
