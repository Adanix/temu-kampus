# Page Override: Item Detail Split-Pane (`/items/{item}`) — SCR-07

- Inherits `design-system/temukampus/MASTER.md`.
- Editorial split-pane (`grid-cols-1 lg:grid-cols-[minmax(0,1fr)_22rem] gap-6`):
  - Left column (`min-w-0`): media/privacy shield (`allowToggle` ONLY if `@can('viewSensitivePhoto', $item)`), description, structured metadata grid, Custody Post details, and authorized Hidden Traits card (`@can('viewHiddenTraits', $item)`).
  - Right column (`min-w-0`): sticky status & action panel (Blind Challenge claim form, active claim status + Handover PIN display for claimant, Finder PIN verification box when `with_finder`, Edit/Cancel/Archive controls), plus Copy Reference & Print Card controls (`.no-print`).
