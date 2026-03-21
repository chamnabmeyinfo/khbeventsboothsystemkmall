/** Circular progress ring ~75% */
export function RingProgress({ percent = 75, color = '#7c3aed', track = '#e9e5f5' }) {
    const r = 22;
    const c = 2 * Math.PI * r;
    const offset = c * (1 - percent / 100);
    return (
        <svg width="56" height="56" viewBox="0 0 56 56" className="shrink-0" aria-hidden>
            <circle cx="28" cy="28" r={r} fill="none" stroke={track} strokeWidth="6" />
            <circle
                cx="28"
                cy="28"
                r={r}
                fill="none"
                stroke={color}
                strokeWidth="6"
                strokeLinecap="round"
                strokeDasharray={c}
                strokeDashoffset={offset}
                transform="rotate(-90 28 28)"
            />
        </svg>
    );
}

/** Simple sparkline */
export function Sparkline({ stroke, fill, gradId = 'sparkFill' }) {
    const d = 'M2 28 L10 22 L18 26 L26 14 L34 18 L42 8 L50 12';
    return (
        <svg width="64" height="36" viewBox="0 0 52 32" className="shrink-0" aria-hidden>
            <defs>
                <linearGradient id={gradId} x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor={fill} stopOpacity="0.35" />
                    <stop offset="100%" stopColor={fill} stopOpacity="0" />
                </linearGradient>
            </defs>
            <path d={`${d} L50 32 L2 32 Z`} fill={`url(#${gradId})`} />
            <path d={d} fill="none" stroke={stroke} strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
        </svg>
    );
}
