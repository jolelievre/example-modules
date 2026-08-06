# Dashboard example (`dashexample`)

Demonstration module for the **migrated (Symfony) Back Office Dashboard** and its new dedicated hook family.

It is both living documentation of the integration contract and a validation vehicle: once installed with the `dashboard` feature flag enabled, it renders content in every zone of the new dashboard page, including four Chart.js charts (doughnut, line, bar, polar area).

## What it demonstrates

- Registering on the **new** dashboard hooks: `displayAdminDashboardZoneOne`, `displayAdminDashboardZoneTwo`, `displayAdminDashboardZoneThree`, `displayAdminDashboardTop`, `displayAdminDashboardBottom`, `displayAdminDashboardToolbar`.
- Rendering hook content through **module Twig templates** (`views/templates/admin/*.html.twig`) — no Smarty, no `HelperForm`, no `Db::getInstance()`.
- Passing and using hook **parameters** (`date_from` / `date_to`, the employee stats date range).
- Rendering charts with the **core-provided Chart.js** and its **PrestaShop palette** (see below) — the module ships no charting library.
- Presenting each block as a **Bootstrap card** (`card` / `card-header` / `card-body`), the supported markup of the new Back Office theme.
- Loading the module's **own CSS/JS assets from its hook output** — no `actionAdminControllerSetMedia`, no `get_class($this->context->controller)` detection. Each hook template loads only what its own blocks need, so every hook keeps working when the others are unregistered.

## Requirements

- PrestaShop **9.2.0** or later (the version that introduces the migrated dashboard, its hooks and the core Chart.js bundle).
- The **`dashboard` feature flag** enabled: *Advanced Parameters > New & Experimental Features > Dashboard*.

## Install

```bash
# from the PrestaShop root
php bin/console prestashop:module install dashexample
```

Then enable the `dashboard` feature flag and open the Dashboard. You should see the module's cards in every zone (four charts across the three columns) and a marker in the toolbar area.

## Charts with the core-provided Chart.js

The dashboard page loads **Chart.js v4** as an independent core bundle (`themes/new-theme/public/chartjs.bundle.js`) — only on that page, not in the main Back Office bundle. It exposes two globals:

- **`Chart`** — the Chart.js entry point ([chartjs.org](https://www.chartjs.org)).
- **`psChart`** — the PrestaShop palette, mirroring the modern PrestaShop branding (the `.b-color-*` blocks of prestashop.com, one named color per `--color-N`), read from the design-kit tokens (`--cdk-*` CSS custom properties) where they exist:
  - `psChart.colors` — named colors, in prestashop.com order: `white`, `black`, `blue`, `green`, `purple`, `yellow`, `lightGray`, `gray`, `paleGray`, `teal`, `lightBlue`, `midGray`, `offWhite`, `borderGray` (`teal` and `gray` are the darker accents to prefer for line strokes);
  - `psChart.series` — the categorical ramp as an ordered array for multi-series charts: `green`, `teal`, `blue`, `yellow`, `gray`, `purple` (the brand pastels interleaved with the darker accents so adjacent series stay distinguishable for color-blind readers);
  - `psChart.withAlpha(color, alpha)` — translucent variant of a color, e.g. for line-chart area fills.

A chart whose datasets define **no color at all** is colored automatically with the PrestaShop palette (the core replaces the default Chart.js `colors` plugin) — see the Zone One doughnut. A chart that defines **any** color is left untouched, so pick every color from `psChart` explicitly when some series need a specific meaning — see the Zone Two line chart (previous period in neutral gray) and bar chart.

Keep legends and tooltips enabled (Chart.js defaults) so series identity never relies on color alone, and give each `<canvas>` a `role="img"` and an `aria-label` — canvases are invisible to screen readers.

## New vs legacy hook families

The migrated dashboard deliberately uses a **new hook family**, distinct from the legacy one. A module knows which architecture it is integrating with purely from **which hook is called** — there is no version detection to do on the module side.

| New (Symfony dashboard) | Legacy (legacy dashboard) | Area |
|---|---|---|
| `displayAdminDashboardZoneOne` | `dashboardZoneOne` | Left column |
| `displayAdminDashboardZoneTwo` | `dashboardZoneTwo` | Center column |
| `displayAdminDashboardZoneThree` | `dashboardZoneThree` | Right column |
| `displayAdminDashboardTop` | `displayDashboardTop` | Top area |
| `displayAdminDashboardToolbar` | `displayDashboardToolbarTopMenu` | Toolbar |

The legacy hooks are untouched and keep working on the legacy page (flag off).

## Supporting both PrestaShop versions in one module

To render on **both** the legacy and the migrated dashboard from a single module, register on **both** hook families and let each callback render with the matching architecture. The core calls only the hook that belongs to the currently displayed page, so the two never collide.

```php
public function install(): bool
{
    return parent::install()
        && $this->registerHook([
            // Migrated (Symfony) dashboard — Twig, no legacy helpers
            'displayAdminDashboardZoneOne',
            'displayAdminDashboardZoneTwo',
            'displayAdminDashboardToolbar',
            // Legacy dashboard — Smarty / HelperForm as before
            'dashboardZoneOne',
            'dashboardZoneTwo',
            'displayDashboardToolbarTopMenu',
        ]);
}

// Called only on the migrated page
public function hookDisplayAdminDashboardZoneOne(array $params): string
{
    return $this->get('twig')->render('@Modules/mymodule/views/templates/admin/zone_one.html.twig', $params);
}

// Called only on the legacy page
public function hookDashboardZoneOne(array $params): string
{
    // legacy Smarty rendering
    $this->smarty->assign($params);

    return $this->display(__FILE__, 'views/templates/hook/zone_one.tpl');
}
```

This module registers **only the new hooks** on purpose, so it also serves as a focused test of the new contract.

## Files

| File | Role |
|---|---|
| `dashexample.php` | Module class: hook registration + hook callbacks rendering Twig |
| `views/templates/admin/zone_one.html.twig` | Zone One card: traffic-sources doughnut (auto-colored) |
| `views/templates/admin/zone_two.html.twig` | Zone Two cards: sales-trend line + monthly-goals bar (explicit palette tokens) |
| `views/templates/admin/zone_three.html.twig` | Zone Three card: orders-by-status polar area (auto-colored) |
| `views/templates/admin/top.html.twig` | Top area banner |
| `views/templates/admin/bottom.html.twig` | Full-width bottom card |
| `views/templates/admin/toolbar.html.twig` | Toolbar marker badge |
| `views/js/zone-one.js`, `views/js/zone-two.js`, `views/js/zone-three.js` | Per-zone chart initialization (each loaded by its own hook template) |
| `views/css/dashexample.css` | Module-owned styles |
