<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Role Permissions
                </h2>
                <p class="text-sm text-gray-500">Manage which permissions belong to each role.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="flex justify-end">
                <a href="{{ route('admin.roles.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800">
                    + New Role
                </a>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Permissions</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($roles as $role)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $role->permissions_count }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-sm font-semibold text-slate-700 hover:text-slate-900">
                                        Edit Permissions
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
