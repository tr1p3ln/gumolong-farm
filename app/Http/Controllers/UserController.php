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

    /**
     * Cek apakah aktor (yang login) boleh melakukan aksi terhadap target user.
     * - super_admin : boleh aksi ke siapa saja (kecuali self-disable/self-delete)
     * - admin       : boleh aksi ke non-super_admin saja
     */
    private function canActOn(User $target): bool
    {
        $actor = Auth::user();

        if ($actor->isSuperAdmin()) {
            return true;
        }

        // admin tidak boleh menyentuh akun super_admin
        return ! $target->isSuperAdmin();
    }

    /* ------------------------------------------------------------------ */
    /*  INDEX                                                               */
    /* ------------------------------------------------------------------ */
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_hp', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest('user_id')->paginate(10)->withQueryString();

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

        if ($validated['role'] === 'super_admin' && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya super admin yang dapat membuat akun super admin.');
        }

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return back()->with('success', "Akun {$validated['nama']} berhasil dibuat.");
    }

    /* ------------------------------------------------------------------ */
    /*  UPDATE                                                              */
    /* ------------------------------------------------------------------ */
    public function update(Request $request, User $user)
    {
        // Pastikan aktor boleh mengedit target
        if (! $this->canActOn($user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah akun ini.');
        }

        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', Rule::unique('user', 'email')->ignore($user->user_id, 'user_id')],
            'role'     => ['required', Rule::in(['super_admin', 'admin', 'operator', 'viewer'])],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'status'   => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers()],
        ]);

        // Admin tidak boleh meng-assign role super_admin
        if ($validated['role'] === 'super_admin' && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya super admin yang dapat menetapkan role super admin.');
        }

        // Cegah siapa pun menonaktifkan akunnya sendiri lewat form edit
        if ($user->user_id === Auth::id() && $validated['status'] === 'nonaktif') {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', "Akun {$user->nama} berhasil diperbarui.");
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE / SHOW / EDIT (view helpers)                                 */
    /* ------------------------------------------------------------------ */
    public function create()
    {
        return view('users.create', [
            'roleOptions' => ['super_admin', 'admin', 'operator', 'viewer'],
        ]);
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user'        => $user,
            'roleOptions' => ['super_admin', 'admin', 'operator', 'viewer'],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  TOGGLE STATUS                                                       */
    /* ------------------------------------------------------------------ */
    public function toggleStatus(Request $request, User $user)
    {
        // Cegah self-disable
        if ($user->user_id === Auth::id()) {
            return back()->withErrors(['toggle' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        // Admin tidak boleh mengubah status akun super_admin
        if (! $this->canActOn($user)) {
            abort(403, 'Anda tidak memiliki izin untuk mengubah status akun ini.');
        }

        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        $label = $user->status === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => "Akun {$user->nama} berhasil {$label}.",
                'data'    => [
                    'user_id' => $user->user_id,
                    'status'  => $user->status,
                ],
            ]);
        }

        return back()->with('success', "Akun {$user->nama} berhasil {$label}.");
    }

    /* ------------------------------------------------------------------ */
    /*  DESTROY                                                             */
    /* ------------------------------------------------------------------ */
    public function destroy(User $user)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya super admin yang dapat menghapus akun.');
        }

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
        // Admin tidak boleh mereset password akun super_admin
        if (! $this->canActOn($user)) {
            abort(403, 'Anda tidak memiliki izin untuk mereset password akun ini.');
        }

        $request->validate([
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', "Password akun {$user->nama} berhasil direset.");
    }
}