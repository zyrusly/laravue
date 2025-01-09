<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $role =  Role::get();

            return response()->json($role);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }


    public function create()
    {
        //
    }


    public function store(RoleRequest $request)
    {
        try {
            if ($request['id'] == null) {
                $role = Role::Create([
                    'name' => $request['name'],
                    'description' => $request['description'] ?? null
                ]);

                return response()->json([
                    'status' => '201',
                    'message' => 'Role created sucessfully!'
                ]);
            } else {
                $role = Role::updateOrCreate(
                    ['id' => $request['id']],
                    [
                        'name' => $request['name'],
                        'description' => $request['description'] ?? null
                    ]
                );

                return response()->json([
                    'status' => '200',
                    'message' => 'Role updated sucessfully!'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }


    public function show(Role $role)
    {
        try {
            $user_content = Role::where('id', $role->id)
                ->first();

            return response()->json($user_content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }


    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();

            return response()->json([
                'status' => '200',
                'message' => 'Role Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }
}
