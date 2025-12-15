# YMCA Website Services to Canvas Conversion Plan

Multi-phase plan for converting all block_content components from YMCA Website Services (Small Y) to Drupal Canvas SDC Organisms.

---

## Project Overview

| Attribute | Value |
|-----------|-------|
| **Source** | small-y-test (YMCA Website Services) |
| **Destination** | ymca-canvas-poc / ymca_ws_canvas_demo |
| **Tutorial Reference** | [podarok/trainings PR#5](https://github.com/podarok/trainings/pull/5) |
| **Total Components** | 50 block_content types |
| **Already Converted** | 1 (y-hero) |
| **Remaining** | 49 components |

---

## Component Inventory

### Category: Layout Builder Core Components (14)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 1 | lb_hero | `lb_hero` | ~~y-hero~~ | Done | - |
| 2 | lb_accordion | `lb_accordion` | y-accordion | Medium | 1 |
| 3 | lb_accordion | `accordion_item` | y-accordion-item | Low | 1 |
| 4 | lb_cards | `lb_cards` | y-cards | Medium | 1 |
| 5 | lb_cards | `card_item` | y-card-item | Low | 1 |
| 6 | lb_carousel | `lb_carousel` | y-carousel | High | 2 |
| 7 | lb_carousel | `carousel_item` | y-carousel-item | Low | 2 |
| 8 | lb_ping_pong | `lb_ping_pong` | y-ping-pong | Medium | 2 |
| 9 | lb_promo | `lb_promo` | y-promo | Medium | 2 |
| 10 | lb_modal | `lb_modal` | y-modal | High | 3 |
| 11 | lb_table | `lb_table` | y-table | Medium | 2 |
| 12 | lb_statistics | `lb_statistics` | y-statistics | Medium | 3 |
| 13 | lb_statistics | `statistics_item` | y-statistics-item | Low | 3 |
| 14 | lb_simple_menu | `lb_simple_menu` | y-simple-menu | Medium | 3 |

### Category: Grid & CTA Components (4)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 15 | lb_grid_cta | `lb_grid_cta` | y-grid-cta | Medium | 4 |
| 16 | lb_grid_cta | `grid_item` | y-grid-item | Low | 4 |
| 17 | lb_grid_icon | `lb_icon_grid` | y-icon-grid | Medium | 4 |
| 18 | lb_grid_icon | `icon_grid_item` | y-icon-grid-item | Low | 4 |

### Category: Testimonials & Partners (6)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 19 | lb_testimonial_blocks | `lb_testimonials` | y-testimonials | Medium | 4 |
| 20 | lb_testimonial_blocks | `testimonial_item` | y-testimonial-item | Low | 4 |
| 21 | lb_partners_blocks | `lb_partners` | y-partners | Medium | 5 |
| 22 | lb_partners_blocks | `lb_partner_item` | y-partner-item | Low | 5 |
| 23 | lb_partners_blocks | `partners_tier` | y-partners-tier | Low | 5 |
| 24 | lb_staff_members_blocks | `lb_staff_members` | y-staff-members | Medium | 5 |
| 25 | lb_staff_members_blocks | `lb_staff_member_item` | y-staff-member-item | Low | 5 |

### Category: Branch-Specific Components (4)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 26 | lb_branch_amenities_blocks | `lb_branch_amenities_blocks` | y-branch-amenities | Medium | 5 |
| 27 | lb_branch_social_links_blocks | `lb_branch_social_links_blocks` | y-branch-social | Low | 5 |
| 28 | lb_branch_hours_blocks | (via views) | y-branch-hours | Medium | 6 |
| 29 | openy_block_branch_amenities | `branch_amenities` | y-branch-amenities-legacy | Low | 6 |

### Category: Content Listing Components (9)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 30 | lb_related_articles_blocks | `lb_related_articles` | y-related-articles | Medium | 6 |
| 31 | lb_related_events_blocks | `lb_related_events` | y-related-events | Medium | 6 |
| 32 | y_lb_article | `articles_filter` | y-articles-filter | High | 7 |
| 33 | y_lb_article | `articles_listing` | y-articles-listing | High | 7 |
| 34 | y_lb_article | `lb_featured_articles` | y-featured-articles | Medium | 7 |
| 35 | ws_event | `events_filter` | y-events-filter | High | 7 |
| 36 | ws_event | `events_listing` | y-events-listing | High | 7 |
| 37 | ws_event | `featured_events` | y-featured-events | Medium | 7 |
| 38 | small_y_search | `lb_search_results` | y-search-results | High | 8 |

### Category: Tabs & Webform (3)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 39 | ws_lb_tabs | `lb_tabs` | y-tabs | High | 4 |
| 40 | ws_lb_tabs | `tab_item` | y-tab-item | Low | 4 |
| 41 | lb_webform | (via webform ref) | y-webform | Medium | 6 |

### Category: Donate Components (2)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 42 | lb_donate | `lb_donate` | y-donate | High | 8 |
| 43 | lb_donate | `donate_item` | y-donate-item | Low | 8 |

### Category: Small Y Specific (4)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 44 | ws_small_y_statistics | `small_y_statistics` | y-small-statistics | Medium | 8 |
| 45 | ws_small_y_statistics | `small_y_statistics_item` | y-small-statistics-item | Low | 8 |
| 46 | small_y_ping_pongs | `ping_pong_section` | y-ping-pong-section | Medium | 8 |
| 47 | y_lb_main_menu_cta_block | `menu_cta` | y-menu-cta | Low | 3 |

### Category: Schedules & Activity Finder (3)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 48 | lb_activity_finder | `lb_activity_finder` | y-activity-finder | High | 9 |
| 49 | lb_simple_schedule | `lb_simple_schedule` | y-simple-schedule | High | 9 |
| 50 | lb_repeat_schedules | `lb_repeat_schedules` | y-repeat-schedules | High | 9 |

### Category: Legacy/Utility Blocks (6)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 51 | openy_block_basic | `basic_block` | y-basic-block | Low | 10 |
| 52 | openy_block_custom_simple | `simple_block` | y-simple-block | Low | 10 |
| 53 | openy_block_date | `date_block` | y-date-block | Low | 10 |
| 54 | openy_block_flexible_content | `flexible_content` | y-flexible-content | Medium | 10 |
| 55 | openy_block_featured_highlights | `featured_highlights_block` | y-featured-highlights | Medium | 10 |
| 56 | openy_block_menu | `menu_block` | y-menu-block | Low | 10 |
| 57 | openy_code_block / ws_code_block | `code_block` / `lb_code_block` | y-code-block | Low | 10 |

### Category: Camp-Specific (2)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 58 | y_camp | `camp_menu_lb` | y-camp-menu | Medium | 10 |
| 59 | y_camp | `camp_quick_links` | y-camp-quick-links | Low | 10 |

### Category: Map (1)

| # | Source Module | Block Type | Canvas Target | Complexity | Sprint |
|---|---------------|------------|---------------|------------|--------|
| 60 | openy_map_lb | `lb_openy_map` | y-map | High | 9 |

---

## Phase Structure

```
Phase 0: Research & Setup (Complete)
   ↓
Phase 1: Foundation Components
   ↓
Phase 2: Media-Rich Components
   ↓
Phase 3: Interactive Components
   ↓
Phase 4: Grid & Container Components
   ↓
Phase 5: People & Partners
   ↓
Phase 6: Branch & Related Content
   ↓
Phase 7: Content Listings (Articles/Events)
   ↓
Phase 8: Specialized Components
   ↓
Phase 9: Complex Applications
   ↓
Phase 10: Legacy & Camp Components
```

---

## Sprint Breakdown

### Phase 1: Foundation Components (Sprint 1)

**Duration**: 1 week
**Components**: 4 (accordion, cards with items)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 1.1 | y-accordion | `.component.yml`, `.twig`, `.css` |
| 1.2 | y-accordion-item | `.component.yml`, `.twig`, `.css` |
| 1.3 | y-cards | `.component.yml`, `.twig`, `.css` |
| 1.4 | y-card-item | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 4 Canvas SDC components
- Library registrations
- Basic tests
- Demo content

**Success Criteria**:
- Components render in Canvas editor
- Style variations work
- Nested items display correctly

---

### Phase 2: Media-Rich Components (Sprint 2)

**Duration**: 1 week
**Components**: 5 (carousel, ping-pong, promo, table)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 2.1 | y-carousel | `.component.yml`, `.twig`, `.css`, `.js` |
| 2.2 | y-carousel-item | `.component.yml`, `.twig`, `.css` |
| 2.3 | y-ping-pong | `.component.yml`, `.twig`, `.css` |
| 2.4 | y-promo | `.component.yml`, `.twig`, `.css` |
| 2.5 | y-table | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 5 Canvas SDC components
- Swiper.js integration for carousel
- Responsive image handling

**Success Criteria**:
- Carousel slides/autoplay work
- Ping-pong layout alternates
- Tables are responsive

---

### Phase 3: Interactive Components (Sprint 3)

**Duration**: 1 week
**Components**: 5 (modal, statistics, simple-menu, menu-cta)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 3.1 | y-modal | `.component.yml`, `.twig`, `.css`, `.js` |
| 3.2 | y-statistics | `.component.yml`, `.twig`, `.css`, `.js` |
| 3.3 | y-statistics-item | `.component.yml`, `.twig`, `.css` |
| 3.4 | y-simple-menu | `.component.yml`, `.twig`, `.css` |
| 3.5 | y-menu-cta | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 5 Canvas SDC components
- Modal open/close functionality
- Counter animation for statistics

**Success Criteria**:
- Modal triggers correctly
- Statistics animate on scroll
- Menu renders navigation

---

### Phase 4: Grid & Container Components (Sprint 4)

**Duration**: 1 week
**Components**: 6 (grids, tabs, testimonials)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 4.1 | y-grid-cta | `.component.yml`, `.twig`, `.css` |
| 4.2 | y-grid-item | `.component.yml`, `.twig`, `.css` |
| 4.3 | y-icon-grid | `.component.yml`, `.twig`, `.css` |
| 4.4 | y-icon-grid-item | `.component.yml`, `.twig`, `.css` |
| 4.5 | y-tabs | `.component.yml`, `.twig`, `.css`, `.js` |
| 4.6 | y-tab-item | `.component.yml`, `.twig`, `.css` |
| 4.7 | y-testimonials | `.component.yml`, `.twig`, `.css` |
| 4.8 | y-testimonial-item | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 8 Canvas SDC components
- CSS Grid layouts
- Tab switching functionality

**Success Criteria**:
- Grids responsive across breakpoints
- Tabs accessible (ARIA)
- Testimonials cycle properly

---

### Phase 5: People & Partners (Sprint 5)

**Duration**: 1 week
**Components**: 7 (partners, staff, branch amenities/social)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 5.1 | y-partners | `.component.yml`, `.twig`, `.css` |
| 5.2 | y-partner-item | `.component.yml`, `.twig`, `.css` |
| 5.3 | y-partners-tier | `.component.yml`, `.twig`, `.css` |
| 5.4 | y-staff-members | `.component.yml`, `.twig`, `.css` |
| 5.5 | y-staff-member-item | `.component.yml`, `.twig`, `.css` |
| 5.6 | y-branch-amenities | `.component.yml`, `.twig`, `.css` |
| 5.7 | y-branch-social | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 7 Canvas SDC components
- Partner tier display logic
- Social icon integration

**Success Criteria**:
- Partner logos scale properly
- Staff cards display photos
- Social links functional

---

### Phase 6: Branch & Related Content (Sprint 6)

**Duration**: 1 week
**Components**: 5 (branch hours, related content, webform)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 6.1 | y-branch-hours | `.component.yml`, `.twig`, `.css` |
| 6.2 | y-branch-amenities-legacy | `.component.yml`, `.twig`, `.css` |
| 6.3 | y-related-articles | `.component.yml`, `.twig`, `.css` |
| 6.4 | y-related-events | `.component.yml`, `.twig`, `.css` |
| 6.5 | y-webform | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 5 Canvas SDC components
- Branch hours formatting
- Webform embed support

**Success Criteria**:
- Hours display today/week
- Related content links work
- Webform renders and submits

---

### Phase 7: Content Listings (Sprint 7)

**Duration**: 1.5 weeks
**Components**: 6 (article/event filters, listings, featured)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 7.1 | y-articles-filter | `.component.yml`, `.twig`, `.css`, `.js` |
| 7.2 | y-articles-listing | `.component.yml`, `.twig`, `.css` |
| 7.3 | y-featured-articles | `.component.yml`, `.twig`, `.css` |
| 7.4 | y-events-filter | `.component.yml`, `.twig`, `.css`, `.js` |
| 7.5 | y-events-listing | `.component.yml`, `.twig`, `.css` |
| 7.6 | y-featured-events | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 6 Canvas SDC components
- Filter/facet integration
- Pagination support

**Success Criteria**:
- Filters update listings
- Featured items highlight
- Load more works

---

### Phase 8: Specialized Components (Sprint 8)

**Duration**: 1.5 weeks
**Components**: 6 (search, donate, small-y specifics)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 8.1 | y-search-results | `.component.yml`, `.twig`, `.css`, `.js` |
| 8.2 | y-donate | `.component.yml`, `.twig`, `.css`, `.js` |
| 8.3 | y-donate-item | `.component.yml`, `.twig`, `.css` |
| 8.4 | y-small-statistics | `.component.yml`, `.twig`, `.css` |
| 8.5 | y-small-statistics-item | `.component.yml`, `.twig`, `.css` |
| 8.6 | y-ping-pong-section | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 6 Canvas SDC components
- Search API integration
- Donation form handling

**Success Criteria**:
- Search returns results
- Donation amounts configurable
- Stats render correctly

---

### Phase 9: Complex Applications (Sprint 9)

**Duration**: 2 weeks
**Components**: 4 (activity finder, schedules, map)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 9.1 | y-activity-finder | `.component.yml`, `.twig`, `.css`, `.js` |
| 9.2 | y-simple-schedule | `.component.yml`, `.twig`, `.css`, `.js` |
| 9.3 | y-repeat-schedules | `.component.yml`, `.twig`, `.css`, `.js` |
| 9.4 | y-map | `.component.yml`, `.twig`, `.css`, `.js` |

**Deliverables**:
- 4 Canvas SDC components
- Activity Finder React integration
- Schedule API connections
- Map library integration

**Success Criteria**:
- Activity Finder filters work
- Schedule displays correctly
- Map shows locations

---

### Phase 10: Legacy & Camp (Sprint 10)

**Duration**: 1 week
**Components**: 9 (legacy blocks, camp-specific)

| Task | Component | Files to Create |
|------|-----------|-----------------|
| 10.1 | y-basic-block | `.component.yml`, `.twig`, `.css` |
| 10.2 | y-simple-block | `.component.yml`, `.twig`, `.css` |
| 10.3 | y-date-block | `.component.yml`, `.twig`, `.css` |
| 10.4 | y-flexible-content | `.component.yml`, `.twig`, `.css` |
| 10.5 | y-featured-highlights | `.component.yml`, `.twig`, `.css` |
| 10.6 | y-menu-block | `.component.yml`, `.twig`, `.css` |
| 10.7 | y-code-block | `.component.yml`, `.twig`, `.css` |
| 10.8 | y-camp-menu | `.component.yml`, `.twig`, `.css` |
| 10.9 | y-camp-quick-links | `.component.yml`, `.twig`, `.css` |

**Deliverables**:
- 9 Canvas SDC components
- Legacy compatibility layer
- Camp-specific styling

**Success Criteria**:
- All legacy blocks convert
- Camp navigation works
- Code block syntax highlights

---

## Timeline Summary

| Phase | Sprint | Components | Duration |
|-------|--------|------------|----------|
| 1 | Sprint 1 | 4 | 1 week |
| 2 | Sprint 2 | 5 | 1 week |
| 3 | Sprint 3 | 5 | 1 week |
| 4 | Sprint 4 | 8 | 1 week |
| 5 | Sprint 5 | 7 | 1 week |
| 6 | Sprint 6 | 5 | 1 week |
| 7 | Sprint 7 | 6 | 1.5 weeks |
| 8 | Sprint 8 | 6 | 1.5 weeks |
| 9 | Sprint 9 | 4 | 2 weeks |
| 10 | Sprint 10 | 9 | 1 week |
| **Total** | **10 Sprints** | **59** | **~12 weeks** |

---

## Per-Component Conversion Checklist

For each component:

- [ ] Analyze source block_content type fields
- [ ] Create `.component.yml` with Canvas schema
- [ ] Create `.twig` template with BEM classes
- [ ] Create `.css` with component styles
- [ ] Create `.js` if interactive behavior needed
- [ ] Register in theme `*.libraries.yml`
- [ ] Test in Canvas editor
- [ ] Verify all variations render
- [ ] Add demo content examples
- [ ] Document field mapping

---

## File Structure

```
docroot/themes/custom/y_canvas/
├── components/
│   └── organisms/
│       ├── y-hero/              # (DONE)
│       ├── y-accordion/
│       ├── y-accordion-item/
│       ├── y-cards/
│       ├── y-card-item/
│       ├── y-carousel/
│       ├── y-carousel-item/
│       ├── y-ping-pong/
│       ├── y-promo/
│       ├── y-modal/
│       ├── y-table/
│       ├── y-statistics/
│       ├── y-statistics-item/
│       ├── y-simple-menu/
│       ├── y-grid-cta/
│       ├── y-grid-item/
│       ├── y-icon-grid/
│       ├── y-icon-grid-item/
│       ├── y-testimonials/
│       ├── y-testimonial-item/
│       ├── y-tabs/
│       ├── y-tab-item/
│       ├── y-partners/
│       ├── y-partner-item/
│       ├── y-partners-tier/
│       ├── y-staff-members/
│       ├── y-staff-member-item/
│       ├── y-branch-amenities/
│       ├── y-branch-social/
│       ├── y-branch-hours/
│       ├── y-related-articles/
│       ├── y-related-events/
│       ├── y-webform/
│       ├── y-articles-filter/
│       ├── y-articles-listing/
│       ├── y-featured-articles/
│       ├── y-events-filter/
│       ├── y-events-listing/
│       ├── y-featured-events/
│       ├── y-search-results/
│       ├── y-donate/
│       ├── y-donate-item/
│       ├── y-activity-finder/
│       ├── y-simple-schedule/
│       ├── y-repeat-schedules/
│       ├── y-map/
│       └── ... (legacy + camp)
├── y_canvas.info.yml
└── y_canvas.libraries.yml
```

---

## Dependencies

### External Libraries

| Library | Components | Source |
|---------|------------|--------|
| Swiper.js | carousel | npm/CDN |
| Leaflet/Google Maps | map | npm/CDN |
| React | activity-finder | existing integration |

### Drupal Modules

| Module | Required For |
|--------|--------------|
| canvas | All components |
| sdc | Component discovery |
| webform | y-webform |
| search_api | y-search-results |

---

## Risk Mitigation

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Canvas API changes | Low | High | Pin Canvas version, follow changelog |
| Complex JS integrations | Medium | Medium | Stub with static demos first |
| Field mapping complexity | Medium | Low | Document all mappings |
| Performance with many components | Low | Medium | Lazy load, code split |

---

## Success Metrics

- [ ] All 59 components converted
- [ ] Canvas editor shows all components
- [ ] Demo site functional
- [ ] No console errors
- [ ] Lighthouse performance > 80
- [ ] WCAG 2.1 AA compliant

---

## Related Resources

- [Canvas Module Documentation](https://www.drupal.org/project/canvas)
- [SDC Documentation](https://www.drupal.org/docs/develop/theming-drupal/using-single-directory-components)
- [YMCA Website Services Docs](https://ds-docs.y.org)
- [Conversion Tutorial (PR#5)](https://github.com/podarok/trainings/pull/5)

---

**Created**: December 2024
**Status**: Planning Complete
**Next Step**: Sprint 1 - Foundation Components
