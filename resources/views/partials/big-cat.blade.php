{{-- 登入頁大貓咪：眼睛跟著滑鼠，輸入密碼時舉手遮眼（見 app.js 與 app.css） --}}
<svg class="{{ $small ?? false ? 'big-cat big-cat-sm' : 'big-cat' }}" viewBox="0 0 220 210" aria-hidden="true">
    <path d="M38 78 L20 14 L82 44 Z" fill="#e2e8f0"/>
    <path d="M182 78 L200 14 L138 44 Z" fill="#e2e8f0"/>
    <path d="M42 70 L31 27 L72 47 Z" fill="#f9a8c0"/>
    <path d="M178 70 L189 27 L148 47 Z" fill="#f9a8c0"/>

    <ellipse cx="110" cy="118" rx="88" ry="82" fill="#eef2f7"/>

    <g class="cat-eye">
        <circle cx="75" cy="103" r="17" fill="#fff"/>
        <circle class="cat-pupil" cx="75" cy="105" r="9" fill="#1e293b"/>
        <circle cx="79" cy="99" r="3" fill="#fff"/>
    </g>
    <g class="cat-eye">
        <circle cx="145" cy="103" r="17" fill="#fff"/>
        <circle class="cat-pupil" cx="145" cy="105" r="9" fill="#1e293b"/>
        <circle cx="149" cy="99" r="3" fill="#fff"/>
    </g>

    <circle cx="52" cy="132" r="10" fill="#fbcfe8" opacity="0.8"/>
    <circle cx="168" cy="132" r="10" fill="#fbcfe8" opacity="0.8"/>

    <path d="M103 130 L117 130 L110 139 Z" fill="#f472a3"/>
    <path d="M110 139 Q102 150 92 145 M110 139 Q118 150 128 145" stroke="#475569" stroke-width="2.4" fill="none" stroke-linecap="round"/>

    <path d="M18 112 Q40 114 52 118 M16 126 Q38 126 51 126 M202 112 Q180 114 168 118 M204 126 Q182 126 169 126" stroke="#94a3b8" stroke-width="1.8" fill="none" stroke-linecap="round"/>

    <g class="cat-paw cat-paw-left">
        <ellipse cx="62" cy="198" rx="24" ry="16" fill="#e2e8f0"/>
        <path d="M50 190 L50 198 M62 188 L62 198 M74 190 L74 198" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round"/>
    </g>
    <g class="cat-paw cat-paw-right">
        <ellipse cx="158" cy="198" rx="24" ry="16" fill="#e2e8f0"/>
        <path d="M146 190 L146 198 M158 188 L158 198 M170 190 L170 198" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round"/>
    </g>
</svg>
