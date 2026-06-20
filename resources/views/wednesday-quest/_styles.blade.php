<style>
    .nevermore-quest {
        --quest-black: #050507;
        --quest-ink: #101019;
        --quest-wine: #611d36;
        --quest-violet: #49306f;
        --quest-silver: #ded8e7;
        --quest-muted: rgba(222, 216, 231, 0.68);
        min-height: calc(100vh - 48px);
        margin: -1.5rem;
        color: var(--quest-silver);
        background:
            linear-gradient(115deg, rgba(97, 29, 54, 0.34), transparent 36%),
            linear-gradient(245deg, rgba(73, 48, 111, 0.34), transparent 40%),
            repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.028) 0 1px, transparent 1px 44px),
            linear-gradient(180deg, #171520 0%, #050507 76%);
        font-family: Georgia, "Times New Roman", serif;
        overflow-x: hidden;
        position: relative;
    }

    .nevermore-quest::before,
    .nevermore-quest::after {
        content: "";
        position: fixed;
        inset: 0;
        pointer-events: none;
    }

    .nevermore-quest::before {
        background:
            linear-gradient(90deg, rgba(0, 0, 0, 0.76), transparent 18%, transparent 82%, rgba(0, 0, 0, 0.76)),
            linear-gradient(180deg, rgba(0, 0, 0, 0.62), transparent 38%, rgba(0, 0, 0, 0.72));
    }

    .nevermore-quest::after {
        opacity: 0.34;
        background:
            repeating-linear-gradient(0deg, transparent 0 18px, rgba(222, 216, 231, 0.045) 18px 19px),
            repeating-linear-gradient(90deg, transparent 0 86px, rgba(222, 216, 231, 0.05) 86px 87px);
        mix-blend-mode: screen;
    }

    .nevermore-wrap {
        position: relative;
        z-index: 1;
        width: min(100%, 1120px);
        min-height: calc(100vh - 48px);
        margin: 0 auto;
        display: grid;
        place-items: center;
        padding: clamp(18px, 5vw, 72px);
    }

    .nevermore-panel {
        width: 100%;
        border: 1px solid rgba(222, 216, 231, 0.24);
        background:
            linear-gradient(135deg, rgba(222, 216, 231, 0.12), rgba(222, 216, 231, 0.025)),
            rgba(5, 5, 7, 0.76);
        box-shadow:
            0 32px 90px rgba(0, 0, 0, 0.62),
            inset 0 0 0 1px rgba(255, 255, 255, 0.035);
        padding: clamp(10px, 2vw, 18px);
    }

    .nevermore-inner {
        position: relative;
        border: 1px solid rgba(222, 216, 231, 0.24);
        background:
            linear-gradient(180deg, rgba(16, 16, 25, 0.97), rgba(5, 5, 7, 0.94)),
            var(--quest-ink);
        padding: clamp(26px, 5vw, 60px);
        overflow: hidden;
    }

    .nevermore-inner::before,
    .nevermore-inner::after {
        content: "";
        position: absolute;
        left: clamp(18px, 4vw, 44px);
        right: clamp(18px, 4vw, 44px);
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(222, 216, 231, 0.52), transparent);
    }

    .nevermore-inner::before {
        top: 18px;
    }

    .nevermore-inner::after {
        bottom: 18px;
    }

    .nevermore-content {
        position: relative;
        z-index: 1;
    }

    .nevermore-crest {
        width: 62px;
        height: 62px;
        margin: 0 auto 20px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(222, 216, 231, 0.42);
        background:
            linear-gradient(135deg, rgba(97, 29, 54, 0.34), rgba(73, 48, 111, 0.24)),
            #07070d;
        color: #ffffff;
        font-size: 30px;
        font-weight: 800;
        box-shadow: 0 0 32px rgba(73, 48, 111, 0.42);
    }

    .nevermore-kicker {
        margin: 0 0 10px;
        color: var(--quest-muted);
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-align: center;
        text-transform: uppercase;
    }

    .nevermore-title {
        margin: 0;
        color: #ffffff;
        font-size: clamp(38px, 7vw, 84px);
        line-height: 0.95;
        text-align: center;
        text-shadow: 0 0 34px rgba(97, 29, 54, 0.35);
    }

    .nevermore-copy {
        width: min(100%, 560px);
        margin: 18px auto 0;
        color: rgba(246, 242, 249, 0.76);
        font-family: Arial, sans-serif;
        font-size: clamp(15px, 2vw, 18px);
        line-height: 1.6;
        text-align: center;
    }

    .nevermore-form {
        width: min(100%, 520px);
        margin: 34px auto 0;
        display: grid;
        gap: 14px;
    }

    .nevermore-label {
        color: rgba(246, 242, 249, 0.86);
        font-family: Arial, sans-serif;
        font-size: 13px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .nevermore-input {
        width: 100%;
        min-height: 58px;
        border: 1px solid rgba(222, 216, 231, 0.35);
        border-radius: 2px;
        color: #ffffff;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.07), rgba(255, 255, 255, 0.02)),
            #08080e;
        font-family: Arial, sans-serif;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: 0.08em;
        padding: 0 18px;
        text-align: center;
        box-shadow:
            inset 0 -18px 30px rgba(73, 48, 111, 0.15),
            0 12px 24px rgba(0, 0, 0, 0.28);
    }

    .nevermore-input:focus {
        outline: none;
        border-color: rgba(222, 216, 231, 0.85);
        box-shadow:
            inset 0 -18px 30px rgba(73, 48, 111, 0.2),
            0 0 0 3px rgba(97, 29, 54, 0.28),
            0 16px 30px rgba(0, 0, 0, 0.36);
    }

    .nevermore-button,
    .nevermore-safe-button {
        min-height: 56px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(222, 216, 231, 0.38);
        border-radius: 2px;
        color: #ffffff;
        background:
            linear-gradient(135deg, rgba(97, 29, 54, 0.98), rgba(73, 48, 111, 0.96)),
            #34142d;
        cursor: pointer;
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        box-shadow: 0 18px 34px rgba(0, 0, 0, 0.42);
        transition: filter 160ms ease, transform 160ms ease;
    }

    .nevermore-button:hover,
    .nevermore-safe-button:hover {
        filter: brightness(1.1);
        transform: translateY(-1px);
    }

    .nevermore-alert {
        width: min(100%, 560px);
        margin: 22px auto 0;
        border: 1px solid rgba(191, 58, 92, 0.56);
        background: rgba(97, 29, 54, 0.24);
        color: #ffd9e4;
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.45;
        padding: 12px 14px;
        text-align: center;
    }

    .route-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
        gap: clamp(18px, 3vw, 32px);
        align-items: stretch;
        margin-top: 34px;
    }

    .route-card {
        border: 1px solid rgba(222, 216, 231, 0.2);
        background: rgba(5, 5, 7, 0.52);
        padding: clamp(20px, 3vw, 32px);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.025);
    }

    .team-title {
        margin: 0;
        color: #ffffff;
        font-size: clamp(36px, 6vw, 70px);
        line-height: 1;
        text-align: center;
        text-shadow: 0 0 34px rgba(73, 48, 111, 0.42);
    }

    .hint-card {
        min-height: 260px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 1px solid rgba(222, 216, 231, 0.24);
        background:
            linear-gradient(135deg, rgba(97, 29, 54, 0.24), rgba(73, 48, 111, 0.18)),
            rgba(10, 10, 15, 0.82);
        padding: clamp(22px, 4vw, 40px);
        position: relative;
    }

    .hint-number {
        margin: 0 0 14px;
        color: rgba(222, 216, 231, 0.66);
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .hint-text {
        margin: 0;
        color: #ffffff;
        font-size: clamp(24px, 4vw, 44px);
        line-height: 1.18;
        white-space: pre-line;
    }

    .hint-placeholder {
        color: rgba(222, 216, 231, 0.72);
        font-family: Arial, sans-serif;
        font-size: clamp(16px, 2vw, 20px);
        line-height: 1.6;
        text-align: center;
    }

    .safe-panel {
        text-align: center;
    }

    .safe-panel .hint-text {
        margin-bottom: 28px;
    }

    .nevermore-safe-button {
        width: min(100%, 420px);
        min-height: 74px;
        padding: 0 28px;
        text-decoration: none;
    }

    .route-back {
        display: inline-flex;
        margin-bottom: 22px;
        color: rgba(222, 216, 231, 0.74);
        font-family: Arial, sans-serif;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-decoration: none;
        text-transform: uppercase;
    }

    .route-back:hover {
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .route-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .nevermore-wrap {
            padding: 14px;
        }

        .nevermore-inner {
            padding: 28px 16px;
        }

        .nevermore-input {
            font-size: 18px;
        }
    }
</style>
