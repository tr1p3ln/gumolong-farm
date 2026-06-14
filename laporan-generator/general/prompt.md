# Prompt: Generate Makalah / General (Pandoc + LaTeX)

## Tujuan

Kamu akan membantu user membuat **makalah akademik** berbentuk PDF berdasarkan **kajian pustaka** (literature review). Makalah ini menggunakan format standar penulisan makalah Indonesia (BAB I - III, font Times New Roman 12pt, margin A4, numbering otomatis). Target makalah ini adalah tugas kuliah, kajian topik, atau paper sederhana yang tidak memerlukan penelitian lapangan atau implementasi teknis.

Sumber pedoman: Pedoman Penulisan Makalah Kemendikdasmen SLI-2023, standar makalah perguruan tinggi Indonesia.

Kamu harus:
1. Membaca topik/user requirements
2. Bertanya ke user untuk konfirmasi detail
3. Generate semua file markdown (.md) untuk setiap bab
4. Menyediakan template LaTeX dan build script
5. Build PDF dengan pandoc + pdflatex

---

## Tech Stack & Tools (WAJIB Diinstall)

**Tanyain OS/Distro dulu ke user** -- jangan asal kasih command:

- **Linux (Ubuntu/Debian)**: `sudo apt update && sudo apt install pandoc texlive-latex-base texlive-fonts-recommended texlive-latex-extra texlive-fonts-extra imagemagick`
- **Fedora**: `sudo dnf install pandoc texlive-scheme-medium texlive-fonts-extra imagemagick`
- **Arch Linux**: `sudo pacman -S pandoc texlive-core texlive-fontsextra imagemagick`
- **macOS**: `brew install pandoc imagemagick && brew install --cask mactex`
- **Windows (WSL)**: sama kayak Linux (Ubuntu)

Tools yang dipake:
- **Pandoc** -- convert markdown → LaTeX → PDF
- **pdflatex** -- pdf engine (BUKAN lualatex)
- **Times New Roman** -- font (default, via Nimbus Serif). Tanya user kalo mau ganti.
- **ImageMagick** (`convert`) -- opsional, kalo ada gambar
- **Font encoding file**: `/tmp/fontools_ts1.enc` (harus ada sebelum build)

---

## Struktur Folder Laporan

```
laporan/
  template.latex        # Template LaTeX
  build.sh              # Build script
  cover.md              # Cover page
  logo-kampus.jpg       # Logo kampus
  01-pendahuluan.md     # BAB I - Pendahuluan
  02-pembahasan.md      # BAB II - Pembahasan
  03-penutup.md         # BAB III - Penutup
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
  echo '# PEMBAHASAN'
  echo ''
  strip_yaml "$OUTDIR/02-pembahasan.md" | strip_hash

  echo ''
  echo '# PENUTUP'
  echo ''
  strip_yaml "$OUTDIR/03-penutup.md" | strip_hash

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
| Header | Nama project kiri, nomor halaman kanan |
| Page numbering | Roman di cover/daftar isi, Arabic di konten |
| Daftar Pustaka | Format APA |
| Heading numbering | Serahkan ke LaTeX |

---

## Struktur Konten Per Bab

### Cover
- Judul makalah (Huge bold)
- Deskripsi singkat -- optional
- Sub judul 1 & 2 (LARGE bold)
- Logo kampus (4cm width)
- Nama penulis + NPM
- Program studi
- Nama kampus
- Tahun

### BAB I -- PENDAHULUAN
- **Latar Belakang**: Alasan pemilihan topik, pentingnya topik dibahas
- **Rumusan Masalah**: Pertanyaan penelitian dalam bentuk numbered list
- **Tujuan**: Tujuan penulisan makalah dalam bentuk numbered list
- **Manfaat**: Manfaat penulisan bagi pembaca dan penulis

### BAB II -- PEMBAHASAN
Ini adalah inti makalah. User akan menentukan topik yang dibahas. Tanya user apa topiknya, lalu buat subsection yang relevan.

Sebagai panduan umum, pembahasan bisa mencakup:
- **Kajian Teori**: Definisi, konsep, teori dari literatur
- **Analisis**: Pembahasan mendalam tentang topik dari berbagai sumber
- **Sintesis**: Perbandingan pendapat para ahli dan pandangan penulis

> **Catatan**: Makalah ini murni kajian pustaka. Tidak ada implementasi, tidak ada penelitian lapangan, tidak ada kode. Semua konten berasal dari buku, jurnal, artikel, dan sumber terpercaya.

### BAB III -- PENUTUP
- **Kesimpulan**: Numbered list kesimpulan dari pembahasan
- **Saran**: Numbered list saran terkait topik
- **Penutup**: Ucapan terima kasih

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
Format referensi: APA. Cari referensi dari buku, jurnal, dan sumber terpercaya.

---

## Alur Pengerjaan (WAJIB Interaktif)

### Step 1 -- Pahami Topik
Tanya user:
- Topik makalah
- Mata kuliah terkait (jika ada)
- Sumber referensi yang sudah dimiliki (jika ada)

### Step 2 -- Tanya User
TANYAKAN satu per satu (jangan sekaligus):
1. **"OS/Distro yang dipake?"** -- biar install command sesuai
2. "Judul makalah?"
3. "Topik/ bidang pembahasan?"
4. "Sub judul (jika ada)?"
5. "Nama penulis + NPM?"
6. "Program studi?"
7. "Nama kampus?"
8. "Tahun?"
9. "Mau font apa? (default: Times New Roman)"
10. "Path logo kampus?"
11. "Ada sumber referensi yang sudah dimiliki atau mau dicariin?"
12. "Untuk judul bab, apakah mau disesuaikan?"
13. Konfirmasi: "Ada yang mau ditambahkan?"

### Step 3 -- Buat Folder & File
1. Buat `laporan/` folder
2. Copy `logo-kampus.jpg` ke `laporan/`
3. Generate `laporan/template.latex`
4. Generate `laporan/build.sh`
5. Generate `laporan/cover.md`
6. Generate file `.md` bab 1-3
7. Generate `laporan/daftar-pustaka.md`

### Step 4 -- Build & Verifikasi
1. `chmod +x laporan/build.sh`
2. `./laporan/build.sh`
3. Cek file PDF
4. Tanya user: "PDF sudah jadi. Ada yang mau diperbaiki?"

### Step 5 -- Iterasi
Kalo user minta revisi, ulangi langkah yang diperlukan.

---

## Catatan Penting

- **JANGAN pake lualatex** -- pake pdflatex
- **JANGAN tulis nomor heading manual** di file .md
- **File `/tmp/fontools_ts1.enc` harus ada** sebelum build
- **Logo kampus harus .jpg**
- **Heading level di .md**: `#` = BAB, `##` = Sub Bab, `###` = Sub Sub Bab
- **Daftar Pustaka**: APA format
- **Target panjang**: 10-20 halaman
- **MAKALAH INI MURNI KAJIAN PUSTAKA** -- tidak perlu kode, screenshot, implementasi, atau metode penelitian

---

*Prompt template general/makalah -- untuk makalah kajian pustaka, paper tugas kuliah, atau topik umum non-penelitian.*
