<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class UserController extends Controller
{
    private $cname = "UserController";
    public function index()
    {
        $users = DB::table('users')->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with('roles')->find($id);
        $roles = Role::all();

        if (!empty($user)) {
            $user_roles = [];
            $user_roles = collect($user_roles);

            foreach ($roles as $role) {
                if (DB::table('role_user')
                    ->where('role_id', $role->id)
                    ->where('user_id', $id)
                    ->exists()
                ) {
                    $user_roles->put($role->name, true);
                } else {
                    $user_roles->put($role->name, false);
                }
            }

            $user_roles = $user_roles->all();

            $user = collect($user);
            $user->put('roles', $user_roles);
            $user = $user->all();

            return response()->json($user);
        }

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    public function store(Request $request)
    {
        $user = User::where('email', '=', $request->email)->first();
        $auth_user = (object)$request->authenticatedUser;
        $auth_user_name = $auth_user->name;
        $auth_user_id = $auth_user->id;

        if (empty($user)) {
            $data = DB::table('users')->insert([
                [
                    'department_id' => $request->department_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'remember_token' => str_random(10),
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            ]);

            \Logger::instance()->log(
                Carbon::now(),
                $auth_user_id,
                $auth_user_name,
                $this->cname,
                "store",
                "message",
                "Create new User: " . $request->name
            );


            return response()->json($this->index());
        } else {

            return response()->json(['error' => 'Email already exists!'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // return $request;
        $auth_user = (object)$request->authenticatedUser;
        $auth_user_name = $auth_user->name;
        $auth_user_id = $auth_user->id;

        // return $request;


        $old_roles = DB::table('role_user')->where('user_id', $id)->get();

        if (empty($request->password)) {
            $new_data = DB::table('users')
                ->where('id', $id)
                ->update([
                    'department_id' => $request->department_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'updated_at' => \Carbon\Carbon::now()
                ]);
        } else {
            $new_data = DB::table('users')
                ->where('id', $id)
                ->update([
                    'department_id' => $request->department_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'updated_at' => \Carbon\Carbon::now()
                ]);
        }

        \Logger::instance()->log(
            Carbon::now(),
            $auth_user_id,
            $auth_user_name,
            $this->cname,
            "update",
            "message",
            "Update User Details ID: " . $id
        );

        if (!empty($request->roles)) {
            DB::table('role_user')->where('user_id', $id)->delete();
            $request->roles = (object)$request->roles;
            $this->setRoles($request->roles, $id);
        }

        return response()->json($this->index());
    }

    public function showSearch(Request $request)
    {
        $users = User::where('name', 'like', '%' . $request->user . '%')->get();
        return response()->json($users);
    }

    public function setRoles($roles, $id)
    {

        // manage accounts
        if ($roles->create_account) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_account')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_account) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_account')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage client
        if ($roles->create_client) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_client')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_client) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_client')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage items
        if ($roles->create_item) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_item')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_item) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_item')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->delete_item) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'delete_item')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage components
        if ($roles->create_comp) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_comp')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_comp) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_comp')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->delete_comp) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'delete_comp')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        //manage direct receieve
        if ($roles->create_direct_receive) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_direct_receive')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->update_direct_receive) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_direct_receive')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage s.o
        if ($roles->create_sales_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_sales_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_sales_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_sales_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->approved_sales_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'approved_sales_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage d.r
        if ($roles->create_delivery_receipt) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_delivery_receipt')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->update_delivery_receipt) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_delivery_receipt')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage p.o
        if ($roles->view_purchase_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'view_purchase_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->create_purchase_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_purchase_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_purchase_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_purchase_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->approved_purchase_order) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'approved_purchase_order')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }


        // manage sales return
        if ($roles->create_sales_return) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_sales_return')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->update_sales_return) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_sales_return')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->approve_sales_return) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'approve_sales_return')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage supplier
        if ($roles->create_supplier) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_supplier')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        if ($roles->update_supplier) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_supplier')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->delete_supplier) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'delete_supplier')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage i.r
        if ($roles->create_item_receipt) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_item_receipt')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        //manage email
        if ($roles->send_mr_email) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'send_mr_email')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->send_po_email) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'send_po_email')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }

        // manage accountability
        if ($roles->create_accountability) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'create_accountability')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->update_accountability) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'update_accountability')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
        if ($roles->delete_accountability) {
            $role = DB::table('roles')
                ->select('id')
                ->where('name', 'delete_accountability')
                ->first();

            if (DB::table('role_user')
                ->where('role_id', $role->id)
                ->where('user_id', $id)
                ->doesntExist()
            ) {
                DB::table('role_user')->insert(
                    ['user_id' => $id, 'role_id' => $role->id]
                );
            }
        }
    }
}
