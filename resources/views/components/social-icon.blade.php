<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@if($name === 'whatsapp')
<path d="M20.5 11.5a8.5 8.5 0 0 1-12.7 7.4L3 20l1.2-4.6a8.5 8.5 0 1 1 16.3-3.9Z"/><path d="m8 7 1.5-.2 1.1 2.5-1.1 1.1a9 9 0 0 0 3.5 3.3l1-1.2 2.5 1.2-.2 1.5c-.2 1.4-2.2 1.6-4.8.1C8.6 13.6 6.4 9 8 7Z"/>
@elseif($name === 'instagram')
<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>
@else
<circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18M5 6.5h14M5 17.5h14"/>
@endif
</svg>
