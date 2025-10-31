<div class="flex flex-wrap gap-3 rounded-xl bg-white p-4 shadow">
    <a href="{{ route('admin.reports.candidates') }}"
       class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        ⬇️ Candidates by Month
    </a>
    <a href="{{ route('admin.reports.skills') }}"
       class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        ⬇️ Top Skills
    </a>
    <a href="{{ route('admin.reports.interviews') }}"
       class="inline-flex items-center rounded bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
        ⬇️ Interview Results
    </a>
    <a href="{{ route('admin.reports.recruiters') }}"
       class="inline-flex items-center rounded bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-200">
        📊 Recruiter Performance
    </a>
</div>
