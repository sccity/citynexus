/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    darkMode: ["class"],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],
    theme: {
    	container: {
    		center: true,
    		padding: '2rem',
    		screens: {
    			'2xl': '1400px'
    		}
    	},
    	extend: {
    		colors: {
    			border: 'hsl(var(--border))',
    			input: 'hsl(var(--input))',
    			ring: 'hsl(var(--ring))',
    			background: 'hsl(var(--background))',
    			foreground: 'hsl(var(--foreground))',
    			'desert-sand': {
    				DEFAULT: 'hsl(33 63% 77%)',
    				foreground: 'hsl(85 19% 24%)'
    			},
    			'sage-green': {
    				DEFAULT: 'hsl(82 18% 44%)',
    				foreground: 'hsl(60 30% 98%)'
    			},
    			'sandstone': {
    				DEFAULT: 'hsl(33 54% 62%)',
    				foreground: 'hsl(85 19% 24%)'
    			},
    			'canyon-red': {
    				DEFAULT: 'hsl(5 68% 42%)',
    				foreground: 'hsl(60 30% 98%)'
    			},
    			'sky-blue': {
    				DEFAULT: 'hsl(215 35% 65%)',
    				foreground: 'hsl(85 19% 24%)'
    			},
    			'dark-slate': {
    				DEFAULT: 'hsl(90 6% 13%)',
    				foreground: 'hsl(40 25% 83%)'
    			},
    			'terracotta': {
    				DEFAULT: 'hsl(17 57% 53%)',
    				foreground: 'hsl(40 25% 83%)'
    			},
    			primary: {
    				DEFAULT: 'hsl(var(--primary))',
    				foreground: 'hsl(var(--primary-foreground))'
    			},
    			secondary: {
    				DEFAULT: 'hsl(var(--secondary))',
    				foreground: 'hsl(var(--secondary-foreground))'
    			},
    			destructive: {
    				DEFAULT: 'hsl(var(--destructive))',
    				foreground: 'hsl(var(--destructive-foreground))'
    			},
    			muted: {
    				DEFAULT: 'hsl(var(--muted))',
    				foreground: 'hsl(var(--muted-foreground))'
    			},
    			accent: {
    				DEFAULT: 'hsl(var(--accent))',
    				foreground: 'hsl(var(--accent-foreground))'
    			},
    			popover: {
    				DEFAULT: 'hsl(var(--popover))',
    				foreground: 'hsl(var(--popover-foreground))'
    			},
    			card: {
    				DEFAULT: 'hsl(var(--card))',
    				foreground: 'hsl(var(--card-foreground))'
    			},
    			sidebar: {
    				DEFAULT: 'hsl(var(--sidebar-background))',
    				foreground: 'hsl(var(--sidebar-foreground))',
    				primary: {
    					DEFAULT: 'hsl(var(--sidebar-primary))',
    					foreground: 'hsl(var(--sidebar-primary-foreground))'
    				},
    				border: 'hsl(var(--sidebar-border))',
    				accent: {
    					DEFAULT: 'hsl(var(--sidebar-accent))',
    					foreground: 'hsl(var(--sidebar-accent-foreground))'
    				},
    				ring: 'hsl(var(--sidebar-ring))'
    			},
    			chart: {
    				'1': 'hsl(var(--chart-1))',
    				'2': 'hsl(var(--chart-2))',
    				'3': 'hsl(var(--chart-3))',
    				'4': 'hsl(var(--chart-4))',
    				'5': 'hsl(var(--chart-5))'
    			}
    		},
    		borderRadius: {
    			lg: 'var(--radius)',
    			md: 'calc(var(--radius) - 2px)',
    			sm: 'calc(var(--radius) - 4px)'
    		},
    		fontFamily: {
    			sans: [
    				'var(--font-sans)',
                    ...defaultTheme.fontFamily.sans
                ]
    		},
    		keyframes: {
    			'accordion-down': {
    				from: {
    					height: 0
    				},
    				to: {
    					height: 'var(--radix-accordion-content-height)'
    				}
    			},
    			'accordion-up': {
    				from: {
    					height: 'var(--radix-accordion-content-height)'
    				},
    				to: {
    					height: 0
    				}
    			}
    		},
    		animation: {
    			'accordion-down': 'accordion-down 0.2s ease-out',
    			'accordion-up': 'accordion-up 0.2s ease-out'
    		}
    	}
    },
    plugins: [require("tailwindcss-animate")],
}
