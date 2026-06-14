<p align="center">
  <h1 align="center">Laporan Generator</h1>
</p>

<p align="center">
  <a href="https://pandoc.org"><img src="https://img.shields.io/badge/Pandoc-3.0+-blue?style=for-the-badge&logo=markdown" alt="Pandoc"></a>
  <a href="https://www.latex-project.org"><img src="https://img.shields.io/badge/LaTeX-pdflatex-008080?style=for-the-badge&logo=latex" alt="LaTeX"></a>
  <a href="https://imagemagick.org"><img src="https://img.shields.io/badge/ImageMagick-7.0+-orange?style=for-the-badge&logo=imagemagick" alt="ImageMagick"></a>
  <a href="https://www.gnu.org/software/bash"><img src="https://img.shields.io/badge/Shell-Bash-4EAA25?style=for-the-badge&logo=gnu-bash" alt="Shell"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"></a>
</p>

<p align="left">
Generator laporan akademik otomatis yang menghasilkan PDF dengan format rapi menggunakan AI CLI/IDE dikombinasikan dengan Pandoc + LaTeX. AI membaca project Anda, bertanya satu per satu, menulis semua bab, lalu satu perintah build menghasilkan PDF siap kumpul.
</p>

---

## Daftar Isi

- [Fitur](#fitur)
- [Kategori Laporan](#kategori-laporan)
- [Cara Kerja](#cara-kerja)
- [AI CLI/IDE yang Didukung](#ai-cliide-yang-didukung)
- [Persyaratan](#persyaratan)
- [Panduan Cepat](#panduan-cepat)
- [Struktur Project](#struktur-project)
- [Kustomisasi](#kustomisasi)
- [Tanya Jawab](#tanya-jawab)
- [Lisensi](#lisensi)

---

## Fitur

- **Konten digenerate oleh AI** -- Prompt (`prompt.md`) menginstruksikan AI untuk membaca project Anda, bertanya secara interaktif, dan menulis konten setiap bab.
- **Format akademik standar Indonesia** -- Times New Roman 12pt, A4, spasi 1.5, indent 1.5cm, margin 2.5cm/3cm.
- **Penomoran otomatis** -- Angka Romawi untuk BAB (BAB I, BAB II), Arab untuk sub bab (1.1, 2.1) dan sub-sub bab (1.1.1).
- **Daftar Isi otomatis** -- DAFTAR ISI dengan entri BAB tebal, diposisikan manual sesuai format Indonesia.
- **Daftar Pustaka APA** -- Format APA dengan referensi nyata yang dicari AI dari internet.
- **Dukungan screenshot** -- Sematkan gambar dengan figure full-width dan caption Indonesia (Gambar 4.1). Penghapusan saluran alpha otomatis.
- **Multi-OS/Distro** -- Bekerja di Linux (Ubuntu/Debian, Fedora/RHEL, Arch), macOS, dan Windows (via WSL). Perintah install disesuaikan dengan OS/Distro.
- **Fleksibel font** -- Default Times New Roman (via Nimbus Serif). Bisa ganti font lain dengan memberi tahu AI.
- **4 varian laporan** -- Coding (6 bab), Non-coding/PKL (5 bab), General/Makalah (3 bab), Ilmiah/Skripsi (5 bab + abstrak 2 bahasa).

---

## Kategori Laporan

| Kategori | Folder | Jumlah Bab | Target |
|----------|--------|-----------|--------|
| **Coding / IT** | `coding/` | 6 | Project aplikasi web, mobile, API, sistem informasi, atau IT lainnya. AI membaca source code project Anda. |
| **Non-coding / PKL / Magang** | `non-coding/` | 5 | PKL SMK, magang mahasiswa, praktik kerja industri, kegiatan non-IT. |
| **General / Makalah** | `general/` | 3 | Tugas kuliah, kajian pustaka, paper sederhana tanpa penelitian lapangan. |
| **Ilmiah / Skripsi** | `ilmiah/` | 5 + abstrak | Skripsi, tesis, artikel jurnal, paper konferensi, laporan penelitian formal. Abstrak dua bahasa (Indonesia + Inggris). |

---

## Cara Kerja

Ada 2 alur kerja tergantung kategori laporan:

### Alur Coding

```
User clone repo ini
        |
        v
User salin coding/prompt.md ke AI CLI/IDE
        |
        v
AI clone project user, baca source code, pahami struktur & fitur
        |
        v
AI bertanya satu per satu (judul, OS/Distro, screenshot, dll)
        |
        v
AI tulis semua file .md + template.latex + build.sh di folder laporan/
        |
        v
User jalankan: cd laporan && ./build.sh
        |
        v
PDF jadi (laporan-project.pdf)
```

### Alur Non-coding / General / Ilmiah

```
User clone repo ini
        |
        v
User salin {kategori}/prompt.md ke AI CLI/IDE
        |
        v
AI bertanya satu per satu ke user (topik, tempat magang, judul, OS/Distro, dll)
        |
        v
AI tulis semua file .md + template.latex + build.sh di folder laporan/
        |
        v
User jalankan: cd laporan && ./build.sh
        |
        v
PDF jadi (laporan-project.pdf)
```

Seluruh proses berjalan interaktif. AI bertanya satu per satu, tidak pernah membuang semua pertanyaan sekaligus. Ini memastikan konten sesuai persis dengan project Anda.

---

## AI CLI/IDE yang Didukung

Tools ini dirancang khusus untuk AI yang bisa membaca struktur project dan file di lokal. Tidak bekerja dengan chatbot web (ChatGPT.com, Claude.ai, Gemini.app).

| Tool | Tipe | Keterangan |
|------|------|-----------|
| [OpenCode](https://github.com/anomalyco/opencode) | CLI (Terminal) | Open source, 150k+ stars, 75+ provider, MIT |
| [Claude Code](https://code.claude.com/) | CLI (Terminal) | Dari Anthropic, agentic, auto-commit |
| [Gemini CLI](https://github.com/google-gemini/gemini-cli) | CLI (Terminal) | Dari Google, open source, free tier 1k req/hari |
| [Codex CLI](https://github.com/openai/codex) | CLI (Terminal) | Dari OpenAI, sandboxed, Rust-native |
| [Antigravity CLI](https://github.com/antigravity-ai/antigravity-cli) | CLI (Terminal) | Anthropic API, open source, auto-commit, fork of Claude Code |
| [OpenClaude](https://github.com/OpenClaude/OpenClaude) | CLI (Terminal) | Open source, multi-model, Agentic Code, terminal UI |
| [Aider](https://aider.chat/) | CLI (Terminal) | Open source, git-native, 100+ bahasa |
| [Cursor](https://cursor.sh) | IDE | Agent mode, bisa baca project penuh |
| [Windsurf](https://codeium.com/windsurf) | IDE | Cascade mode, agentic |
| [Trae](https://trae.ai) | IDE | Dari ByteDance, agent mode, built-in terminal |
| [Cline](https://cline.bot) | IDE Extension (VS Code) | Open source, plan/act mode, MCP |
| [Roo Code](https://github.com/roocode/roocode) | IDE Extension (VS Code) | Multi-mode, custom personas |
| [Continue](https://continue.dev) | IDE Extension (VS Code + JetBrains) | Open source, tab autocomplete + chat |
| **Any AI CLI/IDE** | -- | Pada prinsipnya, **semua tools AI yang bisa membaca file dan menjalankan perintah terminal** bisa menggunakan prompt ini. |

---

## Persyaratan

### Tools Utama

| Tool | Fungsi | Instalasi |
|------|--------|-----------|
| Pandoc | Konversi Markdown ke LaTeX | Sesuai OS/Distro (lihat bawah) |
| pdflatex | Engine PDF LaTeX | Bagian dari TeX Live |
| ImageMagick | Hapus saluran alpha dari PNG | Sesuai OS/Distro (lihat bawah) |
| Bash | Menjalankan build script | Sudah terinstall di Linux/macOS |

### Instalasi Per OS/Distro

**Ubuntu / Debian / Linux Mint / Pop!_OS** (apt)
```bash
sudo apt update
sudo apt install pandoc texlive-latex-base texlive-fonts-recommended texlive-latex-extra texlive-fonts-extra imagemagick
```

**Fedora / RHEL / CentOS** (dnf)
```bash
sudo dnf install pandoc texlive-scheme-medium texlive-fonts-extra imagemagick
```

**Arch Linux / Manjaro / EndeavourOS** (pacman)
```bash
sudo pacman -S pandoc texlive-core texlive-fontsextra imagemagick
```

**macOS** (Homebrew)
```bash
brew install pandoc imagemagick
brew install --cask mactex   # Distribusi TeX Live
```

**Windows (native)**
- Install Pandoc dari [pandoc.org](https://pandoc.org/installing.html)
- Install MiKTeX dari [miktex.org](https://miktex.org)
- Install ImageMagick dari [imagemagick.org](https://imagemagick.org/script/download.php)
- Gunakan WSL untuk menjalankan `build.sh`

### Setup Font (Satu Kali)

Font default adalah Times New Roman via Nimbus Serif. Definisi font yang sudah di-patch tertanam di `build.sh` dan file encoding diperlukan:

```bash
cp /usr/share/texmf-dist/fonts/enc/dvips/fontools/fontools_ts1.enc /tmp/fontools_ts1.enc 2>/dev/null || \
  echo "\\DeclareTextCommand{\\textcircled}{TS1}{\\hmode@b\textcircled@}" > /tmp/fontools_ts1.enc
```

Jika ingin font lain, beri tahu AI saat ditanya. AI akan memperbarui `template.latex` dan `build.sh` sesuai.

---

## Panduan Cepat

### Coding / IT

Gunakan `coding/prompt.md` untuk project pemrograman.

```
your-project/
├── src/                # Source code project Anda
├── package.json        # (atau Cargo.toml, gemspec, dll)
├── README.md
└── laporan/
    ├── build.sh
    ├── template.latex
    ├── cover.md
    ├── logo-kampus.jpg
    ├── gambar/
    ├── 01-pendahuluan.md
    ├── 02-landasan-teori.md
    ├── 03-analisis-perancangan.md
    ├── 04-implementasi.md
    ├── 05-hasil-pengujian.md
    ├── 06-penutup.md
    └── daftar-pustaka.md
```

| Bab | Judul |
|-----|-------|
| BAB I | PENDAHULUAN (Latar Belakang, Rumusan Masalah, Batasan, Tujuan, Manfaat) |
| BAB II | LANDASAN TEORI (teori pendukung, framework, library yang dipakai) |
| BAB III | ANALISIS DAN PERANCANGAN (use case, flowchart, arsitektur, wireframe) |
| BAB IV | IMPLEMENTASI (screenshot, kode, konfigurasi) |
| BAB V | HASIL DAN PENGUJIAN (blackbox testing, hasil screenshot, analisis) |
| BAB VI | PENUTUP (kesimpulan, saran) |

### Non-coding / PKL / Magang

Gunakan `non-coding/prompt.md` untuk kegiatan non-IT.

```
laporan-pkl/
├── logo-kampus.jpg
├── gambar/                     # Foto kegiatan
└── laporan/
    ├── build.sh
    ├── template.latex
    ├── cover.md
    ├── 01-pendahuluan.md
    ├── 02-profil-perusahaan.md
    ├── 03-pelaksanaan.md
    ├── 04-hasil.md
    ├── 05-penutup.md
    └── daftar-pustaka.md
```

| Bab | Judul |
|-----|-------|
| BAB I | PENDAHULUAN (Latar Belakang, Tujuan, Manfaat, Waktu dan Tempat) |
| BAB II | PROFIL TEMPAT MAGANG / PERUSAHAAN (sejarah, struktur organisasi, dokumentasi) |
| BAB III | PELAKSANAAN KEGIATAN (uraian kegiatan, metode, jadwal, foto dokumentasi) |
| BAB IV | HASIL DAN PEMBAHASAN (hasil kegiatan, analisis, temuan) |
| BAB V | PENUTUP (kesimpulan, saran) |

### General / Makalah

Gunakan `general/prompt.md` untuk kajian pustaka.

```
laporan-makalah/
└── laporan/
    ├── build.sh
    ├── template.latex
    ├── cover.md
    ├── 01-pendahuluan.md
    ├── 02-pembahasan.md
    ├── 03-penutup.md
    └── daftar-pustaka.md
```

| Bab | Judul |
|-----|-------|
| BAB I | PENDAHULUAN (Latar Belakang, Rumusan Masalah, Tujuan) |
| BAB II | PEMBAHASAN (kajian pustaka, analisis teori, sintesis) |
| BAB III | PENUTUP (kesimpulan, saran) |

### Ilmiah / Skripsi

Gunakan `ilmiah/prompt.md` untuk karya ilmiah formal.

```
laporan-skripsi/
└── laporan/
    ├── build.sh
    ├── template.latex
    ├── cover.md
    ├── abstrak.md
    ├── abstract.md
    ├── 01-pendahuluan.md
    ├── 02-landasan-teori.md
    ├── 03-metodologi.md
    ├── 04-hasil.md
    ├── 05-penutup.md
    └── daftar-pustaka.md
```

| Bab | Judul |
|-----|-------|
| Abstrak | Abstrak Bahasa Indonesia |
| Abstract | Abstract English |
| BAB I | PENDAHULUAN (Latar Belakang, Rumusan Masalah, Tujuan, Manfaat) |
| BAB II | LANDASAN TEORI / TINJAUAN PUSTAKA |
| BAB III | METODOLOGI (metode penelitian, populasi, instrumen, analisis) |
| BAB IV | HASIL DAN PEMBAHASAN (temuan, analisis data, pembahasan) |
| BAB V | PENUTUP (kesimpulan, saran) |

**Catatan**: Karya Ilmiah menggunakan footnote, abstrak dua bahasa (Indonesia + Inggris) di halaman terpisah, dan daftar pustaka APA style.

---

## Struktur Project

```
laporan-generator/
├── README.md               # File ini
├── LICENSE                 # Lisensi MIT
├── .gitignore              # Abaikan PDF dan file sisa LaTeX
├── coding/
│   └── prompt.md           # Template untuk project coding/IT (6 bab)
├── non-coding/
│   └── prompt.md           # Template untuk PKL/magang (5 bab)
├── general/
│   └── prompt.md           # Template untuk makalah (3 bab)
├── ilmiah/
│   └── prompt.md           # Template untuk skripsi/ilmiah (5 bab + abstrak)
└── template/
    ├── template.latex      # Template LaTeX (font, margin, penomoran, TOC)
    └── build.sh            # Script build (gabung file .md, jalankan pandoc)
```

---

## Kustomisasi

### Mengganti Font

Font default adalah Times New Roman (via Nimbus Serif). Saat AI bertanya "Mau font apa?", Anda bisa menentukan font lain. AI akan memperbarui:
- `template.latex` -- mengubah baris `\rmdefault` dan `\pdfmapfile`
- `build.sh` -- memperbarui definisi font terkait jika diperlukan

### Mengubah Judul Bab

Secara default, setiap kategori punya struktur bab tetap. AI akan menanyakan apakah Anda ingin menyesuaikan judul-judul ini.

### Menambah atau Menghapus Bagian

Setiap bab memiliki struktur standar yang ditentukan di `prompt.md`. AI akan mengikuti struktur ini. Jika ingin bagian berbeda, beri tahu AI selama fase interaktif.

---

## Tanya Jawab

**T: AI CLI/IDE apa saja yang didukung?**  
J: OpenCode, Claude Code, Gemini CLI, Codex CLI, Antigravity CLI, OpenClaude, Aider, Cursor, Windsurf, Trae, Cline, Roo Code, Continue, dan AI CLI/IDE lain yang bisa membaca struktur project lokal. Tidak mendukung chatbot web (ChatGPT.com, Claude.ai, Gemini.app).

**T: Berapa lama prosesnya?**  
J: Tergantung kompleksitas project dan kecepatan internet. Project sederhana bisa 5-10 menit, project kompleks bisa sampai 30 menit lebih. Build PDF-nya sendiri cuma 10-30 detik.

**T: Bisakah saya menggunakan engine PDF berbeda?**  
J: Default adalah pdflatex. Prompt secara eksplisit melarang penggunaan lualatex karena masalah kompatibilitas font. Jika harus menggunakan engine lain, ubah `build.sh` sesuai.

**T: Bagaimana jika saya tidak punya screenshot?**  
J: Beri tahu AI saat ditanya. Folder `gambar/` akan dilewati dan tidak ada lingkungan figure yang ditambahkan.

**T: Bagaimana cara menambah lebih dari satu penulis?**  
J: Template cover mendukung satu penulis. Ubah `cover.md` setelah generate untuk menambahkan penulis lain.

**T: PDF terlalu panjang/pendek.**  
J: Target ideal tergantung kategori. Beri tahu AI saat revisi untuk menyesuaikan panjang konten.

**T: Apa beda 4 kategori laporan?**  
J: Coding (6 bab, AI baca source code), Non-coding (5 bab, AI tanya kegiatan), General (3 bab, AI tanya topik), Ilmiah (5 bab + abstrak 2 bahasa, AI tanya topik penelitian).

**T: Harus clone repo ini di mana?**  
J: Untuk Coding, clone di luar project Anda -- AI yang akan clone project Anda. Untuk Non-coding/General/Ilmiah, clone di mana saja.

## Lisensi

Lisensi MIT. Lihat [LICENSE](LICENSE) untuk detail.
