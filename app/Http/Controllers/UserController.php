<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        return response()->json(User::all(), 200);
    }

    public function show($id){
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'data'    => $user
        ], 200);
    }

    public function store(StoreUserRequest $request) {
        $data = $request->validated();

        $userType = UserType::where('role', $data['role'])->firstOrFail();

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $data['password'],
            'user_type_id' => $userType->id,
        ]);

        return response()->json([
            'data'    => $user
        ], 201);
    }

    public function update(UpdateUserRequest $request, $id) {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->validated());

        return response()->json([
            'data'    => $user
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'Admin user deleted successfully',
        ], 204);
    }

}
