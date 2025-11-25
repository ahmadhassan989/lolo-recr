<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage {{ $user->name }}
            </h2>
            <p class="text-sm text-gray-500">Assign a system role to control access levels.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-800">
                    &larr; Back to Users
                </a>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                        <input type="text" value="{{ $user->name }}" disabled class="w-full rounded border border-slate-200 bg-slate-50 px-3 py-2 text-slate-600">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="text" value="{{ $user->email }}" disabled class="w-full rounded border border-slate-200 bg-slate-50 px-3 py-2 text-slate-600">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                        <select id="role" name="role" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected($user->hasRole($role))>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Project Creation Limit</label>
                        <input type="number" name="max_projects" value="{{ old('max_projects', $user->projectLimit->max_projects ?? '') }}" min="0" class="w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        <p class="text-xs text-slate-500 mt-1">Set how many projects this user can create (0 disables creation).</p>
                        @error('max_projects')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
