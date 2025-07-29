<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserPostController extends Controller
{
     public function index(Request $request) {

        $users = User::where('is_admin' , false)->with('roles')->latest()->paginate(10);

        return Inertia::render('Admin/User/Index' , [
            'users' => UserResource::collection($users),
            'roles' => Role::pluck('name', 'id'),
        ]);
    }


     public function showAdmin(Request $request) {

        $users = User::where('is_admin' , true)->with('roles')->latest()->paginate(10);

        return Inertia::render('Admin/IsAdmin/Index' , [
            'users' => UserResource::collection($users),
            'roles' => Role::pluck('name', 'id'),
        ]);
    }


    public function show(User $user_post)
    {
        return Inertia::render('Admin/User/Show', [
            'user' => $user_post,
        ]);
    }



  public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'image'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'roles'    => 'nullable|array', // validate roles as array
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('users', 'public');
        }

        // Create user
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'image'    => $imagePath,
            'is_admin' => $request->is_Admin,
        ]);

        // Assign roles if provided
        $user->syncRoles($request->input('roles', []));

        return redirect()->back()->with('success', 'User created successfully!');
    }

    public function update(Request $request, User $user_post)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user_post->id,
            'password' => 'nullable|min:6',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles'    => 'nullable|array',
            'is_Admin' => 'nullable|boolean',
        ]);

        $user_post->name = $validated['name'];
        $user_post->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user_post->password = Hash::make($validated['password']);
        }

        // Handle image
        if ($request->hasFile('image')) {
            if ($user_post->image) {
                Storage::disk('public')->delete($user_post->image);
            }
            $path = $request->file('image')->store('users', 'public');
            $user_post->image = $path;
        }

        // Update admin 
        $user_post->is_admin = $request->boolean('is_Admin');

        $user_post->save();

        // Sync roles
        $user_post->syncRoles($request->input('roles', []));

        return back()->with('success', 'User updated successfully.');
    }



     public function destroy(User $user_post)
    {
        $user_post->delete();
        return redirect()->back();
    }

    public function selectDelete(Request $request)
    {
        User::whereIn('id', $request->input('user_ids', []))->delete();
        return redirect()->back();
    }

}
