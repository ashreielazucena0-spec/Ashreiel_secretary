document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");
    const backdrop = document.getElementById("sidebarBackdrop");

    function closeSidebar() {
        sidebar && sidebar.classList.remove("show");
        backdrop && backdrop.classList.remove("show");
    }

    if (toggle) {
        toggle.addEventListener("click", function () {
            sidebar.classList.toggle("show");
            backdrop.classList.toggle("show");
        });
    }

    if (backdrop) {
        backdrop.addEventListener("click", closeSidebar);
    }

    // auto-dismiss flash alerts after a few seconds
    document.querySelectorAll(".alert").forEach(function (el) {
        setTimeout(function () {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }
        }, 4000);
    });

    // Live search: filter the table rows as soon as the user types,
    // instead of waiting for Enter / a page reload.
    document.querySelectorAll(".search-box input[name='q']").forEach(function (input) {
        const form = input.closest("form");
        const card = input.closest(".content-card");
        const table = card ? card.querySelector("table") : null;
        if (!table) return;

        const tbody = table.querySelector("tbody");
        const rows = Array.from(tbody.querySelectorAll("tr"));

        // Row shown when there are no matches.
        const noResultsRow = document.createElement("tr");
        noResultsRow.className = "live-search-empty";
        const td = document.createElement("td");
        td.colSpan = table.querySelectorAll("thead th").length;
        td.className = "text-center text-muted py-4";
        td.textContent = "No matching records found.";
        noResultsRow.appendChild(td);
        noResultsRow.style.display = "none";
        tbody.appendChild(noResultsRow);

        // Filtering already happens live — don't let Enter reload the page.
        if (form) {
            form.addEventListener("submit", function (e) {
                e.preventDefault();
            });
        }

        input.addEventListener("input", function () {
            const needle = input.value.trim().toLowerCase();
            let visible = 0;

            rows.forEach(function (row) {
                const match = needle === "" || row.textContent.toLowerCase().includes(needle);
                row.style.display = match ? "" : "none";
                if (match) visible++;
            });

            noResultsRow.style.display = visible === 0 ? "" : "none";
        });
    });

});