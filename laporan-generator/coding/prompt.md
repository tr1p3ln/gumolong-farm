# Prompt: Generate Laporan Project Coding / IT (Pandoc + LaTeX)

## Tujuan

Kamu akan membantu user membuat laporan akademik berbentuk PDF untuk **project coding / IT / pemrograman** yang sudah selesai dikerjakan. Laporan ini menggunakan format standar laporan akademik Indonesia (BAB I - VI, font Times New Roman 12pt, margin A4, numbering otomatis). Target laporan ini adalah project seperti aplikasi web, mobile, API, sistem informasi, atau project IT lainnya.

Kamu harus:
1. Membaca project folder user untuk memahami project
2. Bertanya ke user untuk konfirmasi detail
3. Generate semua file markdown (.md) untuk setiap bab
4. Menyediakan template LaTeX dan build script
5. Build PDF dengan pandoc + pdflatex

---

## Tech Stack & Tools (WAJIB Diinstall)

**Tanyain OS/Distro dulu ke user** -- jangan asal kasih command. Sesuaiin OS/Distro:

- **Linux (Ubuntu/Debian/Linux Mint/Pop!_OS)**: `sudo apt update && sudo apt install pandoc texlive-latex-base texlive-fonts-recommended texlive-latex-extra texlive-fonts-extra imagemagick`
- **Fedora / RHEL / CentOS**: `sudo dnf install pandoc texlive-scheme-medium texlive-fonts-extra imagemagick`
- **Arch Linux / Manjaro / EndeavourOS**: `sudo pacman -S pandoc texlive-core texlive-fontsextra imagemagick`
- **macOS**: `brew install pandoc imagemagick && brew install --cask mactex`
- **Windows (WSL)**: sama kayak Linux (Ubuntu) -- pake `apt install`
- **Windows (native)**: install dari installer masing-masing, terus pake WSL buat jalanin build.sh

Tools yang dipake:
- **Pandoc** -- convert markdown → LaTeX → PDF
- **pdflatex** -- pdf engine (BUKAN lualatex)
- **Nimbus Serif** / Times New Roman -- font (default, via nimbus15.map + fontools_ts1.enc + patched t1jtm.fd). Tanya user kalo mau ganti font lain.
- **ImageMagick** (`convert`) -- strip alpha channel dari PNG screenshots
- **Font encoding file**: `/tmp/fontools_ts1.enc` (harus ada sebelum build)

---

## Struktur Folder Laporan

Buat struktur berikut di dalam project user:

```
laporan/
  template.latex        # Template LaTeX (copy dari bawah, ganti header)
  build.sh              # Build script (copy dari bawah)
  cover.md              # Cover page
  logo-kampus.jpg       # Logo kampus (copy dari user)
  gambar/               # Folder screenshot (optional)
  01-pendahuluan.md     # BAB I - Pendahuluan
  02-landasan-teori.md  # BAB II - Landasan Teori
  03-analisis-perancangan.md # BAB III - Analisis & Perancangan
  04-implementasi.md    # BAB IV - Implementasi
  05-hasil-pengujian.md # BAB V - Hasil & Pengujian
  06-penutup.md         # BAB VI - Penutup
  daftar-pustaka.md     # Daftar Pustaka (APA format)
```

---

## Template Cover (`cover.md`)

```latex
\thispagestyle{empty}
\begin{center}
\vspace*{1cm}
{\Huge\bfseries ${JUDUL_PROJECT}}\\[0.3cm]
{\large\bfseries ${DESKRIPSI_SINGKAT}}\\[0.5cm]
{\LARGE\bfseries ${SUB_JUDUL_1}\\
${SUB_JUDUL_2}}\\[1.5cm]
\includegraphics[width=4cm]{logo-kampus.jpg}\\[2cm]
{\large Disusun oleh:}\\[0.3cm]
{\large\bfseries ${NAMA_PENULIS}}\\
{\normalsize NPM: ${NPM}}\\[1.5cm]
{\large\bfseries ${PROGRAM_STUDI}}\\
{\large\bfseries ${NAMA_KAMPUS}}\\
{\large\bfseries ${TAHUN}}
\end{center}
\newpage
```

---

## Template LaTeX (`template.latex`)

COPY FILE INI PERSIS -- ganti `${HEADER_TEXT}` aja:

```latex
\documentclass[12pt,a4paper,oneside]{book}

\usepackage[T1]{fontenc}
\pdfmapfile{+nimbus15.map}
\renewcommand{\rmdefault}{NimbusSerif}
\renewcommand{\familydefault}{\rmdefault}
\usepackage[margin=2.5cm,top=3cm,bottom=3cm]{geometry}
\usepackage{setspace}
\usepackage{calc}
\usepackage{graphicx}
\usepackage{hyperref}
\usepackage{fancyhdr}
\usepackage{titlesec}
\usepackage{tocloft}
\usepackage{indentfirst}
\usepackage{caption}
\renewcommand{\figurename}{Gambar}
\usepackage{float}
\usepackage{listings}
\usepackage{xcolor}
\usepackage{enumitem}
\usepackage{longtable}
\usepackage{booktabs}
\usepackage{array}
\usepackage{tabularx}
\usepackage{changepage}

\providecommand{\tightlist}{%
  \setlength{\itemsep}{0pt}\setlength{\parskip}{0pt}}

\pagestyle{fancy}
\fancyhf{}
\fancyhead[L]{\small ${HEADER_TEXT}}
\fancyhead[R]{\thepage}
\renewcommand{\headrulewidth}{0.4pt}
\fancyfoot{}

\renewcommand{\chaptername}{BAB}
\renewcommand{\thechapter}{\Roman{chapter}}
\renewcommand{\thesection}{\arabic{chapter}.\arabic{section}}
\renewcommand{\thesubsection}{\arabic{chapter}.\arabic{section}.\arabic{subsection}}

\titleformat{\chapter}[display]
  {\normalfont\bfseries\centering\fontsize{14}{18}\selectfont}
  {\chaptername\ \thechapter}{10pt}{\fontsize{14}{18}\selectfont}
\titlespacing*{\chapter}{0pt}{10pt}{20pt}

\renewcommand{\cftchappresnum}{BAB }
\renewcommand{\cftchapnumwidth}{5em}
\renewcommand{\cftdotsep}{2}

\titleformat{\section}
  {\normalfont\bfseries\normalsize}{\thesection}{0.5em}{}
  
\titleformat{\subsection}
  {\normalfont\bfseries\normalsize}{\thesubsection}{0.5em}{}

\onehalfspacing
\setlength{\parindent}{1.5cm}

\renewcommand{\contentsname}{DAFTAR ISI}
\renewcommand{\cftchapfont}{\bfseries}
\renewcommand{\cftchappagefont}{\bfseries}
\setlength{\cftbeforechapskip}{5pt}
\makeatletter
\renewcommand{\@cftmaketoctitle}{%
  \@cftpagestyle
  \@afterheading}
\makeatother

\renewcommand{\arraystretch}{1.5}
\setlength{\tabcolsep}{8pt}

\lstset{
  basicstyle=\footnotesize\ttfamily,
  breaklines=true,
  frame=single,
  numbers=left,
  numberstyle=\tiny,
  tabsize=2,
  captionpos=b,
  backgroundcolor=\color{lightgray!10},
  frame=single,
  rulecolor=\color{lightgray},
}

\hypersetup{
  colorlinks=true,
  linkcolor=black,
  urlcolor=blue,
  citecolor=black,
}

\fancypagestyle{plain}{
  \fancyhf{}
  \fancyhead[L]{\small ${HEADER_TEXT}}
  \fancyhead[R]{\thepage}
  \renewcommand{\headrulewidth}{0.4pt}
}

\begin{document}

\frontmatter
\pagenumbering{roman}

$for(include-before)$
$include-before$
$endfor$

\vspace*{10pt}
\begin{center}
\bfseries\fontsize{14}{18}\selectfont DAFTAR ISI
\end{center}
\vspace{10pt}
\tableofcontents
\cleardoublepage

\mainmatter
\pagenumbering{arabic}

$body$

\end{document}
```

---

## Build Script (`build.sh`)

COPY FILE INI PERSIS di `laporan/build.sh`:

```bash
#!/usr/bin/env bash
set -e

DIR="$(cd "$(dirname "$0")" && pwd)"
OUTDIR="$DIR"
REPORT="$OUTDIR/laporan-project.pdf"

TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

cp "$OUTDIR/cover.md" "$TMPDIR/"
cp "$OUTDIR/template.latex" "$TMPDIR/"
cp "$OUTDIR/logo-kampus.jpg" "$TMPDIR/"
if [ -d "$OUTDIR/gambar" ]; then
  cp -r "$OUTDIR/gambar" "$TMPDIR/"
  for f in "$TMPDIR/gambar/"*.png; do
    [ -f "$f" ] && convert "$f" -alpha off "$f"
  done
fi
cp /tmp/fontools_ts1.enc "$TMPDIR/"

cat > "$TMPDIR/t1jtm.fd" << 'FDEOF'
\ProvidesFile{t1jtm.fd}
   [2010/11/10 Fontinst v1.927 font definitions for T1/jtm (patched).]

\expandafter\ifx\csname Jtms@scale\endcsname\relax
 \let\Jtms@@scale\@empty
\else
 \edef\Jtms@@scale{s*[\csname Jtms@scale\endcsname]}%
\fi%

\DeclareFontFamily{T1}{jtm}{}

\DeclareFontShape{T1}{jtm}{c}{n}{
    <->\Jtms@@scale jtmr8tc
}{}
\DeclareFontShape{T1}{jtm}{m}{n}{
    <->\Jtms@@scale jtmr8te
}{}
\DeclareFontShape{T1}{jtm}{m}{it}{
    <->\Jtms@@scale jtmri8te
}{}
\DeclareFontShape{T1}{jtm}{m}{sl}{
    <->\Jtms@@scale jtmro8te
}{}
\DeclareFontShape{T1}{jtm}{m}{sc}{
    <->\Jtms@@scale jtmrc8te
}{}
\DeclareFontShape{T1}{jtm}{b}{n}{
    <-> ssub * jtm/bx/n
}{}
\DeclareFontShape{T1}{jtm}{b}{it}{
    <-> ssub * jtm/bx/it
}{}
\DeclareFontShape{T1}{jtm}{b}{sl}{
    <-> ssub * jtm/bx/sl
}{}
\DeclareFontShape{T1}{jtm}{b}{sc}{
    <-> ssub * jtm/bx/sc
}{}
\DeclareFontShape{T1}{jtm}{x}{n}{
    <->\Jtms@@scale jtmr8tw
}{}
\DeclareFontShape{T1}{jtm}{x}{it}{
    <->\Jtms@@scale jtmri8tw
}{}
\DeclareFontShape{T1}{jtm}{x}{sl}{
    <->\Jtms@@scale jtmro8tw
}{}
\DeclareFontShape{T1}{jtm}{x}{sc}{
    <->\Jtms@@scale jtmrc8tw
}{}
\DeclareFontShape{T1}{jtm}{bx}{n}{
    <->\Jtms@@scale jtmb8tv
}{}
\DeclareFontShape{T1}{jtm}{bx}{it}{
    <->\Jtms@@scale jtmbi8tv
}{}
\DeclareFontShape{T1}{jtm}{bx}{sl}{
    <->\Jtms@@scale jtmbo8tv
}{}
\DeclareFontShape{T1}{jtm}{bx}{sc}{
    <->\Jtms@@scale jtmbc8tv
}{}

\endinput
FDEOF

COMBINED="$TMPDIR/report.md"

{
  strip_yaml() {
    sed '/^---$/,/^---$/d' "$1"
  }
  strip_hash() {
    grep -v '^#\+ BAB'
  }

  echo '# PENDAHULUAN'
  echo ''
  strip_yaml "$OUTDIR/01-pendahuluan.md" | strip_hash

  echo ''
  echo '# LANDASAN TEORI'
  echo ''
  strip_yaml "$OUTDIR/02-landasan-teori.md" | strip_hash

  echo ''
  echo '# ANALISIS DAN PERANCANGAN'
  echo ''
  strip_yaml "$OUTDIR/03-analisis-perancangan.md" | strip_hash

  echo ''
  echo '# IMPLEMENTASI'
  echo ''
  strip_yaml "$OUTDIR/04-implementasi.md" | strip_hash

  echo ''
  echo '# HASIL DAN PEMBAHASAN'
  echo ''
  strip_yaml "$OUTDIR/05-hasil-pengujian.md" | strip_hash

  echo ''
  echo '# PENUTUP'
  echo ''
  strip_yaml "$OUTDIR/06-penutup.md" | strip_hash

  echo ''
  cat "$OUTDIR/daftar-pustaka.md"
} > "$COMBINED"

cd "$TMPDIR"
pandoc \
  "$COMBINED" \
  --template="$TMPDIR/template.latex" \
  --include-before-body="$TMPDIR/cover.md" \
  --top-level-division=chapter \
  --pdf-engine=pdflatex \
  -o "$REPORT" 2>&1
```

---

## Format Spesifik Laporan (WAJIB)

| Item | Spesifikasi |
|------|-------------|
| Document class | `book`, 12pt, A4, oneside |
| Font | Times New Roman (default). Bisa diganti -- tanya user. |
| Encoding | T1 fontenc |
| Margin | 2.5cm kiri/kanan, 3cm atas/bawah |
| Spacing | 1.5 lines (`\onehalfspacing`) |
| Indent | 1.5cm (`\parindent`) |
| Bab header | 14pt bold center "BAB I", "BAB II" (ROMAN) |
| Section | 12pt bold "1.1", "2.1" (ARABIC) |
| Subsection | 12pt bold "1.1.1" (ARABIC) |
| DAFTAR ISI | Center bold 14pt, pake `\begin{center}...\end{center}` |
| DAFTAR PUSTAKA | Center bold 14pt, pake `\begin{center}...\end{center}` |
| Tabel | `\arraystretch{1.5}`, `\tabcolsep{8pt}` |
| Figure caption | "Gambar 4.1" (Indonesian prefix) |
| Gambar | Full width (`\textwidth`), PNG, RGB (no alpha) |
| Header | Nama project kiri, nomor halaman kanan |
| Page numbering | Roman (i,ii,iii) di cover/daftar isi, Arabic (1,2,3) di konten |
| TOC entries | "BAB I PENDAHULUAN" bold |
| Chapter start | Setiap bab ganti halaman baru (otomatis dari `\chapter`) |
| Daftar Pustaka | Format APA, cari referensi dari internet |
| Heading numbering | Serahkan ke LaTeX, JANGAN tulis manual di .md |

---

## Struktur Konten Per Bab

### Cover
- Judul project (Huge bold)
- Deskripsi singkat (large bold) -- optional
- Sub judul 1 & 2 (LARGE bold)
- Logo kampus (4cm width)
- "Disusun oleh:"
- Nama penulis + NPM
- Program studi
- Nama kampus
- Tahun

### BAB I -- PENDAHULUAN
- **Latar Belakang**: Masalah yang mendasari project, kenapa project IT ini dibangun
- **Rumusan Masalah**: Pertanyaan penelitian dalam bentuk numbered list
- **Tujuan**: Tujuan pembangunan project dalam bentuk numbered list
- **Manfaat**: Manfaat bagi masing-masing pihak terkait
- **Batasan Masalah**: Ruang lingkup dan batasan project

### BAB II -- LANDASAN TEORI
Buat subsection untuk setiap teknologi yang dipake:
- Teori tentang teknologi utama project
- Framework frontend yang digunakan
- Framework backend yang digunakan
- Database yang digunakan
- Library/pustaka pendukung
- Konsep keamanan yang diterapkan

### BAB III -- ANALISIS DAN PERANCANGAN
- **Analisis Kebutuhan**: Kebutuhan fungsional dan non-fungsional
- **Perancangan Sistem**:
  - Arsitektur sistem (diagram text-based)
  - Use case diagram (deskripsi aktor dan use case)
  - Entity Relationship Diagram (deskripsi tabel)
  - Flowchart / sequence diagram alur utama

### BAB IV -- IMPLEMENTASI
- **Lingkungan Pengembangan**: OS, versi bahasa/framework, tools
- **Implementasi Frontend**: Struktur project, penjelasan tiap halaman + screenshot
- **Implementasi Backend**: Struktur project, API endpoints (tabel), controller
- **Implementasi Keamanan**: Lapisan keamanan yang diterapkan

> **Screenshot**: Tanya user apakah ada screenshot yang ingin disertakan. Jika ada, minta path file screenshot, copy ke `gambar/`, embed dengan format figure. Jika tidak ada, skip.

### BAB V -- HASIL DAN PEMBAHASAN
- **Hasil Implementasi**: Ringkasan fitur yang berhasil dibangun
- **Pengujian Fungsional (Black Box Testing)**: Tabel pengujian
- **Pengujian Keamanan**: Tabel skenario keamanan (jika ada)
- **Pembahasan**: Analisis hasil pengujian

### BAB VI -- PENUTUP
- **Kesimpulan**: Numbered list kesimpulan dari project
- **Saran**: Numbered list saran pengembangan ke depan
- **Penutup**: Ucapan terima kasih dan harapan

### DAFTAR PUSTAKA (`daftar-pustaka.md`)
```markdown
\clearpage
\addcontentsline{toc}{chapter}{DAFTAR PUSTAKA}
\vspace*{30pt}
\begin{center}
\bfseries\fontsize{14}{18}\selectfont DAFTAR PUSTAKA
\end{center}
\vspace{20pt}

\raggedright

Penulis. (Tahun). *Judul*. Penerbit. URL
```
Format referensi: APA. Cari referensi dari internet yang sesuai dengan project.

---

## Alur Pengerjaan (WAJIB Interaktif)

### Step 1 -- Kenali Project
Baca project folder user. Identifikasi:
- Nama project
- Tech stack (frontend, backend, database, dll)
- Fitur-fitur utama
- Ada screenshot aplikasi?

### Step 2 -- Tanya User
TANYAKAN satu per satu (jangan sekaligus):
1. **"OS/Distro yang dipake? (Ubuntu, macOS, Windows/WSL, Arch, Fedora?)"** -- biar install command-nya sesuai
2. "Judul laporan yang diinginkan?"
3. "Deskripsi singkat project?"
4. "Sub judul 1 dan 2?"
5. "Nama penulis + NPM?"
6. "Program studi?"
7. "Nama kampus?"
8. "Tahun?"
9. "Mau font apa? (default: Times New Roman)"
10. "Path logo kampus?"
11. "Ada screenshot yang ingin disertakan? Kalo ada, path filenya?"
12. "Tech stack yang dipake? (frontend, backend, database)"
13. "Fitur-fitur utama?"
14. "Untuk judul bab, apakah mau disesuaikan atau tetap default?"
15. Konfirmasi: "Ada yang mau ditambahkan atau diubah?"

### Step 3 -- Buat Folder & File
1. Buat `laporan/` folder di project
2. Buat `laporan/gambar/` (kalo user punya screenshot)
3. Copy `logo-kampus.jpg` ke `laporan/`
4. Generate `laporan/template.latex` (isi sesuai template di atas, ganti `${HEADER_TEXT}`)
5. Generate `laporan/build.sh` (copy persis dari atas)
6. Generate `laporan/cover.md` (isi sesuai data user)
7. Generate semua file `.md` bab 1-6
8. Generate `laporan/daftar-pustaka.md`

### Step 4 -- Build & Verifikasi
1. `chmod +x laporan/build.sh`
2. `./laporan/build.sh`
3. Cek file PDF: `pdfinfo laporan/laporan-project.pdf`
4. Tanya user: "PDF sudah jadi. Ada yang mau diperbaiki?"

### Step 5 -- Iterasi
Kalo user minta revisi, ulangi langkah yang diperlukan.

---

## Catatan Penting

- **JANGAN pake lualatex** -- pake pdflatex
- **JANGAN tulis nomor heading manual** di file .md -- nomor dari LaTeX
- **Setiap PNG harus di-strip alpha** -- build.sh udah handle ini (skip kalo ga ada gambar)
- **File `/tmp/fontools_ts1.enc` harus ada** sebelum build
- **Logo kampus harus .jpg** (kalo .png, update build.sh)
- **Gunakan `.md` biasa** untuk konten bab, kecuali untuk figure/table
- **Heading level di .md**: `#` = BAB, `##` = Sub Bab, `###` = Sub Sub Bab
- **Daftar Pustaka**: APA format, cari referensi real dari internet
- **Target panjang**: 25-40 halaman sudah ideal
- **Screenshot**: Format figure `\begin{figure}[H]` dengan `\includegraphics[width=\textwidth]`

---

*Prompt template coding/IT -- untuk project pemrograman, aplikasi, sistem informasi, atau project IT lainnya.*
