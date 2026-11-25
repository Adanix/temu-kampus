# Page Override: Custodian Desk (`/custodian/desk`) — SCR-10

- Inherits `design-system/temukampus/MASTER.md`.
- Sectioned operational workbench: (1) Pending Blind Challenge Claims, (2) Approved Claims Awaiting PIN Handover, (3) Deposited Items at Custody Posts.
- Pending claim review displays side-by-side comparison boxes: Secret Hidden Traits (Forest-tinted panel) vs Claimant Blind Answer (Stone panel).
- Uses Table Strategy A: `< md` renders structured stacked cards; `>= md` renders clean tables/panels.
- Handover PIN input uses centered `font-mono tracking-[0.25em] text-center` 6-digit field; never exposes the expected `handover_pin` in HTML.
