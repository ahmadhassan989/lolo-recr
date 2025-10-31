<div x-show="tab === 'attachments'" x-cloak class="space-y-6">
    @can('candidates.manage')
        <div>
            <h3 class="text-sm font-semibold text-slate-700 mb-3">Upload Attachment</h3>
            <form action="{{ route('attachments.store', $candidate) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-2">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">File</label>
                    <input
                        type="file"
                        name="file"
                        required
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Display Name</label>
                    <input
                        type="text"
                        name="file_name"
                        value="{{ old('file_name') }}"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                        placeholder="Optional friendly name"
                    >
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase">Type</label>
                    <select
                        name="type"
                        class="w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                    >
                        @foreach (['cv' => 'CV / Resume', 'certificate' => 'Certificate', 'id' => 'Identification', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', 'other') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        Upload File
                    </button>
                </div>
            </form>
        </div>
    @endcan

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-2 text-left">File</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Uploaded</th>
                    <th class="px-4 py-2 text-left">By</th>
                    <th class="px-4 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($candidate->attachments as $attachment)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ Storage::disk('public')->url($attachment->file_path) }}" class="text-blue-600 hover:underline" target="_blank" rel="noopener">
                                {{ $attachment->file_name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($attachment->type) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $attachment->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $attachment->uploader?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('attachments.download', $attachment) }}" class="text-slate-700 hover:text-slate-900">
                                    Download
                                </a>
                                @can('candidates.manage')
                                    <form action="{{ route('attachments.destroy', $attachment) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800" onclick="return confirm('Delete this attachment?')">
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                            No attachments uploaded.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
