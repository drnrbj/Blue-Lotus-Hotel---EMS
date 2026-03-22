<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Blue Lotus Hotel</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy:      '#0F2453',
                        'navy-deep': '#0a1a3d',
                        sky:       '#45AEE4',
                    },
                    fontFamily: {
                        serif: ['"Merriweather"', 'Georgia', 'serif'],
                        sans:  ['"Open Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,400;0,700;1,400&family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Open Sans', sans-serif; }

        .left-grid {
            position: absolute; inset: 0;
            background-image:
                repeating-linear-gradient(0deg,  rgba(69,174,228,0.045) 0, rgba(69,174,228,0.045) 1px, transparent 1px, transparent 52px),
                repeating-linear-gradient(90deg, rgba(69,174,228,0.045) 0, rgba(69,174,228,0.045) 1px, transparent 1px, transparent 52px);
        }

        .field-input {
            width: 100%; height: 46px;
            border: 1px solid #dde4f0;
            border-radius: 3px;
            padding: 0 14px;
            font-family: 'Open Sans', sans-serif;
            font-size: 14px;
            color: #0F2453;
            background: #fafbfd;
            outline: none;
            transition: border-color .2s, background .2s;
        }
        .field-input:focus      { border-color: #45AEE4; background: #fff; }
        .field-input.is-invalid { border-color: #ef4444; }
        .field-input::placeholder { color: #c5cfe0; }

        .btn-signin { transition: background .2s, transform .1s; }
        .btn-signin:hover  { background: #1a3570 !important; }
        .btn-signin:active { transform: scale(0.99); }
    </style>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">

<div class="flex w-full max-w-[820px] min-h-[500px] bg-white shadow-xl overflow-hidden" style="border-radius:4px;">

    {{-- ── Left Branding Panel ── --}}
    <div class="relative hidden md:flex flex-col justify-between w-[38%] bg-navy px-10 py-14 overflow-hidden">
        <div class="left-grid"></div>

        {{-- Brand --}}
        <div class="relative z-10">
            <div class="w-7 h-0.5 bg-sky mb-7"></div>
            <h1 class="font-serif text-white text-[30px] font-medium leading-snug tracking-wide">
                Blue Lotus<br>Hotel
            </h1>
            <p class="text-sky text-[10px] tracking-[0.22em] uppercase mt-3 font-sans font-light">
                Employee Management
            </p>
        </div>
    </div>

    {{-- ── Right Form Panel ── --}}
    <div class="flex-1 flex flex-col justify-center px-12 py-14">

        <h2 class="font-serif text-navy text-[28px] font-medium tracking-wide mb-1">Sign In</h2>
        <p class="text-slate-400 text-[13px] font-light mb-8">Access your management portal</p>

        <hr class="border-slate-100 mb-8">

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-[13px]" style="border-radius:3px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-5">
                <label for="email" class="block font-sans font-medium text-navy text-[10px] tracking-[0.18em] uppercase mb-2">
                    Email Address
                </label>
                <input
                    class="field-input @error('email') is-invalid @enderror"
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@bluelotus.com"
                    autocomplete="email"
                    autofocus
                >
                @error('email')
                    <p class="mt-1.5 text-red-500 text-[12px]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-6">
                <label for="password" class="block font-sans font-medium text-navy text-[10px] tracking-[0.18em] uppercase mb-2">
                    Password
                </label>
                <input
                    class="field-input @error('password') is-invalid @enderror"
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••••"
                    autocomplete="current-password"
                >
                @error('password')
                    <p class="mt-1.5 text-red-500 text-[12px]">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember + Forgot --}}
            <div class="flex items-center justify-between mb-8">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="remember"
                        class="w-3.5 h-3.5 accent-sky"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <span class="text-slate-400 text-[12.5px] font-light">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sky text-[12.5px] hover:underline">
                        Forgot password?
                    </a>
                @endif
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="btn-signin w-full h-[46px] bg-navy text-white font-sans text-[12px] font-medium tracking-[0.18em] uppercase cursor-pointer border-0"
                style="border-radius:3px;"
            >
                Sign In
            </button>
        </form>

        <p class="text-center mt-8 text-[11px] text-slate-300 tracking-wide">
            Blue Lotus Hotel &copy; {{ date('Y') }} &nbsp;&middot;&nbsp; Confidential Staff Portal
        </p>
    </div>

</div>

</body>
</html>