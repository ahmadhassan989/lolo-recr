<div class="rounded-xl bg-white p-6 shadow space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-slate-800">Tags</h3>
        @cannot('candidates.manage')
            <span class="text-xs text-gray-500">View only</span>
        @endcannot
    </div>

    <div class="flex flex-wrap gap-2">
        @forelse ($candidate->tags as $tag)
            <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                {{ $tag->name }}
            </span>
        @empty
            <p class="text-sm text-gray-500">No tags assigned.</p>
        @endforelse
    </div>

    @can('candidates.manage')
        <form action="{{ route('candidates.tags.update', $candidate) }}" method="POST" class="space-y-3">
            @csrf
            <label class="block text-xs font-medium text-gray-600 uppercase">Update Tags</label>
            <select
                name="tags[]"
                multiple
                class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                size="6"
            >
                @foreach ($allTags as $tag)
                    <option value="{{ $tag->id }}" @selected($candidate->tags->contains($tag))>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple tags.</p>
            <button type="submit" class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                Sync Tags
            </button>
        </form>
    @endcan
</div>
