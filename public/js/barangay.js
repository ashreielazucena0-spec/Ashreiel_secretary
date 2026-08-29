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

});
