<?php

namespace App;

include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/site-settings.php';

class Init
{
    private function head(string $title, array $keywords = GENERIC_KEYWORDS, string $description = GENERIC_DESCRIPTION, string $thumbimage = '') : string {
        // Import file with cookie settings, html start, scripts
        $html = '';
        $html .= '<!DOCTYPE html>' . PHP_EOL;
        $html .= '<html lang="en" class="h-full">' . PHP_EOL;
        $html .= '<head>' . PHP_EOL;
        $html .=  PHP_EOL . '<title>' . $title . ' - ' . SITE_TITLE . '</title>' . PHP_EOL;
        $html .= '<link rel="icon" type="image/x-icon" href="/assets/images/icon.png" >' . PHP_EOL;
        //$html .= '<link rel="apple-touch-icon" href="/apple-touch-icon.png" >' . PHP_EOL;
        $html .= '<!-- Meta tags -->' . PHP_EOL;
        //$html .= '<meta name="apple-mobile-web-app-capable" content="yes">' . PHP_EOL;
        //$html .= '<meta name="apple-mobile-web-app-status-bar-style" content="black">' . PHP_EOL;
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . PHP_EOL;
        $html .= '<meta name="robots" content="index, follow" >' . PHP_EOL;
        $html .= '<meta name="author" content="Dimitar Dzhongov" >' . PHP_EOL;
        $html .= '<meta name="keywords" content="' . implode(",", $keywords) . '" >' . PHP_EOL;
        // Not needed $html .= '<link rel="canonical" href="https://"' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">' . PHP_EOL;
        $html .= '<meta name="description" content="' . $description . '" >' . PHP_EOL;
        // Open Graph tags
        $html .= '<meta property="og:type" content="website" >' . PHP_EOL;
        $html .= '<meta property="og:url" content="https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" >' . PHP_EOL;
        $html .= '<meta property="og:title" content="' . $title . '" >' . PHP_EOL;
        $html .= '<meta property="og:description" content="' . $description . '" >' . PHP_EOL;
        $html .= '<meta property="og:image" content="' . $thumbimage . '" >' . PHP_EOL;
        $html .= '<meta property="og:image:width" content="250">' . PHP_EOL;
        $html .= '<meta property="og:image:height" content="236">' . PHP_EOL;
        $html .= '<meta property="og:image:type" content="image/png">' . PHP_EOL;
        //$html .= '<meta property="fb:app_id" content="1061751454210608" >' . PHP_EOL;
        // Twitter tags
        $html .= '<meta name="twitter:card" content="summary_large_image" >' . PHP_EOL;
        $html .= '<meta name="twitter:site" content="@Sunwell_LTD" >' . PHP_EOL;
        $html .= '<meta name="twitter:creator" content="@Djongov" >' . PHP_EOL;
        $html .= '<meta name="twitter:title" content="' . $title . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:description" content="' . $description . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:image" content="' . $thumbimage . '" >' . PHP_EOL;
        $html .= '<meta name="twitter:image:alt" content="' . SITE_TITLE . ' logo" >' . PHP_EOL;
        $preflight = 'false';
        $html .= '<script src="/assets/js/main.js" defer></script>' . PHP_EOL;
        $html .= '<script src="/assets/js/flowbite.js"></script>' . PHP_EOL;
        $html .= '<script src="https://cdn.tailwindcss.com"></script>' . PHP_EOL;
        $html .= <<< InlineScript
            <script nonce="1nL1n3JsRuN1192kwoko2k323WKE">
                tailwind.config = {
                    content: [
                        './src/**/*.{php,js}',
                        './components/**/*.php',
                        './functions/**/*.php'
                    ],
                    presets: [],
                    darkMode: 'class', // or 'class'
                    theme: {
                        screens: {
                        sm: '640px',
                        md: '768px',
                        lg: '1024px',
                        xl: '1280px',
                        '2xl': '1536px',
                        },
                        colors: ({ colors }) => ({
                        inherit: colors.inherit,
                        current: colors.current,
                        transparent: colors.transparent,
                        black: colors.black,
                        white: colors.white,
                        slate: colors.slate,
                        gray: colors.gray,
                        zinc: colors.zinc,
                        neutral: colors.neutral,
                        stone: colors.stone,
                        red: colors.red,
                        orange: colors.orange,
                        amber: colors.amber,
                        yellow: colors.yellow,
                        lime: colors.lime,
                        green: colors.green,
                        emerald: colors.emerald,
                        teal: colors.teal,
                        cyan: colors.cyan,
                        sky: colors.sky,
                        blue: colors.blue,
                        indigo: colors.indigo,
                        violet: colors.violet,
                        purple: colors.purple,
                        fuchsia: colors.fuchsia,
                        pink: colors.pink,
                        rose: colors.rose,
                        }),
                        columns: {
                        auto: 'auto',
                        1: '1',
                        2: '2',
                        3: '3',
                        4: '4',
                        5: '5',
                        6: '6',
                        7: '7',
                        8: '8',
                        9: '9',
                        10: '10',
                        11: '11',
                        12: '12',
                        '3xs': '16rem',
                        '2xs': '18rem',
                        xs: '20rem',
                        sm: '24rem',
                        md: '28rem',
                        lg: '32rem',
                        xl: '36rem',
                        '2xl': '42rem',
                        '3xl': '48rem',
                        '4xl': '56rem',
                        '5xl': '64rem',
                        '6xl': '72rem',
                        '7xl': '80rem',
                        },
                        spacing: {
                        px: '1px',
                        0: '0px',
                        0.5: '0.125rem',
                        1: '0.25rem',
                        1.5: '0.375rem',
                        2: '0.5rem',
                        2.5: '0.625rem',
                        3: '0.75rem',
                        3.5: '0.875rem',
                        4: '1rem',
                        5: '1.25rem',
                        6: '1.5rem',
                        7: '1.75rem',
                        8: '2rem',
                        9: '2.25rem',
                        10: '2.5rem',
                        11: '2.75rem',
                        12: '3rem',
                        14: '3.5rem',
                        16: '4rem',
                        20: '5rem',
                        24: '6rem',
                        28: '7rem',
                        32: '8rem',
                        36: '9rem',
                        40: '10rem',
                        44: '11rem',
                        48: '12rem',
                        52: '13rem',
                        56: '14rem',
                        60: '15rem',
                        64: '16rem',
                        72: '18rem',
                        80: '20rem',
                        96: '24rem',
                        },
                        animation: {
                        none: 'none',
                        spin: 'spin 1s linear infinite',
                        ping: 'ping 1s cubic-bezier(0, 0, 0.2, 1) infinite',
                        pulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        bounce: 'bounce 1s infinite',
                        },
                        aspectRatio: {
                        auto: 'auto',
                        square: '1 / 1',
                        video: '16 / 9',
                        },
                        backdropBlur: ({ theme }) => theme('blur'),
                        backdropBrightness: ({ theme }) => theme('brightness'),
                        backdropContrast: ({ theme }) => theme('contrast'),
                        backdropGrayscale: ({ theme }) => theme('grayscale'),
                        backdropHueRotate: ({ theme }) => theme('hueRotate'),
                        backdropInvert: ({ theme }) => theme('invert'),
                        backdropOpacity: ({ theme }) => theme('opacity'),
                        backdropSaturate: ({ theme }) => theme('saturate'),
                        backdropSepia: ({ theme }) => theme('sepia'),
                        backgroundColor: ({ theme }) => theme('colors'),
                        backgroundImage: {
                        none: 'none',
                        'gradient-to-t': 'linear-gradient(to top, var(--tw-gradient-stops))',
                        'gradient-to-tr': 'linear-gradient(to top right, var(--tw-gradient-stops))',
                        'gradient-to-r': 'linear-gradient(to right, var(--tw-gradient-stops))',
                        'gradient-to-br': 'linear-gradient(to bottom right, var(--tw-gradient-stops))',
                        'gradient-to-b': 'linear-gradient(to bottom, var(--tw-gradient-stops))',
                        'gradient-to-bl': 'linear-gradient(to bottom left, var(--tw-gradient-stops))',
                        'gradient-to-l': 'linear-gradient(to left, var(--tw-gradient-stops))',
                        'gradient-to-tl': 'linear-gradient(to top left, var(--tw-gradient-stops))',
                        },
                        backgroundOpacity: ({ theme }) => theme('opacity'),
                        backgroundPosition: {
                        bottom: 'bottom',
                        center: 'center',
                        left: 'left',
                        'left-bottom': 'left bottom',
                        'left-top': 'left top',
                        right: 'right',
                        'right-bottom': 'right bottom',
                        'right-top': 'right top',
                        top: 'top',
                        },
                        backgroundSize: {
                        auto: 'auto',
                        cover: 'cover',
                        contain: 'contain',
                        },
                        blur: {
                        0: '0',
                        none: '0',
                        sm: '4px',
                        DEFAULT: '8px',
                        md: '12px',
                        lg: '16px',
                        xl: '24px',
                        '2xl': '40px',
                        '3xl': '64px',
                        },
                        brightness: {
                        0: '0',
                        50: '.5',
                        75: '.75',
                        90: '.9',
                        95: '.95',
                        100: '1',
                        105: '1.05',
                        110: '1.1',
                        125: '1.25',
                        150: '1.5',
                        200: '2',
                        },
                        borderColor: ({ theme }) => ({
                        ...theme('colors'),
                        DEFAULT: theme('colors.gray.200', 'currentColor'),
                        }),
                        borderOpacity: ({ theme }) => theme('opacity'),
                        borderRadius: {
                        none: '0px',
                        sm: '0.125rem',
                        DEFAULT: '0.25rem',
                        md: '0.375rem',
                        lg: '0.5rem',
                        xl: '0.75rem',
                        '2xl': '1rem',
                        '3xl': '1.5rem',
                        full: '9999px',
                        },
                        borderSpacing: ({ theme }) => ({
                        ...theme('spacing'),
                        }),
                        borderWidth: {
                        DEFAULT: '1px',
                        0: '0px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        boxShadow: {
                        sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                        DEFAULT: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                        md: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                        lg: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
                        xl: '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
                        '2xl': '0 25px 50px -12px rgb(0 0 0 / 0.25)',
                        inner: 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)',
                        none: 'none',
                        },
                        boxShadowColor: ({ theme }) => theme('colors'),
                        caretColor: ({ theme }) => theme('colors'),
                        accentColor: ({ theme }) => ({
                        ...theme('colors'),
                        auto: 'auto',
                        }),
                        contrast: {
                        0: '0',
                        50: '.5',
                        75: '.75',
                        100: '1',
                        125: '1.25',
                        150: '1.5',
                        200: '2',
                        },
                        container: {},
                        content: {
                        none: 'none',
                        },
                        cursor: {
                        auto: 'auto',
                        default: 'default',
                        pointer: 'pointer',
                        wait: 'wait',
                        text: 'text',
                        move: 'move',
                        help: 'help',
                        'not-allowed': 'not-allowed',
                        none: 'none',
                        'context-menu': 'context-menu',
                        progress: 'progress',
                        cell: 'cell',
                        crosshair: 'crosshair',
                        'vertical-text': 'vertical-text',
                        alias: 'alias',
                        copy: 'copy',
                        'no-drop': 'no-drop',
                        grab: 'grab',
                        grabbing: 'grabbing',
                        'all-scroll': 'all-scroll',
                        'col-resize': 'col-resize',
                        'row-resize': 'row-resize',
                        'n-resize': 'n-resize',
                        'e-resize': 'e-resize',
                        's-resize': 's-resize',
                        'w-resize': 'w-resize',
                        'ne-resize': 'ne-resize',
                        'nw-resize': 'nw-resize',
                        'se-resize': 'se-resize',
                        'sw-resize': 'sw-resize',
                        'ew-resize': 'ew-resize',
                        'ns-resize': 'ns-resize',
                        'nesw-resize': 'nesw-resize',
                        'nwse-resize': 'nwse-resize',
                        'zoom-in': 'zoom-in',
                        'zoom-out': 'zoom-out',
                        },
                        divideColor: ({ theme }) => theme('borderColor'),
                        divideOpacity: ({ theme }) => theme('borderOpacity'),
                        divideWidth: ({ theme }) => theme('borderWidth'),
                        dropShadow: {
                        sm: '0 1px 1px rgb(0 0 0 / 0.05)',
                        DEFAULT: ['0 1px 2px rgb(0 0 0 / 0.1)', '0 1px 1px rgb(0 0 0 / 0.06)'],
                        md: ['0 4px 3px rgb(0 0 0 / 0.07)', '0 2px 2px rgb(0 0 0 / 0.06)'],
                        lg: ['0 10px 8px rgb(0 0 0 / 0.04)', '0 4px 3px rgb(0 0 0 / 0.1)'],
                        xl: ['0 20px 13px rgb(0 0 0 / 0.03)', '0 8px 5px rgb(0 0 0 / 0.08)'],
                        '2xl': '0 25px 25px rgb(0 0 0 / 0.15)',
                        none: '0 0 #0000',
                        },
                        fill: ({ theme }) => theme('colors'),
                        grayscale: {
                        0: '0',
                        DEFAULT: '100%',
                        },
                        hueRotate: {
                        0: '0deg',
                        15: '15deg',
                        30: '30deg',
                        60: '60deg',
                        90: '90deg',
                        180: '180deg',
                        },
                        invert: {
                        0: '0',
                        DEFAULT: '100%',
                        },
                        flex: {
                        1: '1 1 0%',
                        auto: '1 1 auto',
                        initial: '0 1 auto',
                        none: 'none',
                        },
                        flexBasis: ({ theme }) => ({
                        auto: 'auto',
                        ...theme('spacing'),
                        '1/2': '50%',
                        '1/3': '33.333333%',
                        '2/3': '66.666667%',
                        '1/4': '25%',
                        '2/4': '50%',
                        '3/4': '75%',
                        '1/5': '20%',
                        '2/5': '40%',
                        '3/5': '60%',
                        '4/5': '80%',
                        '1/6': '16.666667%',
                        '2/6': '33.333333%',
                        '3/6': '50%',
                        '4/6': '66.666667%',
                        '5/6': '83.333333%',
                        '1/12': '8.333333%',
                        '2/12': '16.666667%',
                        '3/12': '25%',
                        '4/12': '33.333333%',
                        '5/12': '41.666667%',
                        '6/12': '50%',
                        '7/12': '58.333333%',
                        '8/12': '66.666667%',
                        '9/12': '75%',
                        '10/12': '83.333333%',
                        '11/12': '91.666667%',
                        full: '100%',
                        }),
                        flexGrow: {
                        0: '0',
                        DEFAULT: '1',
                        },
                        flexShrink: {
                        0: '0',
                        DEFAULT: '1',
                        },
                        fontFamily: {
                        sans: [
                            'ui-sans-serif',
                            'system-ui',
                            '-apple-system',
                            'BlinkMacSystemFont',
                            '"Segoe UI"',
                            'Roboto',
                            '"Helvetica Neue"',
                            'Arial',
                            '"Noto Sans"',
                            'sans-serif',
                            '"Apple Color Emoji"',
                            '"Segoe UI Emoji"',
                            '"Segoe UI Symbol"',
                            '"Noto Color Emoji"',
                        ],
                        serif: ['ui-serif', 'Georgia', 'Cambria', '"Times New Roman"', 'Times', 'serif'],
                        mono: [
                            'ui-monospace',
                            'SFMono-Regular',
                            'Menlo',
                            'Monaco',
                            'Consolas',
                            '"Liberation Mono"',
                            '"Courier New"',
                            'monospace',
                        ],
                        },
                        fontSize: {
                        xs: ['0.75rem', { lineHeight: '1rem' }],
                        sm: ['0.875rem', { lineHeight: '1.25rem' }],
                        base: ['1rem', { lineHeight: '1.5rem' }],
                        lg: ['1.125rem', { lineHeight: '1.75rem' }],
                        xl: ['1.25rem', { lineHeight: '1.75rem' }],
                        '2xl': ['1.5rem', { lineHeight: '2rem' }],
                        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
                        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
                        '5xl': ['3rem', { lineHeight: '1' }],
                        '6xl': ['3.75rem', { lineHeight: '1' }],
                        '7xl': ['4.5rem', { lineHeight: '1' }],
                        '8xl': ['6rem', { lineHeight: '1' }],
                        '9xl': ['8rem', { lineHeight: '1' }],
                        },
                        fontWeight: {
                        thin: '100',
                        extralight: '200',
                        light: '300',
                        normal: '400',
                        medium: '500',
                        semibold: '600',
                        bold: '700',
                        extrabold: '800',
                        black: '900',
                        },
                        gap: ({ theme }) => theme('spacing'),
                        gradientColorStops: ({ theme }) => theme('colors'),
                        gridAutoColumns: {
                        auto: 'auto',
                        min: 'min-content',
                        max: 'max-content',
                        fr: 'minmax(0, 1fr)',
                        },
                        gridAutoRows: {
                        auto: 'auto',
                        min: 'min-content',
                        max: 'max-content',
                        fr: 'minmax(0, 1fr)',
                        },
                        gridColumn: {
                        auto: 'auto',
                        'span-1': 'span 1 / span 1',
                        'span-2': 'span 2 / span 2',
                        'span-3': 'span 3 / span 3',
                        'span-4': 'span 4 / span 4',
                        'span-5': 'span 5 / span 5',
                        'span-6': 'span 6 / span 6',
                        'span-7': 'span 7 / span 7',
                        'span-8': 'span 8 / span 8',
                        'span-9': 'span 9 / span 9',
                        'span-10': 'span 10 / span 10',
                        'span-11': 'span 11 / span 11',
                        'span-12': 'span 12 / span 12',
                        'span-full': '1 / -1',
                        },
                        gridColumnEnd: {
                        auto: 'auto',
                        1: '1',
                        2: '2',
                        3: '3',
                        4: '4',
                        5: '5',
                        6: '6',
                        7: '7',
                        8: '8',
                        9: '9',
                        10: '10',
                        11: '11',
                        12: '12',
                        13: '13',
                        },
                        gridColumnStart: {
                        auto: 'auto',
                        1: '1',
                        2: '2',
                        3: '3',
                        4: '4',
                        5: '5',
                        6: '6',
                        7: '7',
                        8: '8',
                        9: '9',
                        10: '10',
                        11: '11',
                        12: '12',
                        13: '13',
                        },
                        gridRow: {
                        auto: 'auto',
                        'span-1': 'span 1 / span 1',
                        'span-2': 'span 2 / span 2',
                        'span-3': 'span 3 / span 3',
                        'span-4': 'span 4 / span 4',
                        'span-5': 'span 5 / span 5',
                        'span-6': 'span 6 / span 6',
                        'span-full': '1 / -1',
                        },
                        gridRowStart: {
                        auto: 'auto',
                        1: '1',
                        2: '2',
                        3: '3',
                        4: '4',
                        5: '5',
                        6: '6',
                        7: '7',
                        },
                        gridRowEnd: {
                        auto: 'auto',
                        1: '1',
                        2: '2',
                        3: '3',
                        4: '4',
                        5: '5',
                        6: '6',
                        7: '7',
                        },
                        gridTemplateColumns: {
                        none: 'none',
                        1: 'repeat(1, minmax(0, 1fr))',
                        2: 'repeat(2, minmax(0, 1fr))',
                        3: 'repeat(3, minmax(0, 1fr))',
                        4: 'repeat(4, minmax(0, 1fr))',
                        5: 'repeat(5, minmax(0, 1fr))',
                        6: 'repeat(6, minmax(0, 1fr))',
                        7: 'repeat(7, minmax(0, 1fr))',
                        8: 'repeat(8, minmax(0, 1fr))',
                        9: 'repeat(9, minmax(0, 1fr))',
                        10: 'repeat(10, minmax(0, 1fr))',
                        11: 'repeat(11, minmax(0, 1fr))',
                        12: 'repeat(12, minmax(0, 1fr))',
                        },
                        gridTemplateRows: {
                        none: 'none',
                        1: 'repeat(1, minmax(0, 1fr))',
                        2: 'repeat(2, minmax(0, 1fr))',
                        3: 'repeat(3, minmax(0, 1fr))',
                        4: 'repeat(4, minmax(0, 1fr))',
                        5: 'repeat(5, minmax(0, 1fr))',
                        6: 'repeat(6, minmax(0, 1fr))',
                        },
                        height: ({ theme }) => ({
                        auto: 'auto',
                        ...theme('spacing'),
                        '1/2': '50%',
                        '1/3': '33.333333%',
                        '2/3': '66.666667%',
                        '1/4': '25%',
                        '2/4': '50%',
                        '3/4': '75%',
                        '1/5': '20%',
                        '2/5': '40%',
                        '3/5': '60%',
                        '4/5': '80%',
                        '1/6': '16.666667%',
                        '2/6': '33.333333%',
                        '3/6': '50%',
                        '4/6': '66.666667%',
                        '5/6': '83.333333%',
                        full: '100%',
                        screen: '100vh',
                        min: 'min-content',
                        max: 'max-content',
                        fit: 'fit-content',
                        }),
                        inset: ({ theme }) => ({
                        auto: 'auto',
                        ...theme('spacing'),
                        '1/2': '50%',
                        '1/3': '33.333333%',
                        '2/3': '66.666667%',
                        '1/4': '25%',
                        '2/4': '50%',
                        '3/4': '75%',
                        full: '100%',
                        }),
                        keyframes: {
                        spin: {
                            to: {
                            transform: 'rotate(360deg)',
                            },
                        },
                        ping: {
                            '75%, 100%': {
                            transform: 'scale(2)',
                            opacity: '0',
                            },
                        },
                        pulse: {
                            '50%': {
                            opacity: '.5',
                            },
                        },
                        bounce: {
                            '0%, 100%': {
                            transform: 'translateY(-25%)',
                            animationTimingFunction: 'cubic-bezier(0.8,0,1,1)',
                            },
                            '50%': {
                            transform: 'none',
                            animationTimingFunction: 'cubic-bezier(0,0,0.2,1)',
                            },
                        },
                        },
                        letterSpacing: {
                        tighter: '-0.05em',
                        tight: '-0.025em',
                        normal: '0em',
                        wide: '0.025em',
                        wider: '0.05em',
                        widest: '0.1em',
                        },
                        lineHeight: {
                        none: '1',
                        tight: '1.25',
                        snug: '1.375',
                        normal: '1.5',
                        relaxed: '1.625',
                        loose: '2',
                        3: '.75rem',
                        4: '1rem',
                        5: '1.25rem',
                        6: '1.5rem',
                        7: '1.75rem',
                        8: '2rem',
                        9: '2.25rem',
                        10: '2.5rem',
                        },
                        listStyleType: {
                        none: 'none',
                        disc: 'disc',
                        decimal: 'decimal',
                        },
                        margin: ({ theme }) => ({
                        auto: 'auto',
                        ...theme('spacing'),
                        }),
                        maxHeight: ({ theme }) => ({
                        ...theme('spacing'),
                        full: '100%',
                        screen: '100vh',
                        min: 'min-content',
                        max: 'max-content',
                        fit: 'fit-content',
                        }),
                        maxWidth: ({ theme, breakpoints }) => ({
                        none: 'none',
                        0: '0rem',
                        xs: '20rem',
                        sm: '24rem',
                        md: '28rem',
                        lg: '32rem',
                        xl: '36rem',
                        '2xl': '42rem',
                        '3xl': '48rem',
                        '4xl': '56rem',
                        '5xl': '64rem',
                        '6xl': '72rem',
                        '7xl': '80rem',
                        full: '100%',
                        min: 'min-content',
                        max: 'max-content',
                        fit: 'fit-content',
                        prose: '65ch',
                        ...breakpoints(theme('screens')),
                        }),
                        minHeight: {
                        0: '0px',
                        full: '100%',
                        screen: '100vh',
                        min: 'min-content',
                        max: 'max-content',
                        fit: 'fit-content',
                        },
                        minWidth: {
                        0: '0px',
                        full: '100%',
                        min: 'min-content',
                        max: 'max-content',
                        fit: 'fit-content',
                        },
                        objectPosition: {
                        bottom: 'bottom',
                        center: 'center',
                        left: 'left',
                        'left-bottom': 'left bottom',
                        'left-top': 'left top',
                        right: 'right',
                        'right-bottom': 'right bottom',
                        'right-top': 'right top',
                        top: 'top',
                        },
                        opacity: {
                        0: '0',
                        5: '0.05',
                        10: '0.1',
                        20: '0.2',
                        25: '0.25',
                        30: '0.3',
                        40: '0.4',
                        50: '0.5',
                        60: '0.6',
                        70: '0.7',
                        75: '0.75',
                        80: '0.8',
                        90: '0.9',
                        95: '0.95',
                        100: '1',
                        },
                        order: {
                        first: '-9999',
                        last: '9999',
                        none: '0',
                        1: '1',
                        2: '2',
                        3: '3',
                        4: '4',
                        5: '5',
                        6: '6',
                        7: '7',
                        8: '8',
                        9: '9',
                        10: '10',
                        11: '11',
                        12: '12',
                        },
                        padding: ({ theme }) => theme('spacing'),
                        placeholderColor: ({ theme }) => theme('colors'),
                        placeholderOpacity: ({ theme }) => theme('opacity'),
                        outlineColor: ({ theme }) => theme('colors'),
                        outlineOffset: {
                        0: '0px',
                        1: '1px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        outlineWidth: {
                        0: '0px',
                        1: '1px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        ringColor: ({ theme }) => ({
                        DEFAULT: theme(`colors.blue.500`, '#3b82f6'),
                        ...theme('colors'),
                        }),
                        ringOffsetColor: ({ theme }) => theme('colors'),
                        ringOffsetWidth: {
                        0: '0px',
                        1: '1px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        ringOpacity: ({ theme }) => ({
                        DEFAULT: '0.5',
                        ...theme('opacity'),
                        }),
                        ringWidth: {
                        DEFAULT: '3px',
                        0: '0px',
                        1: '1px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        rotate: {
                        0: '0deg',
                        1: '1deg',
                        2: '2deg',
                        3: '3deg',
                        6: '6deg',
                        12: '12deg',
                        45: '45deg',
                        90: '90deg',
                        180: '180deg',
                        },
                        saturate: {
                        0: '0',
                        50: '.5',
                        100: '1',
                        150: '1.5',
                        200: '2',
                        },
                        scale: {
                        0: '0',
                        50: '.5',
                        75: '.75',
                        90: '.9',
                        95: '.95',
                        100: '1',
                        105: '1.05',
                        110: '1.1',
                        125: '1.25',
                        150: '1.5',
                        },
                        scrollMargin: ({ theme }) => ({
                        ...theme('spacing'),
                        }),
                        scrollPadding: ({ theme }) => theme('spacing'),
                        sepia: {
                        0: '0',
                        DEFAULT: '100%',
                        },
                        skew: {
                        0: '0deg',
                        1: '1deg',
                        2: '2deg',
                        3: '3deg',
                        6: '6deg',
                        12: '12deg',
                        },
                        space: ({ theme }) => ({
                        ...theme('spacing'),
                        }),
                        stroke: ({ theme }) => theme('colors'),
                        strokeWidth: {
                        0: '0',
                        1: '1',
                        2: '2',
                        },
                        textColor: ({ theme }) => theme('colors'),
                        textDecorationColor: ({ theme }) => theme('colors'),
                        textDecorationThickness: {
                        auto: 'auto',
                        'from-font': 'from-font',
                        0: '0px',
                        1: '1px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        textUnderlineOffset: {
                        auto: 'auto',
                        0: '0px',
                        1: '1px',
                        2: '2px',
                        4: '4px',
                        8: '8px',
                        },
                        textIndent: ({ theme }) => ({
                        ...theme('spacing'),
                        }),
                        textOpacity: ({ theme }) => theme('opacity'),
                        transformOrigin: {
                        center: 'center',
                        top: 'top',
                        'top-right': 'top right',
                        right: 'right',
                        'bottom-right': 'bottom right',
                        bottom: 'bottom',
                        'bottom-left': 'bottom left',
                        left: 'left',
                        'top-left': 'top left',
                        },
                        transitionDelay: {
                        75: '75ms',
                        100: '100ms',
                        150: '150ms',
                        200: '200ms',
                        300: '300ms',
                        500: '500ms',
                        700: '700ms',
                        1000: '1000ms',
                        },
                        transitionDuration: {
                        DEFAULT: '150ms',
                        75: '75ms',
                        100: '100ms',
                        150: '150ms',
                        200: '200ms',
                        300: '300ms',
                        500: '500ms',
                        700: '700ms',
                        1000: '1000ms',
                        },
                        transitionProperty: {
                        none: 'none',
                        all: 'all',
                        DEFAULT:
                            'color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter',
                        colors: 'color, background-color, border-color, text-decoration-color, fill, stroke',
                        opacity: 'opacity',
                        shadow: 'box-shadow',
                        transform: 'transform',
                        },
                        transitionTimingFunction: {
                        DEFAULT: 'cubic-bezier(0.4, 0, 0.2, 1)',
                        linear: 'linear',
                        in: 'cubic-bezier(0.4, 0, 1, 1)',
                        out: 'cubic-bezier(0, 0, 0.2, 1)',
                        'in-out': 'cubic-bezier(0.4, 0, 0.2, 1)',
                        },
                        translate: ({ theme }) => ({
                        ...theme('spacing'),
                        '1/2': '50%',
                        '1/3': '33.333333%',
                        '2/3': '66.666667%',
                        '1/4': '25%',
                        '2/4': '50%',
                        '3/4': '75%',
                        full: '100%',
                        }),
                        width: ({ theme }) => ({
                        auto: 'auto',
                        ...theme('spacing'),
                        '1/2': '50%',
                        '1/3': '33.333333%',
                        '2/3': '66.666667%',
                        '1/4': '25%',
                        '2/4': '50%',
                        '3/4': '75%',
                        '1/5': '20%',
                        '2/5': '40%',
                        '3/5': '60%',
                        '4/5': '80%',
                        '1/6': '16.666667%',
                        '2/6': '33.333333%',
                        '3/6': '50%',
                        '4/6': '66.666667%',
                        '5/6': '83.333333%',
                        '1/12': '8.333333%',
                        '2/12': '16.666667%',
                        '3/12': '25%',
                        '4/12': '33.333333%',
                        '5/12': '41.666667%',
                        '6/12': '50%',
                        '7/12': '58.333333%',
                        '8/12': '66.666667%',
                        '9/12': '75%',
                        '10/12': '83.333333%',
                        '11/12': '91.666667%',
                        full: '100%',
                        screen: '100vw',
                        min: 'min-content',
                        max: 'max-content',
                        fit: 'fit-content',
                        }),
                        willChange: {
                        auto: 'auto',
                        scroll: 'scroll-position',
                        contents: 'contents',
                        transform: 'transform',
                        },
                        zIndex: {
                        auto: 'auto',
                        0: '0',
                        10: '10',
                        20: '20',
                        30: '30',
                        40: '40',
                        50: '50',
                        },
                    },
                    variantOrder: [
                        'first',
                        'last',
                        'odd',
                        'even',
                        'visited',
                        'checked',
                        'empty',
                        'read-only',
                        'group-hover',
                        'group-focus',
                        'focus-within',
                        'hover',
                        'focus',
                        'focus-visible',
                        'active',
                        'disabled',
                    ],
                    plugins: [],
                }
            </script>
            InlineScript . PHP_EOL;
        $html .= '</head>' . PHP_EOL;
        return $html;
    }
    private function body() {
        $html = <<< HTML
        <body class="h-full antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-400">
            <div class="md:mx-auto bg-gray-200 dark:bg-gray-800">
        HTML;
        return $html;
    }
    private function menuBuilder($array, $theme, $username, $isAdmin) {
        $html = '<nav class="px-2 bg-white border-gray-200 dark:border-gray-700 dark:bg-gray-900">';
        // Holder div, used to have justify-between
        $html .= '<div class="flex flex-wrap justify-center">';
        // Logo + href to homepage
        $html .= '<a href="/" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-2 text-' . $theme . '-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                </svg>
            </a>';
        // Button for mobile menu sandwich
        $html .= '<button data-collapse-toggle="mobile-menu" type="button" class="inline-flex justify-center items-center ml-6 text-gray-400 rounded-lg md:hidden hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-' . $theme . '-300 dark:text-gray-400 dark:hover:text-white dark:focus:ring-gray-500" aria-controls="mobile-menu-2" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-8 h-8" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
            </button>';
        // Menu itself
        $html .= '<div class="hidden w-full md:block md:w-auto" id="mobile-menu">';
        $html .= '<ul class="mx-auto flex md:flex-row flex-col flex-wrap justify-center items-center p-4 mt-4 bg-gray-200 rounded-lg border border-gray-100 md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">';
        $uniqueIdCounter = 0;
        if ($username === null) {
            return;
        }
        foreach ($array as $name => $value) {
            // Mechanism to skip entries that should be under login only
            if (isset($value['require_login'])) {
                if ($username === null) {
                    continue;
                }
            }
            if (is_array($value['link'])) {
                $html .= '<li class="min-w-fit mx-auto">';
                $html .= '<div class="flex flex-row items-center">';

                if (isset($value['icon'])) {
                    if ($value['icon']['type'] === 'link') {
                        $html .= '<img class="w-6 h-6" src="' . $value['icon']['src'] . '" alt="' . $name . '" />';
                    } else {
                        $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 w-6 h-6 stroke-' . $theme . '-500">' . $value['icon']['src'] . '</svg>';
                    }
                }

                $html .= '<button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbar-' . $uniqueIdCounter . '" class="text-base font-normal ml-0 flex justify-between hover:bg-' . $theme . '-500 hover:boder hover:border-black hover:text-white text-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 hover:rounded-md p-1"> ' . $name . '<svg class="ml-1 w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button>';
                $html .= '</div>';
                $html .= '<!-- Dropdown menu -->
                        <div id="dropdownNavbar-' . $uniqueIdCounter . '" class="z-10 w-44 font-normal bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 block hidden" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(381px, 66px, 0px);">';
                $html .= '<ul class="py-1 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">';
                foreach ($value['link'] as $sub_name => $sub_array) {
                    $html .= '<li class="min-w-fit">';
                    $html .= '<div class="flex flex-row items-center">';
                    $html .= '<a href="' . $sub_array['sub_link'] . '" class="w-full text-center py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">';
                    if (isset($sub_array['icon'])) {
                        if ($sub_array['icon']['type'] === 'link') {
                            $html .= '<img class="mx-2 inline-block" src="' . $sub_array['icon']['src'] . '" alt="' . $sub_name . '" />';
                        } else {
                            $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-2 w-6 h-6 stroke-' . $theme . '-500">' . $sub_array['icon']['src'] . '</svg>';
                        }
                    }
                    $html .= $sub_name . '</a>';
                    $html .= '</div>';
                    $html .= '</li>';
                }
                $html .= '</ul>';
                $html .= '</div>';
                $html .= '</li>';
                continue;
            } else {
                $html .= '<li class="min-w-fit mx-auto">';
                $html .= '<div class="flex flex-row items-center">';
                if (isset($value['icon'])) {
                    if ($value['icon']['type'] === 'link') {
                        $html .= '<img class="mr-1 w-6 h-6" src="' . $value['icon']['src'] . '" alt="' . $name . '" />';
                    } else {
                        $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 stroke-' . $theme . '-500 mr-1">' . $value['icon']['src'] . '</svg>';
                    }
                }
                $html .= '<a href="' . $value['link'] . '" class="min-w-fit block py-2 text-base font-normal hover:bg-' . $theme . '-500 hover:boder hover:border-black hover:text-white text-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:text-white dark:border-gray-700 dark:hover:bg-gray-700 hover:rounded-md p-1">' . $name . '</a>';
                $html .= '</div>';
                $html .= '</li>';
            }

            $uniqueIdCounter++;
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div class="flex flex-row items-center ml-4">';
        // Theme switcher
        $html .=
            '<button id="theme-toggle" title="Toggle Light/Dark" type="button" class="h-12 w-10 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2">
                <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
            </button>';
        // Now let's only include the Check Demo button if the file calling this function is components\landing\navbar.php
        $debug_trace_array = debug_backtrace();
        if (str_contains($debug_trace_array[0]['file'], 'landing')) {
            $html .= '<a class="inline-flex items-center justify-center ml-2 my-2 px-2 py-2 h-10 w-32 md:w-44 mb-2 text-lg leading-7 text-' . $theme . '-50 bg-' . $theme . '-500 hover:bg-' . $theme . '-600 font-medium focus:ring-2 focus:ring-' . $theme . '-500 focus:ring-opacity-50 border border-transparent rounded-md shadow-sm" href="/demo/">Check Demo</a>';
        }
        if ($username !== null) {
            $html .= '<div class="flex md:order-2">
                <div class="flex items-center justify-between ml-2">
                    <div class="flex items-center">
                        <svg class="ml-1 w-12 h-12 stroke-' . $theme . '-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <button id="dropdownNavbarLink2" data-dropdown-toggle="userAvatarDropDownNavBar" class="ml-1 flex justify-between items-center py-2 pr-4 pl-3 w-full font-medium hover:bg-' . $theme . '-500 rounded hover:text-gray-100 truncate max-w-sm cursor-pointer">
                            ' . $username . '
                            <svg class="ml-1 w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- Dropdown menu -->
                <div id="userAvatarDropDownNavBar" class="z-10 w-44 font-normal bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600 block hidden" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="bottom" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate3d(381px, 66px, 0px);">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">';
                        foreach (USERNAME_DROPDOWN_MENU as $name => $link) {
                            $html .= '<li>';
                                $html .= '<a href="' . $link . '" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">' . $name . '</a>';
                            $html .= '</li>';
                            }
                    $html .= '</ul>
                </div>
            </div>';
        } else {
            $html .= '<a class="inline-flex items-center justify-center px-2 py-2 h-10 w-18 md:w-18 my-2 ml-4 text-lg leading-7 text-' . $theme . '-50 bg-' . $theme . '-500 hover:bg-' . $theme . '-600 font-medium focus:ring-2 focus:ring-' . $theme . '-500 focus:ring-opacity-50 border border-transparent rounded-md shadow-sm" href="' . Login_Button_URL . '">Login</a>';
            $html .= '</div>';
        }
        $html .= '</nav>';
        $html .= '<hr class="border-gray-200 sm:mx-auto dark:border-gray-700">';
        return $html;
    }
    private function insertController($filePath, $usernameArray, $username, $loggedIn, $isAdmin, $theme)
    {
        ob_start(); // Start output buffering

        // Include the file
        include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Views/' . $filePath;

        $content = ob_get_clean(); // Capture the output and clear the buffer
        return $content;
    }

    private function footer($username, $theme) {
        ob_start(); // Start output buffering

        // Include the file
        include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Views/' . 'footer.php';

        $content = ob_get_clean(); // Capture the output and clear the buffer
        return $content;
    }
    public function build($title, $filePath, $menuArray, $loginInfoArray) {

        $usernameArray = $loginInfoArray['usernameArray'];

        $username = $usernameArray['username'] ?? null;

        $theme = $usernameArray['theme'] ?? COLOR_SCHEME;

        $loggedIn = $loginInfoArray['loggedIn'];

        $isAdmin = $loginInfoArray['isAdmin'];

        $html = '';
        $html .= Init::head($title);
        $html .= Init::body();
        $html .= Init::menuBuilder($menuArray, $theme, $username, $isAdmin);
        $html .= init::insertController($filePath, $usernameArray, $username, $loggedIn, $isAdmin, $theme);
        //$html .= self::getFileContent($filePath);
        $html .= Init::footer($username, $theme);
        return $html;
    }
    public static function getFileContent($filePath) {
        ob_start(); // Start output buffering
        include_once dirname($_SERVER['DOCUMENT_ROOT']) . '/Views/' . $filePath;
        $content = ob_get_clean(); // Capture the output buffer and clear it
        return $content;
    }
}
