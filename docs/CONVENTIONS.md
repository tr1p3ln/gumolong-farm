# Konvensi Coding & Workflow

## Naming Convention

| Komponen | Konvensi | Contoh |
|---|---|---|
| Controller | PascalCase + suffix `Controller` | `DombaController` |
| Model | Singular PascalCase | `Domba` (bukan `Dombas`) |
| Migration | snake_case + plural table | `create_domba_table` |
| Route | kebab-case | `stok-pakan`, `daily-task` |
| View folder | kebab-case | `resources/views/stok-pakan/` |
| Blade file | kebab-case | `data-table.blade.php` |
| Enum | Singular PascalCase | `StatusDomba` |
| Service | PascalCase + suffix `Service` | `InbreedingService` |

## Git Workflow

- Branch fitur: `feature/[kode-modul]-[deskripsi-singkat]`
  - Contoh: `feature/a03-data-domba-crud`, `feature/a09-konfirmasi-kebuntingan`
- Branch bugfix: `fix/[deskripsi-singkat]`
- **Selalu branch dari `develop`**, bukan `main`.
- **Tidak boleh push langsung ke `main` atau `develop`.**

## Commit Message (Conventional Commits)

| Prefix | Kapan dipakai |
|---|---|
| `feat` | Fitur baru |
| `fix` | Bug fix |
| `refactor` | Refactor tanpa ubah behavior |
| `docs` | Dokumentasi |
| `chore` | Maintenance (update dependency, dll) |
| `style` | Format/spacing only |
| `test` | Tambah/update tests |

Contoh: `feat(domba): add ear tag validation in form`

## Pull Request Rules

- Minimum **1 approver** dari anggota lain.
- Jalankan `./vendor/bin/pint` sebelum push (format code otomatis).
- PR description wajib mengisi template (lihat `.github/PULL_REQUEST_TEMPLATE.md`).
- Resolve semua conversation reviewer sebelum merge.
- Gunakan **squash merge** (bukan merge commit) agar history bersih.

## Code Style

- Laravel Pint dengan preset default `laravel`.
- Tidak perlu config tambahan.
- Jalankan sebelum setiap commit:
  ```bash
  ./vendor/bin/pint
  ```
