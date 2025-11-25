<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Role
            </h2>
            <p class="text-sm text-gray-500">Define a new role and assign permissions.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Role Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Permissions</label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($permissions as $permission)
                                <label class="flex items-center gap-2 rounded border border-slate-200 px-3 py-2 text-sm">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="rounded border-slate-300 text-slate-700 focus:ring-slate-500" @checked(in_array($permission->name, old('permissions', [])))>
                                    <span>{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('permissions.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('admin.roles.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
