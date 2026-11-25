# Page Override: Admin Analytics (`/admin/analytics`) — SCR-11

- Inherits `design-system/temukampus/MASTER.md`.
- Top 4-card KPI strip (`grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4`, `tabular-nums` large figures).
- Middle two-column analytical row (`grid-cols-1 lg:grid-cols-2 gap-6`) using accessible pure HTML/CSS horizontal proportional bars for Top 5 Building Loss Hotspots & Status Breakdown.
- Bottom full-width Item Ledger wrapped in Table Strategy B isolated scrollable frame (`w-full max-w-full overflow-x-auto overscroll-x-contain rounded-xl border border-border-subtle`) inside a `min-w-0` parent, plus UTF-8 Export CSV button.
