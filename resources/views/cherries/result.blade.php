@extends('layouts.app2')

@section('content')
    <style>
        body:has(.cherry-awards) { overflow: hidden; background: #12070d; }
        body:has(.cherry-awards) .main-container, body:has(.cherry-awards) .main-content { width: 100vw !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
        body:has(.cherry-awards) .dvanimation, body:has(.cherry-awards) [x-data="basic"] { min-height: 100svh; padding: 0 !important; }
        body:has(.cherry-awards) .fixed.bottom-6 { display: none !important; }
        .cherry-awards { --red:#ed174c; --cream:#fff7e6; position:fixed; inset:0; z-index:80; display:grid; grid-template-rows:auto 1fr auto; min-height:100svh; overflow:hidden; background:radial-gradient(circle at 50% 35%,rgba(237,23,76,.24),transparent 32%),linear-gradient(135deg,#13070d,#280913 52%,#10050a); color:white; padding:clamp(16px,3vw,38px); }
        .cherry-awards::before { content:''; position:absolute; inset:0; opacity:.12; background-image:linear-gradient(rgba(255,255,255,.18) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.18) 1px,transparent 1px); background-size:64px 64px; pointer-events:none; }
        .cherry-awards header,.cherry-awards footer { position:relative; z-index:3; display:flex; align-items:center; justify-content:space-between; gap:20px; }
        .cherry-brand { color:#ffbdcc; font-size:.78rem; font-weight:900; letter-spacing:.18em; text-transform:uppercase; }
        .cherry-awards h1 { margin:.15rem 0 0; font-size:clamp(1.7rem,4vw,3.5rem); font-weight:900; line-height:1; }
        .cherry-total { border:1px solid rgba(255,255,255,.18); border-radius:999px; background:rgba(0,0,0,.2); padding:10px 16px; font-weight:900; }
        .award-center { position:relative; z-index:2; display:grid; min-height:0; place-items:center; }
        .award-intro { position:absolute; text-align:center; transition:opacity .5s ease,transform .5s ease; }
        .award-intro strong { display:block; font-size:clamp(3rem,11vw,9rem); font-weight:1000; letter-spacing:-.06em; line-height:.85; text-transform:uppercase; }
        .award-intro span { display:block; margin-top:20px; color:#ffbdcc; font-size:clamp(1rem,2vw,1.5rem); font-weight:800; }
        .winner-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:clamp(14px,3vw,36px); width:min(1180px,94vw); opacity:0; }
        .winner-card { position:relative; display:grid; min-height:min(58vh,650px); place-items:center; overflow:hidden; border:1px solid rgba(255,255,255,.18); border-radius:20px; background:rgba(255,255,255,.055); padding:clamp(18px,3vw,36px); text-align:center; opacity:0; transform:translateY(50px) scale(.94); }
        .winner-card::after { content:''; position:absolute; inset:auto -20% -55% -20%; height:75%; border-radius:50%; background:var(--red); opacity:.14; filter:blur(30px); }
        .winner-card img { position:relative; z-index:2; width:min(280px,54%); aspect-ratio:1; border:5px solid rgba(255,255,255,.85); border-radius:50%; background:white; object-fit:cover; box-shadow:0 22px 60px rgba(0,0,0,.4); }
        .winner-card.team img { border:0; border-radius:18%; background:transparent; object-fit:contain; box-shadow:none; }
        .winner-copy { position:relative; z-index:2; }
        .winner-label { color:#ffbdcc; font-size:clamp(.75rem,1.3vw,1rem); font-weight:900; letter-spacing:.13em; text-transform:uppercase; }
        .winner-name { margin:8px 0 0; font-size:clamp(1.8rem,4vw,4rem); font-weight:1000; line-height:.92; }
        .winner-score { margin-top:12px; color:var(--cream); font-size:clamp(1.1rem,2vw,1.6rem); font-weight:900; }
        .cherry-awards.is-running .award-intro,.cherry-awards.is-revealed .award-intro { opacity:0; transform:scale(.82); }
        .cherry-awards.is-running .winner-grid,.cherry-awards.is-revealed .winner-grid { opacity:1; }
        .cherry-awards.show-player .winner-card.player { animation:cardReveal .9s cubic-bezier(.16,1,.3,1) forwards; }
        .cherry-awards.show-team .winner-card.team { animation:cardReveal 1.05s cubic-bezier(.16,1,.3,1) forwards; }
        @keyframes cardReveal { to { opacity:1; transform:translateY(0) scale(1); box-shadow:0 25px 80px rgba(237,23,76,.18); } }
        .award-status { color:#ffbdcc; font-weight:800; }
        .reveal-button { min-height:52px; border:0; border-radius:12px; background:var(--red); color:white; cursor:pointer; font:inherit; font-size:1rem; font-weight:900; padding:0 28px; transition:transform .2s,opacity .2s; }
        .reveal-button:hover:not(:disabled) { transform:translateY(-2px); }
        .reveal-button:disabled { cursor:default; opacity:.7; }
        .empty-results { border:1px dashed rgba(255,255,255,.25); border-radius:18px; padding:42px; text-align:center; }
        @media(max-width:760px){ .cherry-awards{position:absolute;min-height:100svh;overflow:auto}.winner-grid{grid-template-columns:1fr}.winner-card{min-height:440px}.cherry-awards header,.cherry-awards footer{align-items:flex-start;flex-direction:column}.reveal-button{width:100%}.cherry-total{display:none} }
    </style>

    <section id="cherryAwards" class="cherry-awards" aria-label="Результати Черіків">
        <header>
            <div>
                <div class="cherry-brand">Cherry Camp · фінал заїзду</div>
                <h1>Черіки заїзду</h1>
            </div>
            <div class="cherry-total">Зібрано {{ number_format($grandTotal, 0, ',', ' ') }} Черіків</div>
        </header>

        <main class="award-center">
            @if($bestPlayer && $bestTeam)
                <div class="award-intro">
                    <strong>Хто<br>переміг?</strong>
                    <span>Результати вже готові</span>
                </div>

                <div class="winner-grid">
                    <article class="winner-card player">
                        <img src="{{ $bestPlayer->image_url }}" alt="{{ $bestPlayer->name }}">
                        <div class="winner-copy">
                            <div class="winner-label">Кращий гравець</div>
                            <h2 class="winner-name">{{ $bestPlayer->name }}</h2>
                            <div class="winner-score">{{ number_format($bestPlayer->cherries, 0, ',', ' ') }} Черіків</div>
                        </div>
                    </article>

                    <article class="winner-card team">
                        <img src="{{ $bestTeam['team']->element_logo_url }}" alt="{{ $bestTeam['team']->name }}">
                        <div class="winner-copy">
                            <div class="winner-label">Краща команда</div>
                            <h2 class="winner-name">{{ $bestTeam['team']->name }}</h2>
                            <div class="winner-score">{{ number_format($bestTeam['total'], 0, ',', ' ') }} Черіків</div>
                        </div>
                    </article>
                </div>
            @else
                <div class="empty-results">
                    <h2 class="text-3xl font-black">Результатів ще немає</h2>
                    <p class="mt-3 text-lg text-white-dark">Спочатку внесіть Черіки учасників і перевірте розподіл по командах.</p>
                </div>
            @endif
        </main>

        <footer>
            <p id="awardStatus" class="award-status">Результати приховано</p>
            <button id="revealAwards" class="reveal-button" type="button" @disabled(!$bestPlayer || !$bestTeam)>
                Показати переможців
            </button>
        </footer>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stage = document.getElementById('cherryAwards');
            const button = document.getElementById('revealAwards');
            const status = document.getElementById('awardStatus');
            if (!stage || !button || button.disabled) return;

            stage.addEventListener('contextmenu', (event) => event.preventDefault());
            button.addEventListener('click', () => {
                if (stage.classList.contains('is-running') || stage.classList.contains('is-revealed')) return;

                stage.classList.add('is-running');
                button.disabled = true;
                button.textContent = 'Відкриваємо…';
                status.textContent = 'Підраховуємо останні Черіки…';

                window.setTimeout(() => {
                    stage.classList.add('show-player');
                    status.textContent = 'Кращий гравець визначений';
                }, 1100);

                window.setTimeout(() => {
                    stage.classList.add('show-team', 'is-revealed');
                    status.textContent = 'Кращу команду оголошено';
                    button.textContent = 'Переможців оголошено';
                }, 3400);
            });
        });
    </script>
@endsection
