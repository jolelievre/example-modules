/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

// Chart initialization for the Zone Two blocks only: each hook template loads its own
// script, so the zones keep working when other hooks are unregistered.
// `Chart` and `psChart` are the globals provided by the core chartjs.bundle.js on the
// dashboard page. Both charts pick explicit colors from the psChart palette (a chart
// that defines any color is left untouched by the core auto-coloring plugin).
(function () {
  function readJson(id) {
    var el = document.getElementById(id);
    return el ? JSON.parse(el.textContent) : null;
  }

  function initLine() {
    var canvas = document.getElementById('dashexample-line');
    var data = readJson('dashexample-line-data');
    if (!canvas || !data || typeof Chart === 'undefined' || typeof psChart === 'undefined') {
      return;
    }

    new Chart(canvas, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          {
            label: 'Current period',
            data: data.current,
            borderColor: psChart.colors.teal,
            backgroundColor: psChart.withAlpha(psChart.colors.teal, 0.15),
            fill: true,
            tension: 0.3,
          },
          {
            label: 'Previous period',
            data: data.previous,
            borderColor: psChart.colors.gray,
            borderDash: [6, 4],
            fill: false,
            tension: 0.3,
          },
        ],
      },
      options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } },
      },
    });
  }

  function initBar() {
    var canvas = document.getElementById('dashexample-bar');
    var data = readJson('dashexample-bar-data');
    if (!canvas || !data || typeof Chart === 'undefined' || typeof psChart === 'undefined') {
      return;
    }

    new Chart(canvas, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [
          { label: 'Goal (%)', data: data.goal, backgroundColor: psChart.colors.paleGray },
          { label: 'Actual (%)', data: data.actual, backgroundColor: psChart.colors.green },
        ],
      },
      options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } },
      },
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initLine();
    initBar();
  });
})();
