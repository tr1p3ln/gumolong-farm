<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | {{ config('app.name', 'Gumolong Farm') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        html, body { height: 100%; overflow: hidden; }
        @media (max-width: 767px) { html, body { overflow: auto; } }
    </style>
</head>
<body class="bg-[#FAFAF7] text-[#1F2937] font-sans antialiased">

<main class="flex h-screen w-full">

    {{-- ═══════════════════════════════════════════
         LEFT PANEL — Hero (Desktop only, 40%)
    ═══════════════════════════════════════════ --}}
    <section class="hidden md:flex w-[40%] relative items-end p-12 overflow-hidden">

        {{-- Background image + overlay --}}
        <div class="absolute inset-0 z-0">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCPfG7sfLi6rT4ZqxpzYM-N_lNfJ7VjNB9ExrF9kBolX7-KSJ0NWrU8aoXkCDZhHBbnapT3AP-gPdFjKO-9scn2EKMP6pnbuIWmXKy1wkjXcFbVa0jWSCsFIFKnHMTNlQ6-o4SHKgZpoL_c1toLWdBuTtahnUT1nb5H1hCtldcnbnWTPsyi35exIGIxLZyvaZDIDarjmDL4n3qJYluGh5_3XRKFLWCL47zEM0Th9joIT7v5D6iPxIrhOYkm9-BSNap5i-Y6_7hTgasf"
                 alt="Domba merumput di padang hijau"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px]"></div>
        </div>

        {{-- Tagline --}}
        <div class="relative z-10 max-w-md">
            <h1 class="text-white text-5xl font-bold tracking-tighter leading-tight">
                Kelola Peternakan Anda dengan Cerdas
            </h1>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         RIGHT PANEL — Login Card (60% / full mobile)
    ═══════════════════════════════════════════ --}}
    <section class="flex-1 bg-[#FAFAF7] flex items-center justify-center p-6 md:p-12 overflow-y-auto">
        <div class="w-full max-w-md space-y-8">

            {{-- ── Login Card ── --}}
            <div class="bg-white rounded-xl shadow-[0_32px_64px_-12px_rgba(26,28,27,0.06)] p-10 border border-gray-100">

                {{-- Header: Logo + Brand --}}
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-[60px] h-[60px] border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center mb-4 rounded">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Logo</span>
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-[#1F2937]">GUMOLONG FARM</h2>
                    <p class="text-sm text-gray-400 font-normal">Sistem Informasi Manajemen Peternakan</p>
                </div>

                {{-- Session Status (e.g. password reset link sent) --}}
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Error Alert — shown on failed login --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-white border border-gray-200 border-l-[4px] border-l-[#B14B6F] rounded-lg flex gap-3 items-start">
                        <div class="flex items-center justify-center w-5 h-5 rounded-full bg-[#B14B6F] shrink-0 mt-0.5">
                            <span class="text-white text-[12px] font-bold leading-none">!</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 leading-none mb-1">Login Gagal</p>
                            <p class="text-xs text-gray-500 leading-tight">
                                Email atau password salah. Silakan coba lagi.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ── Form ── --}}
                <form method="POST"
                      action="{{ route('login') }}"
                      class="space-y-6"
                      x-data="{ showPassword: false }">
                    @csrf

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label for="email"
                               class="block text-[10px] uppercase tracking-widest font-bold text-gray-500">
                            Email atau Username
                            <span class="text-[#B14B6F]">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none
                                        text-gray-400 group-focus-within:text-[#2E7D32] transition-colors">
                                <span class="material-symbols-outlined text-xl">mail</span>
                            </div>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="contoh: admin@gumolong.com"
                                   class="block w-full pl-11 pr-4 py-3
                                          bg-[#F4F4F1] border border-transparent
                                          focus:border-[#2E7D32]/20 focus:bg-white focus:ring-4 focus:ring-[#2E7D32]/5
                                          rounded-xl transition-all
                                          placeholder:text-gray-400 text-[#1F2937]
                                          @error('email') border-[#B14B6F] @enderror">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <label for="password"
                                   class="block text-[10px] uppercase tracking-widest font-bold text-gray-500">
                                Password
                                <span class="text-[#B14B6F]">*</span>
                            </label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none
                                        text-gray-400 group-focus-within:text-[#2E7D32] transition-colors">
                                <span class="material-symbols-outlined text-xl">lock</span>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="block w-full pl-11 pr-11 py-3
                                          bg-[#F4F4F1] border border-transparent
                                          focus:border-[#2E7D32]/20 focus:bg-white focus:ring-4 focus:ring-[#2E7D32]/5
                                          rounded-xl transition-all
                                          placeholder:text-gray-400 text-[#1F2937]
                                          @error('password') border-[#B14B6F] @enderror">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center
                                           text-gray-400 hover:text-[#2E7D32] transition-colors">
                                <span class="material-symbols-outlined text-xl"
                                      x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                    </div>

                    {{-- CTA Button --}}
                    <button type="submit"
                            class="w-full bg-[#2E7D32] hover:bg-[#256427]
                                   py-4 rounded-xl
                                   text-white font-bold tracking-tight
                                   shadow-lg shadow-[#2E7D32]/20
                                   hover:scale-[1.02] active:scale-95
                                   transition-all duration-200">
                        Masuk
                    </button>
                </form>

                {{-- Divider --}}
                <div class="my-8 border-t border-dashed border-gray-200"></div>

                {{-- Helper Text --}}
                <p class="text-center italic text-xs text-gray-500 leading-relaxed">
                    Halaman ini hanya untuk Owner &amp; Admin. Lupa akses? Hubungi Administrator sistem.
                </p>

                {{-- Demo Credentials Box --}}
                <div class="mt-6 border border-dashed border-gray-200 rounded-xl overflow-hidden">

                    {{-- Header --}}
                    <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-dashed border-gray-200">
                        <span class="material-symbols-outlined text-gray-400 text-base">key</span>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            Demo Credentials
                        </p>
                    </div>

                    {{-- Credential rows --}}
                    <div class="divide-y divide-dashed divide-gray-100">

                        {{-- Super Admin --}}
                        <div class="px-4 py-3 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-[#1F2937] uppercase tracking-wider">Super Admin</p>
                                <p class="text-[11px] text-gray-500 font-mono truncate">admin@gumolong.farm</p>
                            </div>
                            <button type="button"
                                    onclick="fillCredential('admin@gumolong.farm','admin123')"
                                    class="shrink-0 text-[10px] font-bold text-[#2E7D32] border border-[#2E7D32]/30
                                           px-2 py-1 rounded-lg hover:bg-[#2E7D32]/5 transition-colors">
                                Gunakan
                            </button>
                        </div>

                        {{-- Admin --}}
                        <div class="px-4 py-3 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-[#1F2937] uppercase tracking-wider">Admin</p>
                                <p class="text-[11px] text-gray-500 font-mono truncate">operasional@gumolong.farm</p>
                            </div>
                            <button type="button"
                                    onclick="fillCredential('operasional@gumolong.farm','admin123')"
                                    class="shrink-0 text-[10px] font-bold text-blue-500 border border-blue-300
                                           px-2 py-1 rounded-lg hover:bg-blue-50 transition-colors">
                                Gunakan
                            </button>
                        </div>

                        {{-- Kepala Kandang --}}
                        <div class="px-4 py-3 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-[#607F5B] shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-[#1F2937] uppercase tracking-wider">Kepala Kandang</p>
                                <p class="text-[11px] text-gray-500 font-mono truncate">kepala@gumolong.farm</p>
                            </div>
                            <button type="button"
                                    onclick="fillCredential('kepala@gumolong.farm','kepala123')"
                                    class="shrink-0 text-[10px] font-bold text-[#607F5B] border border-[#607F5B]/30
                                           px-2 py-1 rounded-lg hover:bg-[#607F5B]/5 transition-colors">
                                Gunakan
                            </button>
                        </div>

                        {{-- Pengurus Kandang --}}
                        <div class="px-4 py-3 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-bold text-[#1F2937] uppercase tracking-wider">Pengurus Kandang</p>
                                <p class="text-[11px] text-gray-500 font-mono truncate">kandang@gumolong.farm</p>
                            </div>
                            <button type="button"
                                    onclick="fillCredential('kandang@gumolong.farm','kandang123')"
                                    class="shrink-0 text-[10px] font-bold text-amber-500 border border-amber-300
                                           px-2 py-1 rounded-lg hover:bg-amber-50 transition-colors">
                                Gunakan
                            </button>
                        </div>

                    </div>

                    {{-- Footer note --}}
                    <div class="px-4 py-2.5 bg-gray-50 border-t border-dashed border-gray-200">
                        <p class="text-[10px] text-gray-400 text-center">
                            Klik "Gunakan" untuk mengisi form otomatis
                        </p>
                    </div>
                </div>
            </div>

            {{-- Footer Trademark --}}
            <div class="text-center">
                <p class="text-[11px] text-gray-400">
                    &copy; 2024 GUMOLONG FARM &mdash; Sistem Informasi Manajemen Peternakan
                </p>
            </div>

        </div>
    </section>

</main>
<script>
function fillCredential(email, password) {
    document.getElementById('email').value    = email;
    document.getElementById('password').value = password;
    document.getElementById('email').dispatchEvent(new Event('input'));
}
</script>
</body>
</html>
