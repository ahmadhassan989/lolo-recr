@can('candidates.manage')
    <form action="{{ route('candidates.rate', $candidate) }}" method="POST" class="rounded-xl bg-white p-6 shadow space-y-4">
        @csrf
        <div class="flex items-center gap-3">
            <label for="candidate-rating" class="text-sm font-semibold text-slate-800">Rating</label>
            <select
                id="candidate-rating"
                name="rating"
                class="rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
            >
                @for ($i = 0; $i <= 5; $i++)
                    <option value="{{ $i }}" @selected($candidate->rating === $i)>{{ $i }} ★</option>
                @endfor
            </select>
            <button type="submit" class="inline-flex items-center rounded bg-slate-800 px-3 py-1.5 text-sm font-semibold text-white hover:bg-slate-700">
                Save
            </button>
        </div>
        <p class="text-xs text-gray-500">
            Ratings help recruiters quickly gauge candidate suitability. Updating the rating will leave a note in the activity log.
        </p>
    </form>
@else
    <div class="rounded-xl bg-white p-6 shadow">
        <h3 class="text-sm font-semibold text-slate-800 mb-2">Rating</h3>
        <p class="text-lg font-semibold text-slate-900">
            {{ $candidate->rating ?? '—' }} <span class="text-base text-amber-500">★</span>
        </p>
    </div>
@endcan
