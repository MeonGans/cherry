@once
    @push('styles')
        <style>
            .points-page {
                display: grid;
                gap: 20px;
            }

            .points-hero,
            .points-card {
                border: 1px solid #e0e6ed;
                border-radius: 8px;
                background: #ffffff;
                box-shadow: 0 14px 34px rgba(31, 45, 61, 0.08);
            }

            .dark .points-hero,
            .dark .points-card {
                border-color: #191e3a;
                background: #0e1726;
            }

            .points-hero {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                padding: 20px;
            }

            .points-kicker {
                margin: 0 0 6px;
                color: #d9234e;
                font-size: 12px;
                font-weight: 900;
                text-transform: uppercase;
            }

            .points-title {
                margin: 0;
                color: #111827;
                font-size: clamp(24px, 4vw, 34px);
                font-weight: 900;
                line-height: 1.12;
            }

            .dark .points-title {
                color: #ffffff;
            }

            .points-copy {
                max-width: 650px;
                margin: 8px 0 0;
                color: #6b7280;
                font-weight: 700;
            }

            .points-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-end;
            }

            .points-error {
                border: 1px solid rgba(231, 81, 90, 0.24);
                border-radius: 8px;
                background: rgba(231, 81, 90, 0.08);
                color: #b4173d;
                font-weight: 800;
                padding: 14px 16px;
            }

            .points-success {
                border: 1px solid rgba(0, 171, 85, 0.24);
                border-radius: 8px;
                background: rgba(0, 171, 85, 0.08);
                color: #008f47;
                font-weight: 800;
                padding: 14px 16px;
            }

            .points-card {
                overflow: hidden;
            }

            .points-card-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
                border-bottom: 1px solid #e0e6ed;
                padding: 18px 20px;
            }

            .dark .points-card-header {
                border-color: #191e3a;
            }

            .points-card-title {
                margin: 0;
                color: #111827;
                font-size: 20px;
                font-weight: 900;
            }

            .dark .points-card-title {
                color: #ffffff;
            }

            .points-card-copy {
                margin: 5px 0 0;
                color: #6b7280;
                font-weight: 700;
            }

            .points-badge {
                display: inline-flex;
                min-height: 30px;
                align-items: center;
                border-radius: 999px;
                background: rgba(217, 35, 78, 0.1);
                color: #d9234e;
                font-size: 12px;
                font-weight: 900;
                padding: 0 11px;
                white-space: nowrap;
            }

            .points-choice-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
                gap: 14px;
                padding: 20px;
            }

            .points-choice {
                position: relative;
                min-width: 0;
            }

            .points-choice-input {
                position: absolute;
                width: 1px;
                height: 1px;
                opacity: 0;
                pointer-events: none;
            }

            .points-choice-card {
                position: relative;
                display: grid;
                min-height: 178px;
                align-content: start;
                justify-items: center;
                gap: 10px;
                overflow: hidden;
                border: 1px solid #e0e6ed;
                border-radius: 8px;
                background: #ffffff;
                cursor: pointer;
                padding: 16px;
                text-align: center;
                transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
            }

            .dark .points-choice-card {
                border-color: #191e3a;
                background: #111c32;
            }

            .points-choice-card:hover {
                border-color: var(--choice-color, #d9234e);
                box-shadow: 0 16px 32px rgba(31, 45, 61, 0.12);
                transform: translateY(-2px);
            }

            .points-choice-input:focus-visible + .points-choice-card {
                border-color: var(--choice-color, #d9234e);
                box-shadow: 0 0 0 4px rgba(217, 35, 78, 0.13);
            }

            .points-choice-input:checked + .points-choice-card {
                border-color: var(--choice-color, #d9234e);
                box-shadow: 0 18px 36px rgba(31, 45, 61, 0.16);
                transform: translateY(-2px);
            }

            .points-choice-input:checked + .points-choice-card .points-check {
                opacity: 1;
                transform: scale(1);
            }

            .points-score-card {
                cursor: default;
            }

            .points-score-card:hover {
                transform: none;
            }

            .points-check {
                position: absolute;
                top: 10px;
                right: 10px;
                display: grid;
                width: 28px;
                height: 28px;
                place-items: center;
                border-radius: 999px;
                background: #ffffff;
                color: var(--choice-color, #d9234e);
                font-weight: 900;
                opacity: 0;
                transform: scale(0.82);
                transition: opacity 160ms ease, transform 160ms ease;
                z-index: 2;
            }

            .team-choice-logo {
                display: grid;
                width: 78px;
                height: 78px;
                place-items: center;
                border-radius: 8px;
                background: rgba(67, 97, 238, 0.08);
            }

            .team-choice-logo img {
                width: 62px;
                height: 62px;
                object-fit: contain;
            }

            .photo-choice-card,
            .nominee-choice-card {
                padding: 0;
            }

            .photo-choice-card img,
            .nominee-choice-card img {
                width: 100%;
                aspect-ratio: 4 / 3;
                background: #f1f2f3;
                object-fit: cover;
            }

            .nominee-choice-card img {
                aspect-ratio: 1 / 1;
            }

            .points-choice-title {
                display: flex;
                min-height: 42px;
                align-items: center;
                justify-content: center;
                color: #111827;
                font-weight: 900;
                line-height: 1.22;
                padding: 0 8px;
            }

            .photo-choice-card .points-choice-title,
            .nominee-choice-card .points-choice-title {
                min-height: 54px;
                padding: 10px 12px;
            }

            .dark .points-choice-title {
                color: #ffffff;
            }

            .points-choice-meta {
                max-width: 100%;
                border-radius: 999px;
                background: rgba(31, 45, 61, 0.08);
                color: #6b7280;
                font-size: 12px;
                font-weight: 900;
                padding: 5px 10px;
            }

            .points-current-score {
                display: inline-flex;
                min-height: 28px;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(67, 97, 238, 0.1);
                color: #4361ee;
                font-size: 12px;
                font-weight: 900;
                padding: 0 10px;
            }

            .points-score-footer {
                width: 100%;
                border-top: 1px solid #e0e6ed;
                padding: 12px;
            }

            .dark .points-score-footer {
                border-color: #191e3a;
            }

            .points-score-label {
                display: block;
                margin-bottom: 6px;
                color: #111827;
                font-size: 12px;
                font-weight: 900;
                text-align: left;
            }

            .dark .points-score-label {
                color: #ffffff;
            }

            .points-score-input {
                text-align: center;
                font-size: 18px;
                font-weight: 900;
            }

            .points-submit-panel {
                display: grid;
                grid-template-columns: minmax(160px, 220px) auto;
                gap: 12px;
                align-items: end;
                border-top: 1px solid #e0e6ed;
                background: #f7f8fb;
                padding: 18px 20px;
            }

            .points-submit-panel-sticky {
                border: 1px solid #e0e6ed;
                border-radius: 8px;
                box-shadow: 0 14px 34px rgba(31, 45, 61, 0.08);
            }

            .dark .points-submit-panel-sticky {
                border-color: #191e3a;
            }

            .dark .points-submit-panel {
                border-color: #191e3a;
                background: #0b1220;
            }

            .points-label {
                display: block;
                margin-bottom: 7px;
                color: #111827;
                font-size: 13px;
                font-weight: 900;
            }

            .dark .points-label {
                color: #ffffff;
            }

            .points-submit-copy {
                margin: 0;
                color: #6b7280;
                font-size: 13px;
                font-weight: 700;
            }

            .points-empty {
                margin: 20px;
                border: 1px dashed #e0e6ed;
                border-radius: 8px;
                color: #6b7280;
                font-weight: 800;
                padding: 16px;
            }

            .oscar-admin-grid {
                grid-template-columns: repeat(auto-fit, minmax(145px, 1fr));
            }

            @media (max-width: 768px) {
                .points-hero,
                .points-card-header {
                    align-items: stretch;
                    flex-direction: column;
                }

                .points-actions {
                    justify-content: flex-start;
                }

                .points-submit-panel {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 520px) {
                .points-choice-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 10px;
                    padding: 14px;
                }

                .points-choice-card {
                    min-height: 156px;
                    padding: 12px 8px;
                }

                .team-choice-logo {
                    width: 64px;
                    height: 64px;
                }

                .team-choice-logo img {
                    width: 50px;
                    height: 50px;
                }

                .points-choice-title {
                    font-size: 13px;
                    padding-inline: 4px;
                }
            }
        </style>
    @endpush
@endonce
