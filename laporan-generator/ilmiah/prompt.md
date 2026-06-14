# Prompt: Generate Karya Ilmiah / Skripsi / Artikel (Pandoc + LaTeX)

## Tujuan

Kamu akan membantu user membuat **karya ilmiah formal** berbentuk PDF. Karya ilmiah ini mengikuti standar penulisan ilmiah Indonesia (abstrak, BAB I - V, font Times New Roman 12pt, margin A4, numbering otomatis, footnote). Target karya ini adalah skripsi, tesis, artikel jurnal, paper konferensi, atau laporan penelitian formal.

Sumber pedoman: Pedoman Penulisan Makalah, Tesis dan Disertasi UIN Jakarta 2024, Pedoman Umum Penulisan KTI UNSRI, Panduan Skripsi UNY 2023.

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
- **ImageMagick** (`convert`) -- strip alpha channel dari gambar
- **Font encoding file**: `/tmp/fontools_ts1.enc` (harus ada sebelum build)

---

## Struktur Folder Laporan

```
laporan/
  template.latex        # Template LaTeX
  build.sh              # Build script
  cover.md              # Cover page
  abstrak.md            # Abstrak (include-before-body)
  logo-kampus.jpg       # Logo kampus
  gambar/               # Folder gambar/grafik (optional)
  01-pendahuluan.md     # BAB I - Pendahuluan
  02-tinjauan-pustaka.md # BAB II - Tinjauan Pustaka
  03-metode.md          # BAB III - Metode Penelitian
  04-hasil.md           # BAB IV - Hasil dan Pembahasan
  05-penutup.md         # BAB V - Penutup
  daftar-pustaka.md     # Daftar Pustaka (APA)
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

% Untuk footnote
\usepackage[bottom]{footmisc}

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
cp "$OUTDIR/abstrak.md" "$TMPDIR/" 2>/dev/null || true
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
  echo '# TINJAUAN PUSTAKA'
  echo ''
  strip_yaml "$OUTDIR/02-tinjauan-pustaka.md" | strip_hash

  echo ''
  echo '# METODE PENELITIAN'
  echo ''
  strip_yaml "$OUTDIR/03-metode.md" | strip_hash

  echo ''
  echo '# HASIL DAN PEMBAHASAN'
  echo ''
  strip_yaml "$OUTDIR/04-hasil.md" | strip_hash

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
  --include-before-body="$TMPDIR/abstrak.md" \
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
| Header | Nama project kiri, nomor halaman kanan |
| Page numbering | Roman di cover/abstrak/daftar isi, Arabic di konten |
| Abstrak | Maks 300 kata, 1 spasi, satu alinea |
| Footnote | WAJIB untuk kutipan langsung |
| Daftar Pustaka | Format APA, urut abjad |
| Heading numbering | Serahkan ke LaTeX |

---

## Struktur Konten Per Bab

### Cover
- Judul penelitian (Huge bold)
- Deskripsi singkat -- optional
- Sub judul 1 & 2 (LARGE bold)
- Logo kampus (4cm width)
- Nama penulis + NPM
- Program studi
- Nama kampus
- Tahun

### ABSTRAK (`abstrak.md`)
File terpisah, di-include sebelum DAFTAR ISI. Abstrak ditulis dalam **dua bahasa**: Indonesia (Abstrak) dan Inggris (Abstract), masing-masing di halaman terpisah.

Format abstrak dalam markdown:
```markdown
\thispagestyle{plain}
\begin{center}
\bfseries\fontsize{14}{18}\selectfont ABSTRAK
\end{center}
\vspace{10pt}

\noindent
[Nama], [NPM]. [Tahun]. [Judul]. [Program Studi], [Kampus]. [Pembimbing].

\vspace{5pt}
\noindent
[Teks abstrak Bahasa Indonesia maksimal 300 kata, satu alinea, spasi 1. Berisi latar belakang, tujuan, metode, hasil, dan kesimpulan.]

\vspace{10pt}
\noindent
\textbf{Kata kunci}: [kata kunci 1], [kata kunci 2], [kata kunci 3], maksimal 5 kata.

\newpage

\thispagestyle{plain}
\begin{center}
\bfseries\fontsize{14}{18}\selectfont ABSTRACT
\end{center}
\vspace{10pt}

\noindent
[Name], [Student ID]. [Year]. [English Title]. [Department], [University]. [Supervisor].

\vspace{5pt}
\noindent
[English abstract, maximum 300 words, single paragraph, single spacing. Contains background, objectives, methods, results, and conclusions.]

\vspace{10pt}
\noindent
\textbf{Keywords}: [keyword 1], [keyword 2], [keyword 3], maximum 5 keywords.

\newpage
```

### BAB I -- PENDAHULUAN
- **Latar Belakang**: Konteks penelitian, masalah yang mendasari
- **Rumusan Masalah**: Pertanyaan penelitian (numbered list)
- **Tujuan Penelitian**: Tujuan dalam bentuk numbered list
- **Manfaat Penelitian**: Manfaat teoritis dan praktis
- **Batasan Masalah**: Ruang lingkup penelitian

### BAB II -- TINJAUAN PUSTAKA
- **Landasan Teori**: Teori-teori yang relevan dari buku/jurnal
- **Penelitian Terdahulu**: Review penelitian sebelumnya (tabel perbandingan)
- **Kerangka Berpikir**: Alur logis penelitian
- **Hipotesis** (jika ada): Hipotesis penelitian

> **Footnote**: Setiap kutipan langsung atau data spesifik harus pakai footnote (`\footnote{Sumber, Tahun, halaman}`).

### BAB III -- METODE PENELITIAN
- **Jenis Penelitian**: Kuantitatif/kualitatif/kombinasi
- **Desain Penelitian**: Rancangan penelitian
- **Populasi dan Sampel**: Subjek penelitian
- **Instrumen Penelitian**: Alat pengumpul data
- **Teknik Pengumpulan Data**: Observasi, kuesioner, wawancara, dokumentasi
- **Teknik Analisis Data**: Metode analisis yang digunakan

### BAB IV -- HASIL DAN PEMBAHASAN
- **Deskripsi Data**: Paparan data yang diperoleh
- **Analisis Data**: Hasil pengolahan data (tabel, grafik)
- **Pembahasan**: Interpretasi hasil, keterkaitan dengan teori
- **Keterbatasan Penelitian**: Hal-hal yang mempengaruhi hasil

### BAB V -- PENUTUP
- **Kesimpulan**: Numbered list kesimpulan penelitian
- **Saran**: Numbered list saran akademis dan praktis
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
Format referensi: APA style, urut abjad. Cari referensi dari jurnal ilmiah, buku, dan sumber terpercaya.

---

## Alur Pengerjaan (WAJIB Interaktif)

### Step 1 -- Pahami Topik
Tanya user:
- Topik/judul penelitian
- Jenis penelitian (kuantitatif/kualitatif)
- Metode yang digunakan
- Data yang sudah dimiliki

### Step 2 -- Tanya User
TANYAKAN satu per satu (jangan sekaligus):
1. **"OS/Distro yang dipake?"** -- biar install command sesuai
2. "Judul karya ilmiah/skripsi?"
3. "Jenis penelitian? (kuantitatif, kualitatif, atau kombinasi?)"
4. "Deskripsi singkat penelitian?"
5. "Sub judul (jika ada)?"
6. "Nama penulis + NPM?"
7. "Program studi?"
8. "Nama kampus?"
9. "Tahun?"
10. "Mau font apa? (default: Times New Roman)"
11. "Path logo kampus?"
12. "Ada gambar/grafik yang ingin disertakan? Path filenya?"
13. "Untuk judul bab, apakah mau disesuaikan?"
14. Konfirmasi: "Ada yang mau ditambahkan atau diubah?"

### Step 3 -- Buat Folder & File
1. Buat `laporan/` folder
2. Buat `laporan/gambar/` (kalo user punya gambar)
3. Copy `logo-kampus.jpg` ke `laporan/`
4. Generate `laporan/template.latex`
5. Generate `laporan/build.sh`
6. Generate `laporan/cover.md`
7. Generate `laporan/abstrak.md` (maks 300 kata)
8. Generate file `.md` bab 1-5
9. Generate `laporan/daftar-pustaka.md`

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
- **Heading level**: `#` = BAB, `##` = Sub Bab, `###` = Sub Sub Bab
- **Daftar Pustaka**: APA format, urut abjad
- **Abstrak**: dua bahasa (Indonesia + Inggris), masing-masing maks 300 kata, 1 spasi, satu alinea, halaman terpisah
- **Footnote**: Wajib untuk setiap kutipan langsung (`\footnote{}`)
- **Target panjang**: 30-60 halaman
- **Karya ilmiah ini formal** -- bahasa akademik, objektif, cendekia
- **Semua referensi harus dari sumber kredibel**: jurnal, buku, prosiding, laporan resmi

---

*Prompt template karya ilmiah -- untuk skripsi, tesis, artikel jurnal, paper konferensi, atau laporan penelitian formal. Sumber: Pedoman UIN Jakarta 2024, UNSRI, UNY, Polbangtan Manokwari.*
