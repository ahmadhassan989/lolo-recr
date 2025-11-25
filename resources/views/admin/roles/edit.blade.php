<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }} Permissions
                </h2>
                <p class="text-sm text-gray-500">Toggle the permissions granted to this role.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div>
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-800">
                    &larr; Back to Roles
                </a>
            </div>

            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center space-x-3 rounded border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                                <input
                                    type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->name }}"
                                    class="rounded border-slate-300 text-slate-700 focus:ring-slate-500"
                                    @checked($role->hasPermissionTo($permission->name))
                                >
                                <span class="text-slate-700">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('permissions.*')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('admin.roles.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
