<?php

namespace App\Http\Controllers;

use App\User; // Correct namespace for User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; // If password reset is needed later
use Illuminate\Support\Facades\Auth; // To check admin role

class PenggunaController extends Controller
{
    // Middleware to ensure only authenticated admins can access
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Check if user is admin (role 1 and status 1)
            if (Auth::user()->role != 1 || Auth::user()->status != 1) {
                // Redirect or abort if not authorized
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $query = User::where('role', '!=', 1); // Exclude other admins from the list

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $users = $query->orderBy('name', 'asc')->paginate(15); // Paginate results
        // dd($users);

        // Define roles for the dropdown in the view
        $roles = [
            2 => 'Pendaftaran',
            3 => 'Dokter',
            4 => 'Apotek',
        ];

        return view('pengguna.index', compact('users', 'roles', 'keyword'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user  // Use Route Model Binding
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // Ensure admin cannot change their own role or other admins' roles via this form
        if ($user->role == 1) {
             return redirect()->route('pengguna')->with('gagal', 'Tidak dapat mengubah peran Admin.');
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|integer|in:2,3,4', // Validate role is one of the allowed values
        ]);

        if ($validator->fails()) {
            return redirect()->route('pengguna')
                        ->withErrors($validator)
                        ->withInput()
                        ->with('gagal', 'Gagal memperbarui peran. Data tidak valid.'); // Add error flash
        }

        // Update the user's role
        $user->email_verified_at = now();
        $user->role = $request->role;
        $user->status = 1;
        $user->save();


        return redirect()->route('pengguna')->with('sukses', 'Peran pengguna berhasil diperbarui.');
    }

    // Optional: Add methods for create, show, destroy if needed later
}
