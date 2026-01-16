/**
 * Mobile Menu functionality with submenu support
 */
(function() {
    'use strict';

    function initMobileMenu() {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const menuClose = document.querySelector('.mobile-menu-close');
        const menuOverlay = document.querySelector('.mobile-menu-overlay');
        const menuSidebar = document.querySelector('.mobile-menu-sidebar');
        const menuTabs = document.querySelector('.mobile-menu-tabs');
        const body = document.body;

        if (!menuToggle || !menuClose || !menuOverlay || !menuSidebar) {
            return;
        }

        // Open menu
        function openMenu() {
            menuSidebar.classList.add('active');
            menuOverlay.classList.add('active');
            if (menuTabs) {
                menuTabs.classList.add('active');
            }
            body.classList.add('mobile-menu-open');
        }

        // Close menu
        function closeMenu() {
            menuSidebar.classList.remove('active');
            menuOverlay.classList.remove('active');
            if (menuTabs) {
                menuTabs.classList.remove('active');
            }
            body.classList.remove('mobile-menu-open');

            // Close all submenus when closing main menu
            const openSubmenus = document.querySelectorAll('.submenu.active');
            openSubmenus.forEach(submenu => {
                submenu.classList.remove('active');
                submenu.style.maxHeight = '0';
            });

            const activeParents = document.querySelectorAll('.has-submenu.submenu-open');
            activeParents.forEach(parent => {
                parent.classList.remove('submenu-open');
            });
        }

        // Event listeners
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            openMenu();
        });

        menuClose.addEventListener('click', function(e) {
            e.preventDefault();
            closeMenu();
        });

        menuOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            closeMenu();
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && menuSidebar.classList.contains('active')) {
                closeMenu();
            }
        });

        // Prevent scroll propagation in menu
        menuSidebar.addEventListener('touchmove', function(e) {
            e.stopPropagation();
        });

        // Handle submenu toggles
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const parentLi = this.closest('.has-submenu');
                const submenu = parentLi.querySelector('.submenu');
                const isOpen = parentLi.classList.contains('submenu-open');

                // Close all other submenus at the same level
                const siblings = parentLi.parentElement.querySelectorAll('.has-submenu');
                siblings.forEach(sibling => {
                    if (sibling !== parentLi) {
                        sibling.classList.remove('submenu-open');
                        const siblingSubmenu = sibling.querySelector('.submenu');
                        if (siblingSubmenu) {
                            siblingSubmenu.classList.remove('active');
                            siblingSubmenu.style.maxHeight = '0';
                        }
                    }
                });

                // Toggle current submenu
                if (isOpen) {
                    parentLi.classList.remove('submenu-open');
                    submenu.classList.remove('active');
                    submenu.style.maxHeight = '0';
                } else {
                    parentLi.classList.add('submenu-open');
                    submenu.classList.add('active');
                    submenu.style.maxHeight = submenu.scrollHeight + 'px';
                }
            });
        });

        // Handle main category link clicks
        const categoryLinks = document.querySelectorAll('.has-submenu > .menu-item-link');
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const parentLi = this.closest('.has-submenu');
                const submenu = parentLi.querySelector('.submenu');

                // If submenu exists and not already open, open it instead of following link
                if (submenu && !parentLi.classList.contains('submenu-open')) {
                    e.preventDefault();
                    const toggle = parentLi.querySelector('.submenu-toggle');
                    if (toggle) {
                        toggle.click();
                    }
                }
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileMenu);
    } else {
        initMobileMenu();
    }

})();
