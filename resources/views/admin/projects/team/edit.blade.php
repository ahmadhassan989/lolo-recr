<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Team – {{ $project->title }}
            </h2>
            <p class="text-sm text-gray-500">Assign HRs and recruiters who can operate on this project.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div>
                <a href="{{ route('admin.project-teams.index') }}" class="inline-flex items-center text-sm text-slate-600 hover:text-slate-800">
                    &larr; Back to Project Teams
                </a>
            </div>

            @if (session('status'))
                <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $initialMembers = old('members', $project->team->map(fn ($member) => [
                    'user_id' => (string) $member->user_id,
                    'role' => $member->role,
                ])->values()->toArray());
                $userOptions = $users->map(fn ($user) => [
                    'id' => (string) $user->id,
                    'label' => $user->name . ' (' . $user->email . ')',
                ])->values();
            @endphp

            <div
                x-data="(() => ({
                    members: @js($initialMembers),
                    users: @js($userOptions),
                    addMember() {
                        this.members.push({ user_id: '', role: 'hr' });
                    },
                    removeMember(index) {
                        this.members.splice(index, 1);
                    }
                }))()"
                class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('admin.project-teams.update', $project) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <template x-if="members.length === 0">
                            <p class="text-sm text-slate-500">No members yet. Use the button below to add one.</p>
                        </template>

                        <template x-for="(member, index) in members" :key="index">
                            <div class="team-row flex flex-col gap-3 rounded-lg border border-slate-200 p-4 sm:flex-row sm:items-center">
                                <div class="flex-1">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">User</label>
                                    <select class="w-full rounded border border-slate-300 px-3 py-2"
                                            :name="`members[${index}][user_id]`"
                                            x-model="member.user_id">
                                        <option value="">Select user</option>
                                        <template x-for="userOption in users" :key="userOption.id">
                                            <option :value="userOption.id" x-text="userOption.label"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="w-full sm:w-48">
                                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Role</label>
                                    <select class="w-full rounded border border-slate-300 px-3 py-2"
                                            :name="`members[${index}][role]`"
                                            x-model="member.role">
                                        <option value="hr">HR</option>
                                        <option value="recruiter">Recruiter</option>
                                    </select>
                                </div>
                                <button type="button" class="text-sm text-red-600 hover:text-red-800" @click="removeMember(index)">
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4">
                        <button type="button" id="add-member" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="addMember()">
                            + Add Member
                        </button>
                    </div>

                    <div class="mt-6 space-y-2">
                        <label class="block text-sm font-semibold text-slate-700">Team Lead</label>
                        <p class="text-xs text-slate-500">Select a team member to act as the lead for this project.</p>
                        <select name="team_lead_id" class="w-full rounded border border-slate-300 px-3 py-2">
                            <option value="">Select team lead</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected((string) old('team_lead_id', $project->team_lead_id) === (string) $user->id)>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('team_lead_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.project-teams.index') }}" class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Cancel</a>
                        <button type="submit" class="rounded-md bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Save Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
