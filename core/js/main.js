/* ==========================================================================
   Project:      Persian Standard Typography
   File:         main.js
   Version:      1.0.0
   Description:  Master JavaScript for the typography showcase.
                 - Theme toggle (light/dark) with localStorage
                 - Auto-generates data-title attributes for responsive tables
                 Load before </body>.
   Author:       Parsa Hafezalkotob
   Last Updated: 2026-05-09
   ========================================================================== */


/* ==========================================================================
   01. Theme Toggle
   --------------------------------------------------------------------------
   Controls a checkbox with id="theme-toggle" to switch between light and
   dark themes. The current choice is saved to localStorage and the
   component automatically follows the OS setting when no manual preference
   exists.
   ========================================================================== */

(function () {
    'use strict';

    const html = document.documentElement;
    const toggleSwitch = document.getElementById('theme-toggle');

    // Exit silently if the toggle is not present on the page
    if (!toggleSwitch) return;

    /**
     * Apply the theme on page load.
     * Priority: localStorage > operating system preference
     */
    function applyInitialTheme() {
        const storedTheme = localStorage.getItem('theme');

        if (storedTheme) {
            html.setAttribute('data-theme', storedTheme);
            toggleSwitch.checked = storedTheme === 'dark';
        } else {
            // No stored preference – follow the OS setting
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            toggleSwitch.checked = prefersDark;
            // Do NOT set data-theme so that the CSS automatic fallback works
        }
    }

    applyInitialTheme();

    // Listen for user clicks on the toggle
    toggleSwitch.addEventListener('change', function () {
        if (this.checked) {
            html.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        } else {
            html.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
    });

    // If the user hasn't chosen a theme yet, keep the toggle in sync with OS changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
        if (!localStorage.getItem('theme')) {
            toggleSwitch.checked = e.matches;
        }
    });
})();


/* ==========================================================================
   02. Responsive Table – data-title Generator
   --------------------------------------------------------------------------
   For every table wrapped in .table-stacked or .table-collapse, this
   script reads the text from the <thead> and adds a data-title attribute
   to each <td> in the <tbody>.  This ensures the correct labels are
   shown on small screens.
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Select all wrappers that need this behaviour
    const wrappers = document.querySelectorAll('.table-stacked, .table-collapse');

    wrappers.forEach(function (wrapper) {
        const table = wrapper.querySelector('table');
        if (!table) return;

        // Read the header texts (trimmed)
        const headers = Array.from(table.querySelectorAll('thead th'))
            .map(function (th) {
                return th.textContent.trim();
            });

        if (headers.length === 0) return;

        // Apply data-title to every cell in the body
        table.querySelectorAll('tbody tr').forEach(function (row) {
            row.querySelectorAll('td').forEach(function (td, index) {
                if (index < headers.length) {
                    td.setAttribute('data-title', headers[index]);
                }
            });
        });
    });
});