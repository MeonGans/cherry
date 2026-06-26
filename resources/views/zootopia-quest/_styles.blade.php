<style>
    @media (min-width: 1024px) {
        body:has(.zootopia-quest) .main-container .main-content {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
    }

    .zootopia-quest {
        --zoo-navy: #08153a;
        --zoo-blue: #0b4c94;
        --zoo-cyan: #11b2be;
        --zoo-violet: #6b4bb5;
        --zoo-orange: #ffb23d;
        --zoo-pink: #ef4fd5;
        --zoo-ice: #eaf7ff;
        --zoo-muted: rgba(234, 247, 255, 0.72);
        min-height: calc(100vh - 48px);
        margin: -1.5rem;
        padding: clamp(10px, 2vw, 28px);
        color: var(--zoo-ice);
        background:
            radial-gradient(circle at 18% 20%, rgba(17, 178, 190, 0.25), transparent 28%),
            radial-gradient(circle at 74% 16%, rgba(239, 79, 213, 0.18), transparent 26%),
            linear-gradient(145deg, #063d64 0%, #0b1c56 48%, #0a4f97 100%);
        font-family: Nunito, Arial, sans-serif;
        overflow-x: hidden;
    }

    .zootopia-quest::before {
        content: "";
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 30% 80%, rgba(255, 178, 61, 0.25), transparent 10%),
            radial-gradient(circle at 70% 88%, rgba(17, 178, 190, 0.22), transparent 12%),
            repeating-radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.08) 0 2px, transparent 2px 22px);
        opacity: 0.42;
    }

    .zootopia-hero {
        position: relative;
        width: min(100%, 1920px);
        min-height: clamp(220px, 34vw, 540px);
        margin: 0 auto;
        border-radius: 0 0 44% 44% / 0 0 12% 12%;
        background:
            linear-gradient(180deg, rgba(8, 21, 58, 0) 42%, rgba(8, 21, 58, 0.96) 100%),
            url("{{ asset('assets/images/zootopia-police-server.webp') }}") center 36% / cover no-repeat;
        box-shadow: 0 28px 60px rgba(4, 15, 44, 0.42);
        overflow: hidden;
    }

    .zootopia-hero::after {
        content: "";
        position: absolute;
        inset: auto 0 -1px;
        height: 24%;
        background: linear-gradient(180deg, transparent, var(--zoo-navy));
        clip-path: polygon(0 0, 50% 56%, 100% 0, 100% 100%, 0 100%);
    }

    .zootopia-console {
        position: relative;
        z-index: 1;
        width: min(100%, 1880px);
        margin: clamp(-24px, -2vw, -8px) auto 0;
        border-radius: 34px;
        border: 1px solid rgba(169, 224, 255, 0.28);
        background:
            linear-gradient(126deg, rgba(17, 178, 190, 0.24), rgba(107, 75, 181, 0.32) 58%, rgba(11, 76, 148, 0.36)),
            rgba(8, 21, 58, 0.94);
        box-shadow:
            0 28px 80px rgba(3, 12, 37, 0.58),
            inset 0 0 0 1px rgba(255, 255, 255, 0.06);
        padding: clamp(18px, 2.8vw, 36px);
    }

    .zootopia-header,
    .zootopia-meta,
    .zootopia-terminal,
    .zootopia-input-wrap {
        border: 1px solid rgba(173, 229, 255, 0.22);
        background:
            linear-gradient(120deg, rgba(255, 255, 255, 0.14), rgba(255, 255, 255, 0.04)),
            rgba(15, 30, 79, 0.78);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.035);
    }

    .zootopia-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        min-height: 104px;
        border-radius: 28px;
        padding: clamp(14px, 2vw, 22px);
    }

    .zootopia-brand {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .zootopia-badge {
        flex: 0 0 auto;
        width: clamp(58px, 7vw, 76px);
        height: clamp(58px, 7vw, 76px);
        display: grid;
        place-items: center;
        border-radius: 18px;
        color: #19335c;
        background: linear-gradient(135deg, #ffd25d, #ff9f39);
        font-size: clamp(16px, 2vw, 22px);
        font-weight: 1000;
        box-shadow: 0 16px 28px rgba(255, 178, 61, 0.22);
    }

    .zootopia-kicker,
    .zootopia-meta p,
    .zootopia-label {
        margin: 0;
        color: rgba(234, 247, 255, 0.72);
        font-size: clamp(11px, 1.2vw, 15px);
        font-weight: 1000;
        letter-spacing: 0.12em;
        line-height: 1.35;
        text-transform: uppercase;
    }

    .zootopia-header h1 {
        margin: 5px 0 0;
        color: #ffffff;
        font-size: clamp(24px, 4vw, 42px);
        font-weight: 1000;
        line-height: 1.08;
        overflow-wrap: anywhere;
        text-transform: uppercase;
        text-shadow: 0 8px 24px rgba(0, 0, 0, 0.28);
    }

    .zootopia-status {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 52px;
        border: 1px solid rgba(93, 204, 255, 0.38);
        border-radius: 999px;
        padding: 0 22px;
        color: #ffffff;
        background: rgba(55, 129, 193, 0.44);
        font-size: 16px;
        font-weight: 1000;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .zootopia-status span {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #58f59e;
        box-shadow: 0 0 0 8px rgba(88, 245, 158, 0.14), 0 0 22px rgba(88, 245, 158, 0.76);
    }

    .zootopia-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(12px, 1.8vw, 20px);
        margin-top: 20px;
    }

    .zootopia-meta {
        min-height: 86px;
        border-radius: 22px;
        padding: 18px;
    }

    .zootopia-meta strong {
        display: block;
        margin-top: 8px;
        color: #ffffff;
        font-size: clamp(19px, 2.3vw, 27px);
        font-weight: 1000;
        line-height: 1.18;
        overflow-wrap: anywhere;
    }

    .zootopia-alert {
        margin-top: 20px;
        border: 1px solid rgba(255, 151, 110, 0.66);
        border-radius: 18px;
        background: rgba(119, 35, 55, 0.38);
        color: #ffe2da;
        font-size: 16px;
        font-weight: 800;
        line-height: 1.45;
        padding: 14px 18px;
    }

    .zootopia-terminal {
        min-height: clamp(260px, 34vw, 420px);
        margin-top: 22px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        gap: 16px;
        border-radius: 30px;
        padding: clamp(18px, 3vw, 32px);
        background:
            linear-gradient(125deg, rgba(17, 178, 190, 0.12), rgba(239, 79, 213, 0.10)),
            rgba(4, 15, 48, 0.82);
    }

    .zootopia-line {
        border: 1px solid rgba(93, 204, 255, 0.34);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.88);
        font-size: clamp(17px, 1.8vw, 22px);
        font-weight: 800;
        line-height: 1.45;
        padding: 15px 18px;
        box-shadow: inset 4px 0 0 rgba(255, 178, 61, 0.62);
    }

    .zootopia-evidence,
    .zootopia-final {
        min-height: 100%;
        display: grid;
        align-content: center;
        gap: 18px;
        border: 1px solid rgba(93, 204, 255, 0.28);
        border-radius: 24px;
        background:
            linear-gradient(135deg, rgba(255, 178, 61, 0.13), rgba(17, 178, 190, 0.12)),
            rgba(7, 20, 58, 0.72);
        padding: clamp(22px, 4vw, 44px);
    }

    .zootopia-hint {
        color: #ffffff;
        font-size: clamp(28px, 4vw, 54px);
        font-weight: 1000;
        line-height: 1.15;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    .zootopia-final {
        text-align: center;
    }

    .zootopia-final h2 {
        margin: 0;
        color: #ffffff;
        font-size: clamp(30px, 4.5vw, 58px);
        font-weight: 1000;
        line-height: 1.08;
    }

    .zootopia-command {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(190px, 270px);
        gap: 16px;
        align-items: stretch;
        margin-top: 20px;
    }

    .zootopia-input-wrap {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        align-items: center;
        gap: 12px;
        min-height: 76px;
        border-radius: 22px;
        padding: 0 22px;
    }

    .zootopia-input-wrap span {
        color: var(--zoo-orange);
        font-size: 24px;
        font-weight: 1000;
    }

    .zootopia-input-wrap input {
        width: 100%;
        min-width: 0;
        border: 0;
        color: #ffffff;
        background: transparent;
        font-size: clamp(18px, 2vw, 23px);
        font-weight: 900;
        outline: none;
    }

    .zootopia-input-wrap input::placeholder {
        color: rgba(234, 247, 255, 0.64);
    }

    .zootopia-primary,
    .zootopia-safe-link {
        min-height: 76px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 22px;
        color: #11162b;
        background: linear-gradient(135deg, #ffc044, #ff8d3d 42%, var(--zoo-pink) 74%, #8b58ff);
        cursor: pointer;
        font-size: clamp(15px, 1.6vw, 20px);
        font-weight: 1000;
        letter-spacing: 0.06em;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        box-shadow: 0 18px 34px rgba(3, 12, 37, 0.36);
        transition: filter 160ms ease, transform 160ms ease;
    }

    .zootopia-primary:hover,
    .zootopia-safe-link:hover,
    .zootopia-change:hover {
        filter: brightness(1.06);
        transform: translateY(-1px);
    }

    .zootopia-safe-link {
        width: min(100%, 420px);
        margin: 4px auto 0;
        color: #12162a;
    }

    .zootopia-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 16px;
    }

    .zootopia-change {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 56px;
        border: 1px solid rgba(93, 204, 255, 0.34);
        border-radius: 18px;
        color: #ffffff;
        background: rgba(38, 132, 202, 0.48);
        font-size: 16px;
        font-weight: 1000;
        padding: 0 24px;
        text-decoration: none;
        transition: filter 160ms ease, transform 160ms ease;
    }

    .zootopia-sr {
        position: absolute;
        width: 1px;
        height: 1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
    }

    @media (max-width: 900px) {
        .zootopia-header,
        .zootopia-brand {
            align-items: flex-start;
        }

        .zootopia-header,
        .zootopia-command {
            grid-template-columns: 1fr;
        }

        .zootopia-header {
            display: grid;
        }

        .zootopia-status {
            justify-self: start;
        }
    }

    @media (max-width: 680px) {
        .zootopia-quest {
            padding: 0;
        }

        .zootopia-hero {
            min-height: 240px;
            border-radius: 0;
            background-position: 50% 34%;
        }

        .zootopia-console {
            margin-top: -8px;
            border-radius: 24px 24px 0 0;
            padding: 14px;
        }

        .zootopia-meta-grid {
            grid-template-columns: 1fr;
        }

        .zootopia-brand {
            gap: 12px;
        }

        .zootopia-terminal {
            min-height: 280px;
        }

        .zootopia-primary,
        .zootopia-safe-link,
        .zootopia-input-wrap {
            min-height: 64px;
            border-radius: 18px;
        }
    }

    @media (max-width: 460px) {
        .zootopia-brand {
            display: grid;
        }

        .zootopia-badge {
            width: 58px;
            height: 58px;
        }

        .zootopia-header h1 {
            font-size: 23px;
        }
    }
</style>
