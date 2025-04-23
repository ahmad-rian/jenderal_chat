<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JenderalChat - Realtime Chat Application</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700|instrument-sans:400,500,600|geologica:400,500,600"
        rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #0EA5E9 0%, #6366F1 100%);
        }

        .text-gradient {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-float-delay-1 {
            animation: float 6s ease-in-out 1s infinite;
        }

        .animate-float-delay-2 {
            animation: float 6s ease-in-out 2s infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .mesh-gradient {
            background-color: hsla(240, 100%, 97%, 1);
            background-image:
                radial-gradient(at 40% 20%, hsla(250, 100%, 90%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(221, 100%, 85%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(264, 100%, 95%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(234, 100%, 90%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(271, 100%, 90%, 1) 0px, transparent 50%);
        }

        .dark .mesh-gradient {
            background-color: hsla(240, 10%, 10%, 1);
            background-image:
                radial-gradient(at 40% 20%, hsla(250, 70%, 10%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 0%, hsla(221, 70%, 15%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 50%, hsla(264, 70%, 15%, 1) 0px, transparent 50%),
                radial-gradient(at 80% 100%, hsla(234, 70%, 15%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(271, 70%, 15%, 1) 0px, transparent 50%);
        }
    </style>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Geologica', 'sans-serif'],
                        mono: ['Instrument Sans', 'monospace'],
                    },
                }
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: localStorage.theme === 'dark' ||
                    (!('theme' in localStorage) &&
                        window.matchMedia('(prefers-color-scheme: dark)').matches),

                toggle() {
                    this.dark = !this.dark;
                    localStorage.theme = this.dark ? 'dark' : 'light';
                    this.updateDOM();
                },

                updateDOM() {
                    if (this.dark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });

            Alpine.store('theme').updateDOM();
        });
    </script>
</head>

<body class="antialiased font-sans min-h-screen mesh-gradient dark:text-white">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav
            class="backdrop-blur-md bg-white/70 dark:bg-gray-900/70 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-primary flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="ml-2 text-xl font-heading font-semibold">JenderalChat</span>
                            </div>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="#"
                                class="border-indigo-500 dark:border-indigo-400 text-gray-900 dark:text-white inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Home
                            </a>
                            {{-- <a href="#"
                                class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Features
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Pricing
                            </a>
                            <a href="#"
                                class="border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-700 dark:hover:text-gray-200 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                About --}}
                            </a>
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center">
                        <button x-data @click="$store.theme.toggle()"
                            class="p-2 rounded-full text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                            <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="border-l border-gray-200 dark:border-gray-700 ml-4 pl-4 flex">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 px-3 py-2 rounded-md text-sm">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 px-3 py-2 rounded-md text-sm">Login</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                            class="ml-2 bg-gradient-primary text-white px-4 py-2 rounded-md text-sm font-medium shadow hover:shadow-lg transition duration-200">Register</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button" x-data @click="$store.theme.toggle()"
                            class="p-2 rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 mr-2">
                            <svg x-show="!$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <svg x-show="$store.theme.dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <button type="button" x-data="{ open: false }" @click="open = !open"
                            class="bg-white dark:bg-gray-800 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Open menu</span>
                            <svg x-show="!open" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="open" x-cloak class="h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu, show/hide based on menu state. -->
            <div x-data="{ open: false }" @click.away="open = false" x-show="open" x-cloak class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="#"
                        class="border-indigo-500 text-indigo-700 dark:text-indigo-300 block pl-3 pr-4 py-2 border-l-4 text-base font-medium bg-indigo-50 dark:bg-indigo-900/20">Home</a>
                    <a href="#"
                        class="border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-800 dark:hover:text-gray-200 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Features</a>
                    <a href="#"
                        class="border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-800 dark:hover:text-gray-200 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Pricing</a>
                    <a href="#"
                        class="border-transparent text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-800 dark:hover:text-gray-200 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">About</a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="space-y-1">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="block px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="block px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800">Login</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="block px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800">Register</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="flex-grow flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left">
                        <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold font-heading tracking-tight">
                            <span class="text-gray-900 dark:text-white">Modern Realtime </span>
                            <span class="text-gradient">Chat Experience</span>
                        </h1>
                        <p class="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-300 max-w-3xl">
                            Connect with your team in real-time using our blazing-fast, feature-rich chat platform
                            powered by Laravel, Livewire, Alpine.js and Tailwind CSS.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="#"
                                class="bg-gradient-primary text-white px-8 py-3 rounded-lg text-lg font-medium shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 duration-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                        clip-rule="evenodd" />
                                </svg>
                                Get Started
                            </a>
                            <a href="#"
                                class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-8 py-3 rounded-lg text-lg font-medium shadow-lg border border-gray-200 dark:border-gray-700 hover:shadow-xl transition transform hover:-translate-y-1 duration-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                </svg>
                                Watch Demo
                            </a>
                        </div>
                        <div
                            class="mt-8 flex items-center justify-center lg:justify-start text-sm text-gray-500 dark:text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2">No credit card required · Free 14-day trial</span>
                        </div>
                    </div>
                    <div class="relative">
                        <!-- Chat UI Mockup -->
                        <div
                            class="relative z-10 bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 transform rotate-1">
                            <div class="bg-gradient-secondary text-white px-6 py-4 flex items-center">
                                <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium">Team Jenderal</div>
                                    <div class="text-xs opacity-80">5 members, 3 online</div>
                                </div>
                            </div>

                            <div class="px-6 py-4 h-80 overflow-y-auto space-y-4">
                                <!-- Message - Left -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-500 dark:text-indigo-300 flex items-center justify-center font-medium">
                                            H</div>
                                    </div>
                                    <div
                                        class="ml-3 bg-gray-100 dark:bg-gray-700 rounded-lg rounded-tl-none px-4 py-2 max-w-xs">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Hafez</div>
                                        <div>Hi team! How's the new feature coming along?</div>
                                    </div>
                                </div>

                                <!-- Message - Right -->
                                <div class="flex items-start justify-end">
                                    <div
                                        class="bg-indigo-500 text-white rounded-lg rounded-tr-none px-4 py-2 max-w-xs">
                                        <div>Almost done! Just fixing some minor UI issues.</div>
                                    </div>
                                </div>

                                <!-- Message - Left -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-8 w-8 rounded-full bg-pink-100 dark:bg-pink-900 text-pink-500 dark:text-pink-300 flex items-center justify-center font-medium">
                                            N</div>
                                    </div>
                                    <div
                                        class="ml-3 bg-gray-100 dark:bg-gray-700 rounded-lg rounded-tl-none px-4 py-2 max-w-xs">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Naufal</div>
                                        <div>I've updated the documentation. Can someone review it?</div>
                                    </div>
                                </div>

                                <!-- Message - Right -->
                                <div class="flex items-start justify-end">
                                    <div
                                        class="bg-indigo-500 text-white rounded-lg rounded-tr-none px-4 py-2 max-w-xs">
                                        <div>I'll take a look at it this afternoon!</div>
                                    </div>
                                </div>

                                <!-- Message - Left -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="h-8 w-8 rounded-full bg-amber-100 dark:bg-amber-900 text-amber-500 dark:text-amber-300 flex items-center justify-center font-medium">
                                            RJ</div>
                                    </div>
                                    <div
                                        class="ml-3 bg-gray-100 dark:bg-gray-700 rounded-lg rounded-tl-none px-4 py-2 max-w-xs">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Robert Johnson</div>
                                        <div>Don't forget our team meeting at 3PM today!</div>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 flex">
                                <input type="text" placeholder="Type your message..."
                                    class="flex-grow bg-gray-100 dark:bg-gray-700 border-none rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                                <button class="ml-2 p-2 bg-indigo-500 text-white rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Decorative elements -->
                        <div
                            class="absolute -bottom-6 -left-6 w-24 h-24 bg-gradient-primary rounded-full opacity-30 animate-float">
                        </div>
                        <div
                            class="absolute -top-10 right-20 w-16 h-16 bg-gradient-secondary rounded-full opacity-30 animate-float-delay-1">
                        </div>
                        <div
                            class="absolute top-1/2 -right-8 w-20 h-20 bg-gradient-primary rounded-full opacity-30 animate-float-delay-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Tech Stack Section -->
        <section class="py-12 md:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold font-heading text-gray-900 dark:text-white">Powered by TALL
                        Stack</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Built with the most powerful web technologies for performance and developer experience
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8">
                    <!-- Laravel -->
                    <div class="flex flex-col items-center">
                        <div class="h-16 w-16 text-[#FF2D20]">
                            <svg viewBox="0 0 50 52" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                                <path
                                    d="M49.626 11.564a.809.809 0 0 1 .028.209v10.972a.8.8 0 0 1-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 0 1-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 0 1 0 39.25V6.334c0-.072.01-.142.028-.21.006-.023.02-.044.028-.067.015-.042.029-.085.051-.124.015-.026.037-.047.055-.071.023-.032.044-.065.071-.093.023-.023.053-.04.079-.06.029-.024.055-.05.088-.069h.001l9.61-5.533a.802.802 0 0 1 .8 0l9.61 5.533h.002c.032.02.059.045.088.068.026.02.055.038.078.06.028.029.048.062.072.094.017.024.04.045.054.071.023.04.036.082.052.124.008.023.022.044.028.068a.809.809 0 0 1 .028.209v20.559l8.008-4.611v-10.51c0-.07.01-.141.028-.208.007-.024.02-.045.028-.068.016-.042.03-.085.052-.124.015-.026.037-.047.054-.071.024-.032.044-.065.072-.093.023-.023.052-.04.078-.06.03-.024.056-.05.088-.069h.001l9.611-5.533a.801.801 0 0 1 .8 0l9.61 5.533c.034.02.06.045.09.068.025.02.054.038.077.06.028.029.048.062.072.094.018.024.04.045.054.071.023.039.036.082.052.124.009.023.022.044.028.068zm-1.574 10.718v-9.124l-3.363 1.936-4.646 2.675v9.124l8.01-4.611zm-9.61 16.505v-9.13l-4.57 2.61-13.05 7.448v9.216l17.62-10.144zM1.602 7.719v31.068L19.22 48.93v-9.214l-9.204-5.209-.003-.002-.004-.002c-.031-.018-.057-.044-.086-.066-.025-.02-.054-.036-.076-.058l-.002-.003c-.026-.025-.044-.056-.066-.084-.02-.027-.044-.05-.06-.078l-.001-.003c-.018-.03-.029-.066-.042-.1-.013-.03-.03-.058-.038-.09v-.001c-.01-.038-.012-.078-.016-.117-.004-.03-.012-.06-.012-.09V12.33L4.965 9.654 1.602 7.72zm8.81-5.994L2.405 6.334l8.005 4.609 8.006-4.61-8.006-4.608zm4.164 28.764l4.645-2.674V7.719l-3.363 1.936-4.646 2.675v20.096l3.364-1.937zM39.243 7.164l-8.006 4.609 8.006 4.609 8.005-4.61-8.005-4.608zm-.801 10.605l-4.646-2.675-3.363-1.936v9.124l4.645 2.674 3.364 1.937v-9.124zM20.02 38.33l11.743-6.704 5.87-3.35-8-4.606-9.211 5.303-8.395 4.833 7.993 4.524z"
                                    fill-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Laravel</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400"> Framework</p>
                    </div>

                    <!-- Livewire - External Image URL -->
                    <div class="flex flex-col items-center">
                        <div class="h-16 w-16">
                            <img src="https://icon.icepanel.io/Technology/svg/Livewire.svg" alt="Livewire Logo" />
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Livewire</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Dynamic UI without JS</p>
                    </div>

                    <!-- Alpine.js - External Image URL -->
                    <div class="flex flex-col items-center">
                        <div class="h-16 w-16">
                            <img src="https://icon.icepanel.io/Technology/png-shadow-512/Alpine.js.png"
                                alt="Alpine.js Logo" />
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Alpine.js</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Lightweight JS framework</p>
                    </div>

                    <!-- Tailwind CSS -->
                    <div class="flex flex-col items-center">
                        <div class="h-16 w-16 text-[#38BDF8]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 33" fill="currentColor">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M27 0c-7.2 0-11.7 3.6-13.5 10.8 2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C30.744 13.09 33.808 16.2 40.5 16.2c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.514-3.522-2.004-5.147-3.653C36.756 3.11 33.692 0 27 0zM13.5 16.2C6.3 16.2 1.8 19.8 0 27c2.7-3.6 5.85-4.95 9.45-4.05 2.054.514 3.522 2.004 5.147 3.653C17.244 29.29 20.308 32.4 27 32.4c7.2 0 11.7-3.6 13.5-10.8-2.7 3.6-5.85 4.95-9.45 4.05-2.054-.514-3.522-2.004-5.147-3.653C23.256 19.31 20.192 16.2 13.5 16.2z" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tailwind CSS</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Utility-first CSS</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-12 md:py-20 bg-white/70 dark:bg-gray-900/70 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold font-heading text-gray-900 dark:text-white">Powerful Features</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Built with modern technologies to provide the best chat experience
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition duration-300">
                        <div
                            class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Real-Time Communication</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                            Instant message delivery with WebSockets. No page refreshes required.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition duration-300">
                        <div
                            class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">End-to-End Encryption</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                            Your conversations are secured with the latest encryption standards.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition duration-300">
                        <div
                            class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">File Sharing</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                            Securely share files, images, and documents with your team members.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition duration-300">
                        <div
                            class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Group Chats</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                            Create dedicated channels for projects, teams, or topics.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition duration-300">
                        <div
                            class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Advanced Permissions</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                            Control who can send messages, upload files, or add members.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div
                        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 hover:shadow-lg transition duration-300">
                        <div
                            class="h-12 w-12 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Message History</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">
                            Access your complete message history with powerful search capabilities.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center">
                    <div class="h-8 w-8 rounded-full bg-gradient-primary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span
                        class="ml-2 text-xl font-heading font-semibold text-gray-900 dark:text-white">JenderalChat</span>
                </div>
                <p class="mt-4 text-center text-gray-600 dark:text-gray-300">&copy; 2025 JenderalChat. All rights
                    reserved.
                </p>
            </div>
        </footer>
    </div>
</body>

</html>
