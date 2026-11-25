# Page Override: Search-First Catalog (`/items`) — SCR-05

- Inherits `design-system/temukampus/MASTER.md`.
- Search-first header bar with search input (`min-h-11`) + Lost/Found quick toggle pills.
- Secondary filters (`category`, `zone_id`, `status`, `sort`) inline on `>= md` and collapsed behind an Alpine `Filter (N)` toggle button on `< md` (`min-h-11`).
- Fluid auto-fit card grid: `grid grid-cols-[repeat(auto-fit,minmax(min(100%,17.5rem),1fr))] gap-4` with `min-w-0` on every card.
- Sensitive items (`is_sensitive = true`) always render blurred without a reveal toggle in catalog cards; `hidden_traits` is never present in HTML.
