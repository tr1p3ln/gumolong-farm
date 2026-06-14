# Prompt: Generate Laporan Non-coding / PKL / Magang (Pandoc + LaTeX)

## Tujuan

Kamu akan membantu user membuat laporan akademik berbentuk PDF untuk **kegiatan non-coding / PKL / magang / praktik kerja lapangan**. Laporan ini menggunakan format standar laporan akademik Indonesia (BAB I - V, font Times New Roman 12pt, margin A4, numbering otomatis). Target laporan ini adalah kegiatan seperti PKL SMK, magang mahasiswa, praktik kerja industri, atau kegiatan non-IT lainnya.

Sumber pedoman: Panduan Praktik Kerja Lapangan Kurikulum Merdeka Kemendikdasmen, format laporan PKL standar nasional.

Kamu harus:
1. Membaca project/kegiatan user untuk memahami konteks
2. Bertanya ke user untuk konfirmasi detail
3. Generate semua file markdown (.md) untuk setiap bab
4. Menyediakan template LaTeX dan build script
5. Build PDF dengan pandoc + pdflatex

---

## Tech Stack & Tools (WAJIB Diinstall)

**Tanyain OS/Distro dulu ke user** -- jangan asal kasih command:

- **Linux (Ubuntu/Debian/Linux Mint/Pop!_OS)**: `sudo apt update && sudo apt install pandoc texlive-latex-base texlive-fonts-recommended texlive-latex-extra texlive-fonts-extra imagemagick`
- **Fedora / RHEL / CentOS**: `sudo dnf install pandoc texlive-scheme-medium texlive-fonts-extra imagemagick`
- **Arch Linux / Manjaro / EndeavourOS**: `sudo pacman -S pandoc texlive-core texlive-fontsextra imagemagick`
- **macOS**: `brew install pandoc imagemagick && brew install --cask mactex`
- **Windows (WSL)**: sama kayak Linux (Ubuntu) -- pake `apt install`

Tools yang dipake:
- **Pandoc** -- convert markdown → LaTeX → PDF
- **pdflatex** -- pdf engine (BUKAN lualatex)
- **Times New Roman** -- font (default, via Nimbus Serif). Tanya user kalo mau ganti.
- **ImageMagick** (`convert`) -- strip alpha channel dari foto kegiatan
- **Font encoding file**: `/tmp/fontools_ts1.enc` (harus ada sebelum build)

---

## Struktur Folder Laporan

```
laporan/
  template.latex        # Template LaTeX
  build.sh              # Build script
  cover.md              # Cover page
  logo-kampus.jpg       # Logo kampus
  gambar/               # Folder foto kegiatan (optional)
  01-pendahuluan.md     # BAB I - Pendahuluan
  02-profil.md          # BAB II - Profil Perusahaan/Instansi
  03-pelaksanaan.md     # BAB III - Pelaksanaan Kegiatan
  04-analisis.md        # BAB IV - Analisis dan Pembahasan
  05-penutup.md         # BAB V - Penutup
  daftar-pustaka.md     # Daftar Pustaka
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
  echo '# PROFIL PERUSAHAAN'
  echo ''
  strip_yaml "$OUTDIR/02-profil.md" | strip_hash

  echo ''
  echo '# PELAKSANAAN KEGIATAN'
  echo ''
  strip_yaml "$OUTDIR/03-pelaksanaan.md" | strip_hash

  echo ''
  echo '# ANALISIS DAN PEMBAHASAN'
  echo ''
  strip_yaml "$OUTDIR/04-analisis.md" | strip_hash

  echo ''
  echo '# PENUTUP'
  echo ''
  strip_yaml "$OUTDIR/05-penutup.md" | strip_hash

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
| DAFTAR ISI | Center bold 14pt |
| DAFTAR PUSTAKA | Center bold 14pt |
| Tabel | `\arraystretch{1.5}`, `\tabcolsep{8pt}` |
| Figure caption | "Gambar 4.1" (Indonesian prefix) |
| Foto kegiatan | Full width (`\textwidth`), PNG/JPG, RGB (no alpha) |
| Header | Nama project kiri, nomor halaman kanan |
| Page numbering | Roman di cover/daftar isi, Arabic di konten |
| Daftar Pustaka | Format APA |
| Heading numbering | Serahkan ke LaTeX |

---

## Struktur Konten Per Bab

### Cover
- Judul laporan PKL/magang (Huge bold)
- Deskripsi singkat (large bold) -- optional
- Sub judul 1 & 2 (LARGE bold)
- Logo kampus (4cm width)
- Nama penulis + NPM
- Program studi
- Nama kampus
- Tahun

### BAB I -- PENDAHULUAN
- **Latar Belakang**: Alasan dilaksanakan PKL/magang, pentingnya pengalaman kerja
- **Tujuan PKL/Magang**: Tujuan kegiatan dalam bentuk numbered list
- **Manfaat PKL/Magang**: Manfaat bagi siswa, sekolah, dan perusahaan
- **Waktu dan Tempat Pelaksanaan**: Jadwal dan lokasi PKL
- **Metode Pengumpulan Data**: Observasi, wawancara, dokumentasi, studi pustaka

### BAB II -- PROFIL PERUSAHAAN/INSTANSI
- **Sejarah Perusahaan**: Gambaran umum dan sejarah berdirinya
- **Visi dan Misi**: Visi misi perusahaan/instansi
- **Struktur Organisasi**: Bagan struktur (deskripsi text-based)
- **Bidang Usaha/Kegiatan**: Produk atau layanan yang ditawarkan

### BAB III -- PELAKSANAAN KEGIATAN
- **Deskripsi Kegiatan**: Tugas dan tanggung jawab selama PKL/magang
- **Proses Kerja**: Langkah-langkah pelaksanaan kegiatan
- **Dokumentasi Kegiatan**: Foto kegiatan (tanya user, copy ke `gambar/`)
- **Kendala dan Solusi**: Masalah yang dihadapi dan cara mengatasinya

> **Foto kegiatan**: Tanya user apakah ada dokumentasi foto. Jika ada, minta path file, copy ke `gambar/`, embed dengan format figure. Jika tidak ada, skip.

### BAB IV -- ANALISIS DAN PEMBAHASAN
- **Analisis Kegiatan**: Analisis pelaksanaan kegiatan (kesesuaian teori dengan praktik)
- **Pembahasan**: Pembahasan hasil yang diperoleh selama PKL/magang
- **Keterkaitan dengan Kompetensi**: Hubungan kegiatan dengan bidang studi

### BAB V -- PENUTUP
- **Kesimpulan**: Numbered list kesimpulan dari kegiatan PKL/magang
- **Saran**: Numbered list saran untuk sekolah, perusahaan, dan peserta selanjutnya
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
Format referensi: APA. Cari referensi yang relevan dengan kegiatan.

---

## Alur Pengerjaan (WAJIB Interaktif)

### Step 1 -- Kenali Kegiatan User
Baca informasi dari user. Identifikasi:
- Nama kegiatan (PKL/Magang/Praktik Kerja)
- Nama perusahaan/instansi tempat kegiatan
- Jurusan/bidang kegiatan
- Ada dokumentasi foto?

### Step 2 -- Tanya User
TANYAKAN satu per satu (jangan sekaligus):
1. **"OS/Distro yang dipake? (Ubuntu, macOS, Windows/WSL, dll?)"** -- biar install command sesuai
2. "Judul laporan PKL/magang?"
3. "Nama perusahaan/instansi tempat PKL?"
4. "Sub judul (jika ada)?"
5. "Nama penulis + NPM/NIS?"
6. "Sekolah/Universitas?"
7. "Program studi/jurusan?"
8. "Tahun?"
9. "Mau font apa? (default: Times New Roman)"
10. "Path logo kampus?"
11. "Ada foto dokumentasi kegiatan? Path filenya?"
12. "Bidang kegiatan/divisi selama PKL?"
13. "Lama pelaksanaan (tanggal mulai - selesai)?"
14. Konfirmasi: "Ada yang mau ditambahkan atau diubah?"

### Step 3 -- Buat Folder & File
1. Buat `laporan/` folder di project user
2. Buat `laporan/gambar/` (kalo user punya foto)
3. Copy `logo-kampus.jpg` ke `laporan/`
4. Generate `laporan/template.latex`
5. Generate `laporan/build.sh`
6. Generate `laporan/cover.md`
7. Generate file `.md` bab 1-5
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
- **Setiap PNG harus di-strip alpha** -- build.sh handle otomatis
- **File `/tmp/fontools_ts1.enc` harus ada** sebelum build
- **Logo kampus harus .jpg** (kalo .png, update build.sh)
- **Heading level di .md**: `#` = BAB, `##` = Sub Bab, `###` = Sub Sub Bab
- **Daftar Pustaka**: APA format
- **Target panjang**: 20-40 halaman
- **Ini adalah laporan NON-CODING** -- tidak perlu implementasi kode, API, atau testing teknis

---

*Prompt template non-coding -- untuk laporan PKL, magang, praktik kerja lapangan, atau kegiatan non-IT lainnya.*
