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

    public function store(Request $request){
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:Admin,Client',
        ]);

        $userType = UserType::where('role', $validated['role'])->firstOrFail();

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => $validated['password'],
            'user_type_id' => $userType->id,
        ]);

        return response()->json([
            'data'    => $user
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:users,email,$id",
            'password' => 'sometimes|string|min:6',
        ]);

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($validated);

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
