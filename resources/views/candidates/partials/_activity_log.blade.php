<div class="rounded-xl bg-white p-6 shadow space-y-4">
    <h3 class="text-sm font-semibold text-slate-800">Activity Log</h3>
    @if ($candidate->logs->isEmpty())
        <p class="text-sm text-gray-500">No activity recorded yet.</p>
    @else
        <ul class="space-y-3">
            @foreach ($candidate->logs as $log)
                <li class="border-b border-gray-100 pb-3 text-sm last:border-b-0 last:pb-0">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-slate-800">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </span>
                        <span class="text-xs text-gray-500">
                            {{ $log->user?->name ?? 'System' }}
                        </span>
                    </div>
                    <p class="mt-1 text-slate-700">{{ $log->action }}</p>
                    @if ($log->notes)
                        <p class="mt-1 text-xs text-gray-500 whitespace-pre-line">{{ $log->notes }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
