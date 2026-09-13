<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pixel Positions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-black text-white font-hanken-grotesk pb-20">
    <div class="px-10">
        <nav class="flex justify-between items-center py-4 border-b border-white/10">
            <div>
                <a href="/">
                    <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="">
                </a>
            </div>

            <div class="space-x-6 font-bold">
                <a href="/">Jobs</a>
                <a href="{{ route('careers.index') }}">Careers</a>
                <a href="{{ route('salaries.index') }}">Salaries</a>
                <a href="{{ route('companies.index') }}">Companies</a>
            </div>

            @auth
                <div class="flex items-center gap-6 font-bold">
                    @if(auth()->user()->canPostJobs())
                        <a href="/jobs/create">Post a Job</a>
                    @endif

                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center" aria-label="Open profile menu">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
                            </svg>
                            <span class="sr-only">Profile menu</span>
                        </summary>

                        <div class="absolute right-0 z-10 mt-3 w-40 rounded-xl border border-white/10 bg-zinc-950 p-2 shadow-xl">
                            <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Profile</a>
                            <form method="POST" action="/logout">
                                @csrf
                                @method('DELETE')

                                <button class="block w-full rounded-lg px-3 py-2 text-left hover:bg-white/10">Log Out</button>
                            </form>
                        </div>
                    </details>
                </div>
            @endauth

            @guest
                <div class="space-x-6 font-bold">
                    <a href="/register">Sign Up</a>
                    <a href="/login">Log In</a>
                </div>
            @endguest
        </nav>

        <main class="mt-10 max-w-[986px] mx-auto">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
