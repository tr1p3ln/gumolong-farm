---
name: Project Gumolong Overview
description: Laravel 11 farm management app — tech stack, architecture, and test coverage summary
type: project
---

Laravel 11 farm management app for Gumolong farm.

**Stack:** PHP 8.2, Laravel 11, PostgreSQL 16, Blade + Alpine.js + Tailwind CSS

**Database:** gumolong_db (PostgreSQL), user: gumolong_user

**Roles:** super_admin, admin, kepala_kandang, pengurus_kandang

**Architecture:** Web routes (admin/KK) + Mobile routes (/pk/*, /kk/*) for field workers

**Models:** User (table: user, PK: user_id), Domba (PK: ear_tag_id, soft deletes), Kandang, TugasHarian, TemplateTugasRutin, Penimbangan

**Key modules:**
- Auth: login with role-based redirect, no public registration (or admin-managed)
- Domba: CRUD + ear tag auto-generation (J-### jantan, B-### betina) with pg_advisory_lock
- Kandang: CRUD with capacity validation
- Tugas Harian: task management with status lifecycle, bulk reassign, generate from templates
- Template Tugas Rutin: recurring task templates with toggle
- Mobile PK: /pk/* routes for pengurus_kandang (field workers)
- Mobile KK: /kk/* routes for kepala_kandang (supervisor)

**Testing (as of 2026-05-04):**
- 168 tests, 366 assertions, all PASSED
- Using DatabaseTransactions (NOT RefreshDatabase) to protect production data
- Factories: UserFactory, KandangFactory, DombaFactory, TugasHarianFactory, TemplateTugasRutinFactory

**Known issues fixed during testing:**
- UserFactory used `name` instead of `nama` (fixed)
- ProfileUpdateRequest used `name` + `->id` (fixed to `nama` + `getKey()`)
- RegisteredUserController used `name` (fixed to `nama`)
- TugasHarian scopeKandang shadowed by kandang() relationship (noted, scope unreachable)
- password_reset_tokens table missing (migration created)
- User model missing HasFactory trait (added)
- TugasHarian/TemplateTugasRutin missing HasFactory trait (added)
