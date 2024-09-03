<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function IndexUser()
    {
        $data['title'] = "User Management";
        $data['users'] = User::get();
        $data['roles'] = Role::get();
        
        return view ('system.user-main', $data);
    }

    public function createUser(Request $request)
    {
        $role = Role::where('id', $request->role)->first();
        if ($role) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole($role);

            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'User Has Been Added']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Something Wrong']);
        }
    }

    public function editUser($id)
    {
        $user = User::where('id', $id)->first();
        $role = $user->roles->first();
        // var_dump($role);
        // die;
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $user,
                'role'    => $role,
            ]);
        }
    
    }

    public function updateUser(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $role = Role::where('id', $request->role)->first();

        if ($role) {
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->syncRoles([$role->id]);
                return redirect()->back()->with('status', ['type' => 'success', 'message' => 'User Has Been Updated']);
            }else {
                return redirect()->back()->with('status', ['type' => 'error', 'message' => 'User Not Found']);
            }
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Role Not Found']);
        }

    }
    
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    
        return response()->json(['success' => 'User deleted successfully']);
    }

    public function IndexRole()
    {
        $data['title'] = "Role Management";
        $data['users'] = User::get();
        $data['roles'] = Role::get();
        
        return view ('system.role-main', $data);
    }

    public function createRole(Request $request)
    {
       
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Role Has Been Added']);
        
    }

    public function editRole($id)
    {
        $role = Role::where('id', $id)->first();
        if ($role) {
            return response()->json([
                'success' => true,
                'message' => 'updated successfully!',
                'data'    => $role,
            ]);
        }
    }

    public function updateRole(Request $request)
    {
        $role = Role::where('id', $request->id)->first();

        if ($role) {
            $role->update([
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ]);
            return redirect()->back()->with('status', ['type' => 'success', 'message' => 'Role Has Been Update']);
        }else {
            return redirect()->back()->with('status', ['type' => 'error', 'message' => 'Role Not Found']);
        }
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        if ($role->users()->exists()) {
            return response()->json(['message' => 'Role cannot be deleted because it is assigned to one or more users'], 400);
        }
        $role->delete();
    
        return response()->json(['success' => 'Role deleted successfully']);
    }

}
