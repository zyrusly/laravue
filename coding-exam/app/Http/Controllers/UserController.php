<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with(['role'])->get();

        return response()->json($users);
    }


    public function create() {}


    public function store(UserRequest $request)
    {
        //IF REQUEST HAS ID UPDATE, OTHERWISE CREATE
        info($request);
        try {
            if ($request['id'] == null) {
                $user = User::Create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => bcrypt($request['password']),
                    'is_active' => true,
                    'role_id' => $request['role_id'] ?? null
                ]);

                return response()->json([
                    'status' => '201',
                    'message' => 'User created sucessfully!'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }


    public function show(User $user)
    {
        try {
            $user_content = User::where('id', $user->id)
                ->with(['role'])
                ->first();

            return response()->json($user_content);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }


    public function edit()
    {
        //
    }


    public function update(Request $request, User $user)
    {
        try {
            info($request);
            $user = User::find($user->id);
            if ($user) {
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->role_id = $request['role_id'];
                $user->save();
            }

            return response()->json([
                'status' => '200',
                'message' => 'User updated successfully!'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }


    public function destroy(User $user)
    {
        try {
            if (auth()->user()->id == $user->id) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Cannot remove Current User.'
                ]);
            }
            $user->delete();

            return response()->json([
                'status' => '200',
                'message' => 'User Successfully Deleted.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'Error',
                'Message' => $th->getMessage()
            ]);
        }
    }
}
