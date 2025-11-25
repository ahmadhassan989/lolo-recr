<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create User
            </h2>
            <p class="text-sm text-gray-500">Add a new user and assign their role.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Role</label>
                        <select name="role" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            <option value="">Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Project Creation Limit</label>
                        <input type="number" name="max_projects" value="{{ old('max_projects') }}" min="0" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500" placeholder="0 for no access">
                        <p class="text-xs text-slate-500 mt-1">Set how many projects this user can create (0 disables creation).</p>
                        @error('max_projects')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Password</label>
                            <input type="password" name="password" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 focus:border-slate-500 focus:ring-slate-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="{{ route('admin.users.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
