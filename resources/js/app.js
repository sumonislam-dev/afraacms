import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Menu builder drag-and-drop (admin.menus.edit).
 *
 * No-ops on every other page since it just checks for #menu-tree-root.
 * Every list (top-level and nested, all present in the server-rendered
 * page) becomes a shared Sortable group, so items can be dragged between
 * levels to nest/un-nest them, not just reordered within one list.
 */
function initMenuBuilder() {
    const root = document.getElementById('menu-tree-root');

    if (! root) {
        return;
    }

    const reorderUrl = root.dataset.reorderUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function collectTree(list) {
        return Array.from(list.children).map((item) => {
            const nestedList = item.querySelector(':scope > div > ul[data-menu-list]');

            return {
                id: parseInt(item.dataset.id, 10),
                children: nestedList ? collectTree(nestedList) : [],
            };
        });
    }

    function saveOrder() {
        const topList = root.querySelector(':scope > ul[data-menu-list]');

        fetch(reorderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ tree: collectTree(topList) }),
        });
    }

    root.querySelectorAll('[data-menu-list]').forEach((list) => {
        new Sortable(list, {
            group: 'menu-items',
            animation: 150,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            handle: '[data-drag-handle]',
            onEnd: saveOrder,
        });
    });
}

document.addEventListener('DOMContentLoaded', initMenuBuilder);
