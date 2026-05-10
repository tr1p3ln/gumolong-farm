<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // Hanya super_admin & admin yang boleh masuk
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! Auth::user()?->isAdmin()) {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  INDEX                                                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter pencarian
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%");
            });
        }

        // Filter role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest('user_id')->paginate(10)->withQueryString();

        // Summary cards
        $summary = [
            'total'    => User::count(),
            'aktif'    => User::where('status', 'aktif')->count(),
            'nonaktif' => User::where('status', 'nonaktif')->count(),
            'admin'    => User::whereIn('role', ['super_admin', 'admin'])->count(),
        ];

        return view('users.index', [
            'users'       => $users,
            'summary'     => $summary,
            'roleOptions' => ['super_admin', 'admin', 'operator', 'viewer'],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  STORE                                                               */
    /* ------------------------------------------------------------------ */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'unique:user,email'],
            'password'  => ['required', Password::min(8)->mixedCase()->numbers()],
            'role'      => ['required', Rule::in(['super_admin', 'admin', 'operator', 'viewer'])],
            'nomor_hp'  => ['nullable', 'string', 'max:20'],
            'status'    => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        // Hanya super_admin yang bisa membuat super_admin lain
        if ($validated['role'] === 'super_admin' && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya super admin yang dapat membuat akun super admin.');
        }

        User::create($validated);

        return back()->with('success', "Akun {$validated['nama']} berhasil dibuat.");
    }

    /* ------------------------------------------------------------------ */
    /*  UPDATE                                                              */
    /* ------------------------------------------------------------------ */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', Rule::unique('user', 'email')->ignore($user->user_id, 'user_id')],
            'role'     => ['required', Rule::in(['super_admin', 'admin', 'operator', 'viewer'])],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'status'   => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
        ]);

        // Jangan izinkan mengubah role super_admin kecuali oleh super_admin
        if ($user->isSuperAdmin() && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Tidak dapat mengubah akun super admin.');
        }

        // Cegah super_admin men-disable dirinya sendiri
        if ($user->user_id === Auth::id() && $validated['status'] === 'nonaktif') {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', "Akun {$user->nama} berhasil diperbarui.");
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE                                                              */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        return view('users.create', [
            'roleOptions' => ['super_admin', 'admin', 'operator', 'viewer'],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  SHOW                                                                */
    /* ------------------------------------------------------------------ */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT                                                                */
    /* ------------------------------------------------------------------ */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user'        => $user,
            'roleOptions' => ['super_admin', 'admin', 'operator', 'viewer'],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  TOGGLE STATUS (AJAX-friendly)                                       */
    /* ------------------------------------------------------------------ */
    public function toggleStatus(User $user)
    {
        // Cegah self-disable
        if ($user->user_id === Auth::id()) {
            return back()->withErrors(['toggle' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        // Cegah non-super_admin mengubah status super_admin
        if ($user->isSuperAdmin() && ! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        $label = $user->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$user->nama} berhasil {$label}.");
    }

    /* ------------------------------------------------------------------ */
    /*  DESTROY                                                             */
    /* ------------------------------------------------------------------ */
    public function destroy(User $user)
    {
        // Hanya super_admin yang bisa hapus
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya super admin yang dapat menghapus akun.');
        }

        // Cegah hapus diri sendiri
        if ($user->user_id === Auth::id()) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $nama = $user->nama;

        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->delete();

        return back()->with('success', "Akun {$nama} berhasil dihapus.");
    }

    /* ------------------------------------------------------------------ */
    /*  RESET PASSWORD (oleh admin)                                         */
    /* ------------------------------------------------------------------ */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        if ($user->isSuperAdmin() && ! Auth::user()->isSuperAdmin()) {
            abort(403);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', "Password akun {$user->nama} berhasil direset.");
    }
}