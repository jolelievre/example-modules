/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

// Chart initialization for the Zone Three block only: each hook template loads its own
// script, so the zones keep working when other hooks are unregistered.
// `Chart` is the global provided by the core chartjs.bundle.js on the dashboard page.
(function () {
  function readJson(id) {
    var el = document.getElementById(id);
    return el ? JSON.parse(el.textContent) : null;
  }

  function initPolarArea() {
    var canvas = document.getElementById('dashexample-polar');
    var data = readJson('dashexample-polar-data');
    if (!canvas || !data || typeof Chart === 'undefined') {
      return;
    }

    // No colors set on purpose: the core psColors plugin applies the PrestaShop
    // palette automatically, per data point like a doughnut.
    new Chart(canvas, {
      type: 'polarArea',
      data: {
        labels: data.labels,
        datasets: [{
          data: data.values,
        }],
      },
      options: {
        plugins: { legend: { position: 'bottom' } },
      },
    });
  }

  document.addEventListener('DOMContentLoaded', initPolarArea);
})();
