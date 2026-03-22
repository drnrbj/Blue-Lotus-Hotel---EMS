<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Blue Lotus Hotel</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy:  '#0F2453',
                        sky:   '#45AEE4',
                    },
                    fontFamily: {
                        sans: ['"Inter"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Thin scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">

<div class="flex h-screen overflow-hidden">

    {{-- ═══════════════════════════════
         SIDEBAR
    ═══════════════════════════════ --}}
    <aside class="w-60 bg-navy flex flex-col flex-shrink-0 overflow-y-auto">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-[18px] border-b border-white/10">
            <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M10 17c0 0-6-3.5-6-9a6 6 0 0112 0c0 5.5-6 9-6 9z" fill="#45AEE4" opacity="0.4"/>
                    <path d="M10 17c0 0-3.5-2.2-3.5-6a3.5 3.5 0 017 0c0 3.8-3.5 6-3.5 6z" fill="#45AEE4" opacity="0.75"/>
                    <path d="M10 17c0 0-1.5-1-1.5-2.5a1.5 1.5 0 013 0c0 1.5-1.5 2.5-1.5 2.5z" fill="#45AEE4"/>
                </svg>
            </div>
            <span class="text-white font-semibold text-[15px] leading-tight">Blue Lotus Hotel</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-3">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-5 py-2.5 text-[13.5px] font-medium transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <path d="M2 9.5L8.5 2.5L15 9.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3.5 8V14.5H7V10.5H10V14.5H13.5V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Dashboard
            </a>

            {{-- Employee Management --}}
            <p class="px-5 pt-5 pb-2 text-[10px] text-white/40 uppercase tracking-widest font-semibold">
                Employee Management
            </p>

            <a href="#"
               class="flex items-center gap-3 px-5 py-2.5 text-[13.5px] font-medium transition-colors
                      {{ request()->routeIs('employees.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <circle cx="6" cy="5.5" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M1 14c0-2.8 2.2-4 5-4s5 1.2 5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M11.5 7.5h4M13.5 5.5v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Employee List
            </a>

            <a href="{{ route('attendance.index') }}"
               class="flex items-center gap-3 px-5 py-2.5 text-[13.5px] font-medium transition-colors
                      {{ request()->routeIs('attendance.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <rect x="2" y="3" width="13" height="11" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5 1.5v3M12 1.5v3M2 7h13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M5 10.5l2 1.5 4-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Attendance
            </a>

            {{-- Finance --}}
            <p class="px-5 pt-5 pb-2 text-[10px] text-white/40 uppercase tracking-widest font-semibold">
                Finance
            </p>

            <a href="#"
               class="flex items-center gap-3 px-5 py-2.5 text-[13.5px] font-medium transition-colors
                      {{ request()->routeIs('payroll.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <rect x="2" y="4" width="13" height="10" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M2 7.5h13" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5.5 11h2M10.5 11h1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Payroll
            </a>

            {{-- Performance & Recruitment --}}
            <p class="px-5 pt-5 pb-2 text-[10px] text-white/40 uppercase tracking-widest font-semibold">
                HR
            </p>

            <a href="{{ route('performance.index') }}"
               class="flex items-center gap-3 px-5 py-2.5 text-[13.5px] font-medium transition-colors
                      {{ request()->routeIs('performance.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <path d="M2 12l3.5-3.5 3 3L13 5.5l2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Performance
            </a>

            <a href="#"
               class="flex items-center gap-3 px-5 py-2.5 text-[13.5px] font-medium transition-colors
                      {{ request()->routeIs('recruitment.*') ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <svg width="17" height="17" viewBox="0 0 17 17" fill="none">
                    <rect x="2" y="2" width="13" height="13" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M5.5 5.5h6M5.5 8.5h6M5.5 11.5h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Recruitment
            </a>

        </nav>

        {{-- User Footer --}}
        <div class="px-5 py-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-sky flex items-center justify-center text-white text-[11px] font-bold flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-white text-[12px] font-medium truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-white/40 text-[11px] truncate">{{ Auth::user()->role ?? 'Staff' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                    @csrf
                    <button type="submit" title="Sign out" class="text-white/30 hover:text-red-400 transition-colors">
                        <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
                            <path d="M6 14H3a1 1 0 01-1-1V3a1 1 0 011-1h3M10.5 11.5L14 8l-3.5-3.5M14 8H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ═══════════════════════════════
         MAIN AREA
    ═══════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Top Bar --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
            <div>
                <h1 class="text-[20px] font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                <p class="text-[13px] text-gray-400 mt-0.5">@yield('subtitle', '')</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-sky flex items-center justify-center text-white text-[13px] font-bold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="text-right">
                    <p class="text-[13px] font-semibold text-gray-800">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[12px] text-gray-400">{{ Auth::user()->role ?? 'Staff' }}</p>
                </div>
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="text-gray-400">
                    <path d="M3 5l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-gray-50 p-8">
            @yield('content')
        </main>

    </div>

</div>

@stack('scripts')
</body>
</html>