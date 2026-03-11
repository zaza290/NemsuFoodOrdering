<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['student', 'faculty'])
            ->withCount('orders')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,inactive'],
        ]);

        $user->update($data);

        return back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User removed.');
    }
}
