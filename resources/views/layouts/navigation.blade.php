@php
    $homeRoute = match (true) {
        Auth::user()->can('projects.view') => 'projects.index',
        Auth::user()->can('candidates.view') => 'candidates.index',
        default => 'applications.index',
    };

    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'can' => 'projects.view'],
        ['label' => 'Projects', 'route' => 'projects.index', 'match' => 'projects.*', 'can' => 'projects.view'],
        ['label' => 'Candidates', 'route' => 'candidates.index', 'match' => 'candidates.*', 'can' => 'candidates.view'],
        ['label' => 'Applications', 'route' => 'applications.index', 'match' => 'applications.*', 'can' => 'applications.view'],
        ['label' => 'Job Posts', 'route' => 'jobs.index', 'match' => 'jobs.*', 'can' => 'jobs.view'],
        ['label' => 'Job Offers', 'route' => 'job-offers.index', 'match' => 'job-offers.*', 'can' => 'offers.view'],
        ['label' => 'Analytics', 'route' => 'admin.analytics', 'match' => 'admin.analytics', 'can' => 'analytics.view'],
    ];

    $adminItems = [
        ['label' => 'Manage Users', 'route' => 'admin.users.index', 'match' => 'admin.users.*'],
        ['label' => 'Roles & Permissions', 'route' => 'admin.roles.index', 'match' => 'admin.roles.*'],
        ['label' => 'Project Teams', 'route' => 'admin.project-teams.index', 'match' => 'admin.project-teams.*'],
    ];
@endphp

<div class="lg:fixed lg:inset-y-0 lg:z-30 lg:flex lg:w-64 lg:flex-col">
    <!-- Mobile overlay -->
    <div class="fixed inset-0 z-40 flex lg:hidden" x-show="sidebarOpen" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60" @click="sidebarOpen = false"></div>

        <div class="relative ml-0 flex w-64 flex-1">
            @include('layouts.sidebar-menu', [
                'homeRoute' => $homeRoute,
                'navItems' => $navItems,
                'adminItems' => $adminItems,
                'isMobile' => true,
            ])
        </div>
    </div>

    <!-- Desktop sidebar -->
    <aside class="hidden h-full w-64 flex-1 border-r border-slate-200 bg-white lg:flex">
        @include('layouts.sidebar-menu', [
            'homeRoute' => $homeRoute,
            'navItems' => $navItems,
            'adminItems' => $adminItems,
            'isMobile' => false,
        ])
    </aside>
</div>
