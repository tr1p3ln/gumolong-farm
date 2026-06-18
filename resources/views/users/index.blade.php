{{-- resources/views/manajemen-user/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen User')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@section('content')
<div class="mu-page">

    {{-- ======================== HEADER ======================== --}}
    <div class="mu-header">
        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; Manajemen User
        </div>
        <div class="mu-header__top">
            <div>
                <h1>Manajemen User</h1>
                <p>Kelola akun, role, dan status akses pengguna sistem</p>
            </div>
            <button class="btn-primary" onclick="openModal('modal-tambah')">
                &#43; Tambah User
            </button>
        </div>
    </div>

    <div class="mu-summary-grid">
        <div class="summary-card">
            <div class="summary-card__icon summary-card__icon--total"><i class="bi bi-people-fill"></i></div>
            <div class="summary-card__body">
                <span class="summary-card__label">Total User</span>
                <span class="summary-card__value" data-summary="total">{{ $summary['total'] }}</span>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card__icon summary-card__icon--aktif"><i class="bi bi-person-check-fill"></i></div>
            <div class="summary-card__body">
                <span class="summary-card__label">Akun Aktif</span>
                <span class="summary-card__value" data-summary="aktif">{{ $summary['aktif'] }}</span>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card__icon summary-card__icon--nonaktif"><i class="bi bi-person-x-fill"></i></div>
            <div class="summary-card__body">
                <span class="summary-card__label">Nonaktif</span>
                <span class="summary-card__value" data-summary="nonaktif">{{ $summary['nonaktif'] }}</span>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card__icon summary-card__icon--admin"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="summary-card__body">
                <span class="summary-card__label">Admin & Super</span>
                <span class="summary-card__value" data-summary="admin">{{ $summary['admin'] }}</span>
            </div>
        </div>
    </div>

    {{-- ======================== FILTER BAR ======================== --}}
    <div class="mu-card mu-filter-bar">
        <form method="GET" action="{{ route('users.index') }}" class="filter-form">
            <div class="filter-form__search">
                <span class="filter-form__search-icon"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-input form-input--search"
                       placeholder="Cari nama, email, atau nomor HP..."
                       value="{{ request('search') }}">
            </div>
            <select name="role" class="form-select form-select--inline">
                <option value="">Semua Role</option>
                @foreach($roleOptions as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>
                    {{ ucfirst(str_replace('_', ' ', $r)) }}
                </option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select--inline">
                <option value="">Semua Status</option>
                <option value="aktif"    @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>
            <button type="submit" class="btn-filter">Terapkan</button>
            @if(request()->hasAny(['search','role','status']))
            <a href="{{ route('users.index') }}" class="btn-reset"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            @endif
        </form>
    </div>

    {{-- ======================== USER TABLE ======================== --}}
    <div class="mu-card">
        <div class="mu-card__header">
            <div>
                <h2>Daftar Pengguna</h2>
                <p>{{ $users->total() }} user ditemukan</p>
            </div>
        </div>

        <div class="mu-table-wrap">
            <table class="mu-table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Nomor HP</th>
                        <th>Login Terakhir</th>
                        <th class="mu-table__th--action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)

                    {{--
                        Tentukan apakah aktor yang login boleh melakukan aksi terhadap baris ini.
                        - super_admin : boleh aksi ke semua user
                        - admin       : boleh aksi ke user non-super_admin saja
                    --}}
                    @php
                        $isSelf       = $user->user_id === auth()->id();
                        $canAct       = auth()->user()->isSuperAdmin() || ! $user->isSuperAdmin();
                    @endphp

                    <tr class="{{ $user->status === 'nonaktif' ? 'mu-table__row--disabled' : '' }}">

                        {{-- Pengguna --}}
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar user-avatar--{{ $user->role }}">
                                    @if($user->foto_profile)
                                        <img src="{{ Storage::url($user->foto_profile) }}"
                                             alt="{{ $user->nama }}">
                                    @else
                                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="user-cell__info">
                                    <span class="user-cell__nama">{{ $user->nama }}</span>
                                    <span class="user-cell__email">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td>
                            <span class="role-badge role-badge--{{ Str::slug($user->role, '_') }}">
                                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-badge status-badge--{{ $user->status }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>

                        {{-- Nomor HP --}}
                        <td class="mu-table__hp">{{ $user->nomor_hp ?? '-' }}</td>

                        {{-- Login Terakhir --}}
                        <td class="mu-table__last-login">
                            @if($user->last_login)
                                <div>{{ $user->last_login->format('d M Y') }}</div>
                                <div class="mu-table__time">{{ $user->last_login->format('H:i') }}</div>
                            @else
                                <span class="mu-table__never">Belum pernah</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td>
                            <div class="action-group">

                                {{-- Toggle status --}}
                                {{-- Ditampilkan jika: aktor boleh aksi pada target DAN bukan diri sendiri --}}
                                @if($canAct && ! $isSelf)
                                <form method="POST"
                                      action="{{ route('users.toggle-status', $user->user_id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn-icon btn-icon--{{ $user->status === 'aktif' ? 'disable' : 'enable' }}"
                                            title="{{ $user->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi {{ $user->status === 'aktif' ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                                    </button>
                                </form>
                                @else
                                {{-- Placeholder kosong agar layout tidak bergeser --}}
                                <span class="btn-icon btn-icon--disabled" title="Tidak tersedia">
                                    <i class="bi bi-dash"></i>
                                </span>
                                @endif

                                {{-- Edit --}}
                                {{-- Ditampilkan jika aktor boleh aksi pada target --}}
                                @if($canAct)
                                <button class="btn-icon btn-icon--edit"
                                        title="Edit"
                                        data-user='@json($user)'
                                        onclick="openEditModal({{ $user->user_id }}, JSON.parse(this.dataset.user))">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @else
                                <span class="btn-icon btn-icon--disabled" title="Tidak tersedia">
                                    <i class="bi bi-dash"></i>
                                </span>
                                @endif

                                {{-- Reset Password --}}
                                {{-- Ditampilkan jika aktor boleh aksi pada target --}}
                                @if($canAct)
                                <button class="btn-icon btn-icon--reset"
                                        title="Reset Password"
                                        onclick="openResetModal({{ $user->user_id }}, '{{ $user->nama }}')">
                                    <i class="bi bi-key-fill"></i>
                                </button>
                                @else
                                <span class="btn-icon btn-icon--disabled" title="Tidak tersedia">
                                    <i class="bi bi-dash"></i>
                                </span>
                                @endif

                                {{-- Hapus — hanya super_admin --}}
                                @if(auth()->user()->isSuperAdmin() && ! $isSelf)
                                <button class="btn-icon btn-icon--delete"
                                        title="Hapus"
                                        onclick="openDeleteModal({{ $user->user_id }}, '{{ $user->nama }}')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="mu-table__empty">
                            Tidak ada user yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->lastPage() > 1)
        <div class="sp-pagination">
            @if($users->onFirstPage())
                <span class="sp-pagination__disabled"><i class="bi bi-chevron-left"></i></span>
            @else
                <a href="{{ $users->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
            @endif

            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="sp-pagination__active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="sp-pagination__disabled"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>
        @endif
    </div>

</div>

{{-- ======================== MODAL: TAMBAH USER ======================== --}}
<div class="modal-backdrop" id="modal-tambah">
    <div class="modal-box">
        <div class="modal-box__header">
            <h3><i class="bi bi-plus-lg"></i>Tambah User Baru</h3>
            <button class="modal-box__close" onclick="closeModal('modal-tambah')"><i class="bi bi-x-lg"></i></button>
        </div>
        <p>Buat akun baru untuk pengguna sistem</p>
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-grid">
                <div class="modal-form-group modal-form-group--full">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input"
                           placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="modal-form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-input"
                           placeholder="email@domain.com" required>
                </div>
                <div class="modal-form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="nomor_hp" class="form-input"
                           placeholder="08xxxxxxxxxx">
                </div>
                <div class="modal-form-group">
                    <label>Role <span class="required">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roleOptions as $r)
                            @if($r === 'super_admin' && ! auth()->user()->isSuperAdmin())
                                @continue
                            @endif
                            <option value="{{ $r }}">{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Status <span class="required">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-input"
                           placeholder="Min. 8 karakter" required>
                    <div class="form-hint">Kombinasi huruf besar, huruf kecil, dan angka</div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-tambah')">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== MODAL: EDIT USER ======================== --}}
<div class="modal-backdrop" id="modal-edit">
    <div class="modal-box">
        <div class="modal-box__header">
            <h3>&#9998; Edit User</h3>
            <button class="modal-box__close" onclick="closeModal('modal-edit')">&#10005;</button>
        </div>
        <p>Perbarui informasi akun pengguna</p>
        <form method="POST" id="form-edit" action="">
            @csrf
            @method('PUT')
            <div class="modal-grid">
                <div class="modal-form-group modal-form-group--full">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama" id="edit-nama" class="form-input" required>
                </div>
                <div class="modal-form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" id="edit-email" class="form-input" required>
                </div>
                <div class="modal-form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="nomor_hp" id="edit-nomor-hp" class="form-input">
                </div>
                <div class="modal-form-group">
                    <label>Role <span class="required">*</span></label>
                    <select name="role" id="edit-role" class="form-select" required>
                        @foreach($roleOptions as $r)
                            @if($r === 'super_admin' && ! auth()->user()->isSuperAdmin())
                                @continue
                            @endif
                            <option value="{{ $r }}">{{ ucfirst(str_replace('_',' ',$r)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <label>Status <span class="required">*</span></label>
                    <select name="status" id="edit-status" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="modal-form-group modal-form-group--full">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-input"
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <div class="form-hint">Isi hanya jika ingin mengganti password</div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== MODAL: RESET PASSWORD ======================== --}}
<div class="modal-backdrop" id="modal-reset">
    <div class="modal-box modal-box--sm">
        <div class="modal-box__header">
            <h3>&#128273; Reset Password</h3>
            <button class="modal-box__close" onclick="closeModal('modal-reset')">&#10005;</button>
        </div>
        <p id="reset-desc">Set password baru untuk akun ini</p>
        <form method="POST" id="form-reset" action="">
            @csrf
            @method('PATCH')
            <div class="modal-form-group">
                <label>Password Baru <span class="required">*</span></label>
                <input type="password" name="password" class="form-input"
                       placeholder="Min. 8 karakter" required>
            </div>
            <div class="modal-form-group">
                <label>Konfirmasi Password <span class="required">*</span></label>
                <input type="password" name="password_confirmation" class="form-input"
                       placeholder="Ulangi password" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-reset')">Batal</button>
                <button type="submit" class="btn-primary">Reset Password</button>
            </div>
        </form>
    </div>
</div>

{{-- ======================== MODAL: HAPUS USER ======================== --}}
<div class="modal-backdrop" id="modal-hapus">
    <div class="modal-box modal-box--sm modal-box--danger">
        <div class="modal-box__header">
            <h3>&#128465; Hapus User</h3>
            <button class="modal-box__close" onclick="closeModal('modal-hapus')">&#10005;</button>
        </div>
        <p id="hapus-desc">Akun ini akan dihapus secara permanen dan tidak dapat dikembalikan.</p>
        <form method="POST" id="form-hapus" action="">
            @csrf
            @method('DELETE')
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal-hapus')">Batal</button>
                <button type="submit" class="btn-primary btn-primary--danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    @vite('resources/js/manajemen-user/index.js')
@endpush
@endsection