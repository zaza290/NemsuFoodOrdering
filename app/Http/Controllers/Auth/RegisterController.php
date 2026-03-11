<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'email', 'max:255', 'unique:users',
                // NEMSU email validation
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@nemsu.edu.ph')) {
                        $fail('Only NEMSU email addresses are allowed (@nemsu.edu.ph).');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:student,faculty',
            'student_id' => 'required_if:role,student|nullable|string',
            'department' => 'required|string',
            'phone' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'student_id' => $request->student_id,
            'department' => $request->department,
            'phone' => $request->phone,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('home');
    }
}
