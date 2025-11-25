@php
    $user = Auth::user();
    $navItems = $navItems ?? [];
    $adminItems = $adminItems ?? [];
    $homeRoute = $homeRoute ?? 'projects.index';
    $isMobile = $isMobile ?? false;
@endphp

<div class="flex h-full flex-col bg-white">
    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2 text-slate-800">
            <x-application-logo class="h-8 w-auto fill-current text-slate-900" />
            <span class="text-lg font-semibold">{{ config('app.name', 'Lolo Recruiting') }}</span>
        </a>
        @if ($isMobile)
            <button @click="sidebarOpen = false" class="rounded-md p-1.5 text-slate-500 hover:text-slate-800">
                <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    <nav class="flex-1 overflow-y-auto px-4 py-6">
        <div class="space-y-1">
            @foreach ($navItems as $item)
                @php
                    $allowed = true;
                    if (isset($item['can'])) {
                        $allowed = $user->can($item['can']);
                    }
                    if (isset($item['role'])) {
                        $allowed = $allowed && $user->hasRole($item['role']);
                    }
                @endphp
                @if ($allowed)
                    @php
                        $active = request()->routeIs($item['match']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ $active ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </div>

        @if ($user->hasRole('super_admin') && ! empty($adminItems))
            <div class="mt-6 text-xs font-semibold uppercase tracking-wide text-slate-400">
                Admin
            </div>
            <div class="mt-2 space-y-1">
                @foreach ($adminItems as $item)
                    @php
                        $active = request()->routeIs($item['match']);
                    @endphp
                    <a href="{{ route($item['route']) }}"
                        class="flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ $active ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        @endif
    </nav>

    <div class="border-t border-slate-200 px-5 py-4">
        <div class="text-sm font-semibold text-slate-800">{{ $user->name }}</div>
        <div class="text-xs text-slate-500">{{ $user->email }}</div>
        <div class="mt-3 flex items-center gap-3 text-sm">
            <a href="{{ route('profile.edit') }}" class="text-slate-600 hover:text-slate-900">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-slate-600 hover:text-slate-900">Logout</button>
            </form>
        </div>
    </div>
</div>
