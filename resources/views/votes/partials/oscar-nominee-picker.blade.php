<style>
    .nominee-picker-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(145px,1fr)); gap:12px; }
    .nominee-picker-input { position:absolute; width:1px; height:1px; opacity:0; }
    .nominee-picker-card { position:relative; display:block; overflow:hidden; border:2px solid transparent; border-radius:14px; background:#fff; cursor:pointer; box-shadow:0 5px 18px rgba(0,0,0,.08); transition:.16s ease; }
    .nominee-picker-card img { display:block; width:100%; aspect-ratio:1/1; object-fit:cover; background:#eee; }
    .nominee-picker-name { display:flex; min-height:52px; align-items:center; justify-content:center; padding:8px; color:#111827; font-weight:700; line-height:1.2; text-align:center; }
    .nominee-picker-check { position:absolute; top:8px; right:8px; display:none; width:28px; height:28px; place-items:center; border-radius:999px; background:#e2a03f; color:#fff; font-weight:900; }
    .nominee-picker-input:checked + .nominee-picker-card { border-color:#e2a03f; transform:translateY(-2px); box-shadow:0 8px 22px rgba(226,160,63,.28); }
    .nominee-picker-input:checked + .nominee-picker-card .nominee-picker-check { display:grid; }
    .nominee-picker-input:focus-visible + .nominee-picker-card { outline:3px solid rgba(67,97,238,.3); }
</style>

<div class="mt-5 space-y-5">
    @foreach(\App\Models\Vote::OSCAR_NOMINATIONS as $key => $nomination)
        @php
            $candidates = $oscarCandidatesByNomination[$key] ?? collect();
            $limit = $oscarSelectionLimits[$key] ?? $nomination['limit'];
            $selected = collect(old("oscar_nominees.{$key}", $selectedNominees[$key] ?? []))
                ->map(fn ($id) => (string) $id);
        @endphp

        <section class="rounded border border-white-light p-3 dark:border-[#191e3a]" data-nominee-section>
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h6 class="font-semibold text-black dark:text-white">{{ $nomination['title'] }}</h6>
                    <p class="text-xs text-white-dark">
                        Оберіть щонайменше {{ $limit }}
                        @if($nomination['gender'] && $candidates->count() < 5)
                            <span class="block">У заїзді менше 5 учасників цієї статі</span>
                        @endif
                    </p>
                </div>
                <span class="badge bg-warning nominee-selected-count">0 обрано</span>
            </div>

            @if($candidates->isEmpty())
                <p class="text-sm font-semibold text-danger">Немає кандидатів для цієї номінації.</p>
            @else
                <div class="nominee-picker-grid">
                    @foreach($candidates as $candidate)
                        <label class="relative">
                            <input
                                class="nominee-picker-input"
                                type="checkbox"
                                name="oscar_nominees[{{ $key }}][]"
                                value="{{ $candidate->id }}"
                                @checked($selected->contains((string) $candidate->id))
                            >
                            <span class="nominee-picker-card">
                                <img src="{{ $candidate->image_url }}" alt="{{ $candidate->name }}" loading="lazy">
                                <span class="nominee-picker-check" aria-hidden="true">✓</span>
                                <span class="nominee-picker-name">{{ $candidate->name }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif
        </section>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-nominee-section]').forEach((section) => {
            const inputs = [...section.querySelectorAll('.nominee-picker-input')];
            const counter = section.querySelector('.nominee-selected-count');
            const sync = () => counter.textContent = `${inputs.filter(input => input.checked).length} обрано`;
            inputs.forEach(input => input.addEventListener('change', sync));
            sync();
        });
    });
</script>
