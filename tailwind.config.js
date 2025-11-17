import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/**/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            /**
             * Typographic foundation for the application.
             * Figtree is used as the primary sans-serif font family.
             */
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            /**
             * Core color system with brand-focused scales, neutrals and semantic tokens.
             * The primary brand color at the 400 step is #029DB2.
             */
            colors: {
                /**
                 * Primary brand palette used for the main interaction color.
                 * The 400 step is the core brand color.
                 */
                primary: {
                    50:  '#EDF7F8',
                    100: '#DAEEF1',
                    200: '#ADE3EB',
                    300: '#4CD3E6',
                    400: '#029DB2', // brand base
                    500: '#088191',
                    600: '#066774',
                    700: '#094A53',
                    800: '#07353C',
                    900: '#052529',
                },

                /**
                 * Complementary palette designed to work with the primary brand.
                 * The 400 step is a coral tone derived from the brand complement.
                 */
                secondary: {
                    50:  '#FFEFED',
                    100: '#FFE0DB',
                    200: '#FEC0B8',
                    300: '#FEA194',
                    400: '#FD624D', // complementary base
                    500: '#D75341',
                    600: '#A44032',
                    700: '#722C23',
                    800: '#4C1D17',
                    900: '#33140F',
                },

                /**
                 * Neutral scale used for text, borders, backgrounds and subtle structures.
                 * Values are aligned with a cool slate-like progression for better pairing with the brand.
                 */
                neutral: {
                    50:  '#F8FAFC',
                    100: '#F1F5F9',
                    200: '#E2E8F0',
                    300: '#CBD5F5',
                    400: '#94A3B8',
                    500: '#64748B',
                    600: '#475569',
                    700: '#334155',
                    800: '#1E293B',
                    900: '#0F172A',
                },

                /**
                 * Accent tokens are dedicated to small interactive and decorative elements.
                 * They are aligned with the brand primary color.
                 */
                accent: {
                    DEFAULT: '#029DB2',
                    soft: '#4CD3E6',
                    'soft-bg': '#EDF7F8',
                },

                /**
                 * Background tokens define page-level canvas colors for both themes.
                 * These values are intended for body and large structural areas.
                 */
                background: {
                    DEFAULT: '#F8FAFC',            // light app background
                    muted: '#F1F5F9',              // light muted sections
                    subtle: '#E2E8F0',             // slightly stronger muted areas
                    elevated: '#FFFFFF',           // elevated light backgrounds
                    inverse: '#020617',            // dark app background
                    'inverse-muted': '#020617',    // dark muted sections
                    'inverse-elevated': '#0B1120', // elevated dark backgrounds
                },

                /**
                 * Surface tokens describe container and card backgrounds.
                 * They represent local surfaces on top of page-level backgrounds.
                 */
                surface: {
                    base: '#FFFFFF',               // standard card or panel in light mode
                    subtle: '#F1F5F9',             // low-emphasis containers in light mode
                    raised: '#FFFFFF',             // elevated light surfaces
                    alt: '#E2E8F0',                // navigation bars and toolbars in light mode
                    inverse: '#020617',            // base dark surface
                    'inverse-subtle': '#020617',   // low-emphasis containers in dark mode
                    'inverse-raised': '#0B1120',   // elevated dark surfaces
                    'inverse-alt': '#020617',      // structural dark navigation surfaces
                },

                /**
                 * Border tokens capture line work and structural separators.
                 * They support both subtle and high-contrast use cases.
                 */
                border: {
                    subtle: '#E2E8F0',       // subtle separators in light mode
                    DEFAULT: '#CBD5F5',      // default border color in light mode
                    strong: '#94A3B8',       // stronger borders and table lines
                    contrast: '#0F172A',     // high-contrast strokes when required
                    inverse: '#1E293B',      // default borders in dark mode
                    'inverse-strong': '#475569', // strong borders in dark mode
                },

                /**
                 * Text tokens define foreground colors for typography and icons.
                 * They ensure clear reading and consistent hierarchy across themes.
                 */
                text: {
                    DEFAULT: '#0F172A',          // main body text on light surfaces
                    muted: '#475569',            // secondary labels and descriptions
                    subtle: '#94A3B8',           // placeholders and disabled content
                    onPrimary: '#FFFFFF',        // text on primary backgrounds
                    onSecondary: '#FFFFFF',      // text on secondary backgrounds
                    inverse: '#E2E8F0',          // main text on dark surfaces
                    'inverse-muted': '#94A3B8',  // secondary text on dark surfaces
                    'inverse-subtle': '#64748B', // subtle dark theme text
                },

                /**
                 * Status tokens describe semantic feedback colors.
                 * They are intended for alerts, toasts, badges and validation states.
                 */
                status: {
                    success: {
                        bg: '#059669',
                        softBg: '#ECFDF5',
                        fg: '#FFFFFF',
                        subtleFg: '#166534',
                        border: '#6EE7B7',
                    },
                    error: {
                        bg: '#E11D48',
                        softBg: '#FEF2F2',
                        fg: '#FFFFFF',
                        subtleFg: '#B91C1C',
                        border: '#FCA5A5',
                    },
                    warning: {
                        bg: '#F59E0B',
                        softBg: '#FFFBEB',
                        fg: '#111827',
                        subtleFg: '#92400E',
                        border: '#FCD34D',
                    },
                    info: {
                        bg: '#0369A1',
                        softBg: '#EFF6FF',
                        fg: '#FFFFFF',
                        subtleFg: '#1D4ED8',
                        border: '#93C5FD',
                    },
                },

                /**
                 * Overlay tokens model scrims placed above content.
                 * They support modal backdrops and lighter layering for popovers.
                 */
                overlay: {
                    DEFAULT: 'rgba(15, 23, 42, 0.65)', // strong backdrop for blocking modals
                    soft: 'rgba(15, 23, 42, 0.35)',     // softer overlay for menus and panels
                    subtle: 'rgba(15, 23, 42, 0.15)',   // very light overlay and focus scrims
                },

                /**
                 * Action tokens define interaction-ready button and control colors.
                 * They provide consistent behavior for default, hover and active states.
                 */
                action: {
                    primary: {
                        bg: '#029DB2',
                        bgHover: '#088191',
                        bgActive: '#066774',
                        fg: '#FFFFFF',
                        border: 'transparent',
                        ring: 'rgba(2, 157, 178, 0.45)',
                        softBg: '#EDF7F8',
                    },
                    secondary: {
                        bg: '#FD624D',
                        bgHover: '#D75341',
                        bgActive: '#A44032',
                        fg: '#FFFFFF',
                        border: 'transparent',
                        ring: 'rgba(253, 98, 77, 0.45)',
                        softBg: '#FFEFED',
                    },
                    ghost: {
                        bg: 'transparent',
                        bgHover: 'rgba(15, 23, 42, 0.04)',
                        bgActive: 'rgba(15, 23, 42, 0.07)',
                        fg: '#029DB2',
                        border: '#E2E8F0',
                        ring: 'rgba(2, 157, 178, 0.40)',
                    },
                    subtle: {
                        bg: '#F1F5F9',
                        bgHover: '#E2E8F0',
                        bgActive: '#CBD5F5',
                        fg: '#0F172A',
                        border: 'transparent',
                        ring: 'rgba(15, 23, 42, 0.35)',
                    },
                },
            },

            /**
             * Shadow tokens for depth and elevation.
             * These values are optimized for card-like surfaces.
             */
            boxShadow: {
                card: '0 14px 30px -20px rgba(15, 23, 42, 0.35)',
                'card-soft': '0 10px 20px -18px rgba(15, 23, 42, 0.25)',
                depth: '0 -14px 30px -20px rgba(15, 23, 42, 0.35)',
                'depth-soft': '0 -10px 20px -18px rgba(15, 23, 42, 0.25)',
            },

            /**
             * Radius tokens that define rounding for components and containers.
             * Larger radii support modern card and panel designs.
             */
            borderRadius: {
                xl: '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
        },
    },

    plugins: [
        forms,
    ],
};
