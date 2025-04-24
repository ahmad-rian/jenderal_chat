<header class="navbar bg-base-100 shadow-md">
    <div class="navbar-start">
        <h2 class="text-xl font-semibold">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                    </path>
                </svg>
                {{ config('app.name', 'Laravel') }}
            </a>
        </h2>
    </div>
    <div class="navbar-end">
        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    @if (auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div
                            class="bg-primary text-primary-content w-10 h-10 rounded-full flex items-center justify-center">
                            {{ auth()->user()->initials() }}
                        </div>
                    @endif
                </div>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li><a href="{{ route('settings.profile') }}">Pengaturan</a></li>
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left">Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
