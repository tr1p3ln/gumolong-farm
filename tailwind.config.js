import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // ── Mobile UI aliases (mobile/pk/* and mobile/kk/* views) ─
                'forest-green': '#2E7D32',
                'sage-muted':   '#607F5B',
                'berry-accent': '#B14B6F',

                // ── Core brand (Modern Homestead) ──────────────────────
                primary:            '#2E7D32',   // Forest Green — CTA, active states
                secondary:          '#607F5B',   // Earthy Green — borders, secondary text
                accent:             '#B14B6F',   // Berry/Rose — danger, destructive, alerts
                surface:            '#FAFAF7',   // Warm Beige — page background (alias)
                background:         '#F9F9F6',   // Page background

                // ── Surface layers ──────────────────────────────────────
                'surface-bright':            '#F9F9F6',
                'surface-container':         '#EEEEEB',
                'surface-container-low':     '#F4F4F1',
                'surface-container-lowest':  '#FFFFFF',
                'surface-container-high':    '#E8E8E5',
                'surface-container-highest': '#E2E3E0',
                'surface-dim':               '#DADAD7',
                'surface-variant':           '#E2E3E0',
                'inverse-surface':           '#2F312F',

                // ── On-surface / text roles ─────────────────────────────
                'on-surface':           '#1A1C1B',   // Primary body text
                'on-surface-variant':   '#40493D',   // Supporting / label text
                'on-background':        '#1A1C1B',
                'inverse-on-surface':   '#F1F1EE',

                // ── Primary role tokens ─────────────────────────────────
                'on-primary':               '#FFFFFF',
                'primary-container':        '#2E7D32',
                'on-primary-container':     '#CBFFC2',
                'primary-fixed':            '#A3F69C',
                'primary-fixed-dim':        '#88D982',
                'on-primary-fixed':         '#002204',
                'on-primary-fixed-variant': '#005312',
                'inverse-primary':          '#88D982',
                'surface-tint':             '#1B6D24',

                // ── Secondary role tokens ───────────────────────────────
                'on-secondary':              '#FFFFFF',
                'secondary-container':       '#C9ECC1',
                'on-secondary-container':    '#4E6C49',
                'secondary-fixed':           '#C9ECC1',
                'secondary-fixed-dim':       '#AED0A6',
                'on-secondary-fixed':        '#052106',
                'on-secondary-fixed-variant':'#314E2E',

                // ── Tertiary role tokens ────────────────────────────────
                tertiary:                     '#923357',
                'on-tertiary':                '#FFFFFF',
                'tertiary-container':         '#B14B6F',
                'on-tertiary-container':      '#FFEDF0',
                'tertiary-fixed':             '#FFD9E2',
                'tertiary-fixed-dim':         '#FFB1C7',
                'on-tertiary-fixed':          '#3F001C',
                'on-tertiary-fixed-variant':  '#7F2448',

                // ── Error tokens ────────────────────────────────────────
                error:              '#BA1A1A',
                'on-error':         '#FFFFFF',
                'error-container':  '#FFDAD6',
                'on-error-container':'#93000A',

                // ── Outline tokens ──────────────────────────────────────
                outline:            '#707A6C',
                'outline-variant':  '#BFCABA',
            },

            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
