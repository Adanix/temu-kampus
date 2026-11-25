# Page Override: Demo Persona Login (`/demo/login`) — SCR-03

- Inherits `design-system/temukampus/MASTER.md`.
- Uses distraction-free `layouts.auth-demo` (`min-h-dvh flex flex-col bg-canvas`).
- Centered container `max-w-4xl w-full mx-auto my-auto py-8 px-[clamp(1rem,3vw,2rem)]`.
- Renders 4 equal-height persona cards in `grid-cols-1 md:grid-cols-2 gap-4` with typographic initials badge (`RP`, `RW`, `HK`, `NL`), role badge, campus unit, capability summary, and CSRF `POST /demo/quick-login` submit button.
- Zero `<input type="password">` credential forms; calm single-line subtitle & footnote.
