# DESIGN SYSTEM MASTER: TemuKampus — Modern Civic Utility

> Aligned with `nextlevelbuilder/ui-ux-pro-max-skill` Master + Page Overrides architecture and PRD Section 17.

## Visual Direction & Pattern
- **Pattern:** Search-First Civic Directory + Architectural Wayfinding + Split-Pane Detail
- **Visual Mood:** Warm archival paper, high-legibility ink, calm institutional authority
- **Stack:** Laravel 13 Blade + Tailwind CSS 4 (`@theme`) + Alpine.js + Inline Lucide SVG

## Color Palette (Light Mode Only — Verified WCAG AA >= 4.5:1 Text, >= 3:1 UI)
- **Canvas Background:** `#F7F6F2` (`bg-canvas`) — Warm Paper / Alabaster
- **Primary Surface:** `#FFFFFF` (`bg-surface`) — Crisp Card / Panel Surface
- **Subtle Surface:** `#EFEDE7` (`bg-surface-subtle`) — Warm Stone Tint (strips, headers, fallbacks)
- **Border Subtle:** `#E5E2DC` (`border-border-subtle`) — 1px Architectural Hairline
- **Border Strong:** `#C9C4BA` (`border-border-strong`) — Inputs & Interactive Hover
- **Ink Primary:** `#18181B` (`text-ink-primary`) — 16.2:1 on `#FFFFFF`, 15.0:1 on Canvas
- **Ink Secondary:** `#52525B` (`text-ink-secondary`) — 7.4:1 on `#FFFFFF`
- **Ink Muted:** `#6B6862` (`text-ink-muted`) — 5.1:1 on `#FFFFFF`, 4.7:1 on Canvas
- **Brand Primary:** `#14533C` (`bg-brand` / `text-brand`) — Deep Campus Forest (9.1:1 with `#FFFFFF`)
- **Brand Hover:** `#0F3F2D` (`hover:bg-brand-hover`)
- **Brand Tint:** `#E8F3EE` (`bg-brand-tint`)
- **Accent Signal:** `#C84B24` (`bg-signal` / `text-signal`) — Terracotta (Lost signal only)
- **Danger Primary:** `#B91C1C` (`bg-danger` / `text-danger`)

## Semantic Status Tokens (Always Pair With Explicit Text Label)
- **Lost:** `bg-status-lost-bg` (`#FDF1ED`) | `border-status-lost-border` (`#F5C9BA`) | `text-status-lost-text` (`#A63614`)
- **Found:** `bg-status-found-bg` (`#EBF7F1`) | `border-status-found-border` (`#B7E2CC`) | `text-status-found-text` (`#13533A`)
- **Claim Pending:** `bg-status-pending-bg` (`#FEF7E6`) | `border-status-pending-border` (`#F3D999`) | `text-status-pending-text` (`#8A5A00`)
- **Approved / PIN Ready:** `bg-status-approved-bg` (`#ECF3FE`) | `border-status-approved-border` (`#BFD6FA`) | `text-status-approved-text` (`#1D4ED8`)
- **Returned / Archived:** `bg-status-neutral-bg` (`#F1F0EC`) | `border-status-neutral-border` (`#D6D3CB`) | `text-status-neutral-text` (`#44403C`)
- **Rejected / Cancelled:** `bg-status-danger-bg` (`#FEF2F2`) | `border-status-danger-border` (`#FECACA`) | `text-status-danger-text` (`#B91C1C`)

## Typography Pairing
- **Display & UI Sans:** `'Plus Jakarta Sans'`, system sans fallback
- **Technical Metadata (`font-mono tabular-nums`):** `'IBM Plex Mono'`, system mono fallback — Ticket IDs (`#TK-2026-0042`), Claim IDs (`#CLM-2026-0001`), Zone Codes (`FT-01`), Handover PINs (`482-910`), Match Scores (`88%`), Timestamps, KPI numbers.

## 10-Priority Ruleset & Z-Index Ladder
- `z-10`: Sticky header, desktop TOC, sticky table headers
- `z-20`: Account dropdown, popovers
- `z-30`: Mobile drawer backdrop (`bg-ink-primary/40`)
- `z-40`: Mobile slide-over drawer panel
- `z-50`: Modals & confirmation dialogs (`max-h-[calc(100dvh-2rem)]`)
- Minimum `44×44px` (`min-h-11`) touch targets, `cursor-pointer` on all interactive controls, `focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2`, `prefers-reduced-motion` support.

## Strict Banlist (Zero Tolerance)
- ZERO emojis anywhere in UI or code — inline Lucide SVGs (`<x-ui.icon>`) ONLY
- ZERO purple/indigo/pink gradients or floating blurred blobs
- ZERO `h-screen` (`100vh`) — use `min-h-dvh`
- ZERO `hover:scale-*` on cards/rows
- ZERO `[DEMO]` prefixes or sticky yellow/red DEMO banners
