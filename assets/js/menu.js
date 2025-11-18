// Sidebar-Script
// Version 1.2 – Final

document.addEventListener("DOMContentLoaded", function () {
    console.log('Sidebar-Script aktiv');

    const toggleButton = document.getElementById("menu-toggle");
    const lockIcon = document.getElementById("lock-icon");
    const sidebar = document.getElementById("sidebar-wrapper");
    const menuGroups = document.querySelectorAll(".menu-group");
    const menuHeaders = document.querySelectorAll(".menu-header");
    let isManuallyToggled = false;

    // 🔒 Alles initial schließen
    menuGroups.forEach(group => group.classList.remove("open"));
    document.querySelectorAll('.submenu').forEach(sub => sub.classList.remove('open'));

    // Sidebar scrollbar sicherstellen
    const sidebarContent = document.querySelector(".sidebar-content");
    const menuContents = document.querySelectorAll(".menu-content");
    const submenuContents = document.querySelectorAll(".submenu-content");

    function ensureScrollability() {
        sidebarContent.style.overflowY = "auto";
        menuContents.forEach(menu => menu.style.overflowY = "auto");
        submenuContents.forEach(submenu => submenu.style.overflowY = "auto");
    }

    ensureScrollability();

    function updateLockIcon() {
        if (isManuallyToggled) {
            lockIcon.src = "/gfx/systemgfx/padlock-close.png";
            lockIcon.classList.remove("unlocked");
            lockIcon.classList.add("locked");
            lockIcon.title = "Fix-Modus aktiviert";
        } else {
            lockIcon.src = "/gfx/systemgfx/padlock.png";
            lockIcon.classList.remove("locked");
            lockIcon.classList.add("unlocked");
            lockIcon.title = "Auto-Modus aktiviert";
        }
    }

    function openSidebar() {
        sidebar.classList.remove("sidebar-closed");
        sidebar.classList.add("open");
        toggleButton.classList.add("open");
        updateLockIcon();
    }

    function closeSidebar() {
        sidebar.classList.add("sidebar-closed");
        sidebar.classList.remove("open");
        toggleButton.classList.remove("open");
        updateLockIcon();
    }

    closeSidebar(); // Standard: geschlossen

    toggleButton.addEventListener("click", () => {
        const isOpen = sidebar.classList.contains("open");
        isManuallyToggled = !isOpen;
        isOpen ? closeSidebar() : openSidebar();
    });

    toggleButton.addEventListener("mouseenter", () => {
        if (!isManuallyToggled && sidebar.classList.contains("sidebar-closed")) {
            openSidebar();
        }
    });

    sidebar.addEventListener("mouseleave", () => {
        if (!isManuallyToggled) {
            setTimeout(() => {
                if (!sidebar.matches(":hover") && !toggleButton.matches(":hover")) {
                    closeSidebar();
                }
            }, 400);
        }
    });

    document.addEventListener("click", function (e) {
        if (!sidebar.contains(e.target) && !toggleButton.contains(e.target)) {
            if (!isManuallyToggled) closeSidebar();
        }
    });

    lockIcon.addEventListener("click", function (e) {
        e.stopPropagation();
        isManuallyToggled = !isManuallyToggled;
        isManuallyToggled ? openSidebar() : closeSidebar();
    });

    menuHeaders.forEach(header => {
        const group = header.closest(".menu-group");

        header.addEventListener("click", () => {
            const isOpen = group.classList.contains("open");

            // Alle schließen
            menuGroups.forEach(g => g.classList.remove("open"));
            document.querySelectorAll('.submenu').forEach(s => s.classList.remove("open"));

            // Nur bei Klick aktivieren
            if (!isOpen) {
                group.classList.add("open");

                // Nur Submenüs öffnen, die aktive Einträge enthalten
                const activeSubItems = group.querySelectorAll(".submenu .list-group-item.active");
                group.querySelectorAll('.submenu').forEach(sub => {
                    if (sub.contains(activeSubItems[0])) {
                        sub.classList.add('open');
                    }
                });
            }
        });

        header.addEventListener("mouseover", () => {
            if (!sidebar.classList.contains("sidebar-closed")) {
                menuGroups.forEach(g => g.classList.remove("open"));
                document.querySelectorAll('.submenu').forEach(s => s.classList.remove("open"));

                group.classList.add("open");

                const activeSubItems = group.querySelectorAll(".submenu .list-group-item.active");
                group.querySelectorAll('.submenu').forEach(sub => {
                    if (sub.contains(activeSubItems[0])) {
                        sub.classList.add('open');
                    }
                });
            }
        });
    });

    document.querySelectorAll(".submenu-header").forEach(subHeader => {
        const parent = subHeader.closest(".submenu");

        subHeader.addEventListener("click", () => {
            const submenuContent = parent.querySelector('.submenu-content');
            const isOpen = parent.classList.contains("open");

            parent.classList.toggle("open");

            if (submenuContent) {
                if (isOpen) {
                    submenuContent.style.maxHeight = '0'; // Schließe Submenü
                    submenuContent.style.opacity = '0';  // Unsichtbar machen
                } else {
                    submenuContent.style.maxHeight = submenuContent.scrollHeight + 'px'; // Öffne Submenü dynamisch
                    submenuContent.style.opacity = '1';  // Sichtbar machen
                }
            }
        });
    });

    // Dynamisch scrollen bei Änderungen
    const observer = new MutationObserver(() => {
        sidebar.scrollTop = sidebar.scrollHeight;
        ensureScrollability();
    });

    observer.observe(sidebar, { childList: true, subtree: true });

    const developerMenu = document.querySelector(".menu-group[data-id='9999']");
    if (developerMenu) {
        const devObserver = new MutationObserver(() => {
            sidebar.scrollTop = sidebar.scrollHeight;
            ensureScrollability();
        });
        devObserver.observe(developerMenu, { childList: true, subtree: true });
    }

    // 🔁 Öffne Menü/Submenü automatisch bei aktivem Link
    const activeItem = document.querySelector(".list-group-item.active");
    if (activeItem) {
        const menuGroup = activeItem.closest(".menu-group");
        if (menuGroup) menuGroup.classList.add("open");
    }

    // Schließe alle Submenüs beim Seitenaufruf
    document.querySelectorAll('.submenu').forEach(sub => {
        sub.classList.remove('open');
        const submenuContent = sub.querySelector('.submenu-content');
        if (submenuContent) {
            submenuContent.style.maxHeight = '0'; // Setze max-height auf 0, um sicherzustellen, dass es geschlossen ist
            submenuContent.style.opacity = '0';  // Setze opacity auf 0, um es unsichtbar zu machen
        }
    });

    console.log('Developer-Menü Einträge im DOM:', document.querySelectorAll('.menu-group:last-child .list-group-item').length);
});
