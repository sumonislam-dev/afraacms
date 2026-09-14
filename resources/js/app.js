import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';

window.Alpine = Alpine;
window.Cropper = Cropper;

Alpine.start();

/**
 * Rich text editors (e.g. News post Content) - Quill mounted over a hidden
 * textarea so the form still submits plain HTML via a normal POST, no JS
 * required server-side. No-ops if no [data-rich-text-editor] is present.
 */
function initRichTextEditors() {
    document.querySelectorAll('[data-rich-text-editor]').forEach((wrapper) => {
        const input = wrapper.querySelector('[data-rich-text-input]');
        const editorEl = wrapper.querySelector('[id$="-editor"]');
        const toolbarEl = wrapper.querySelector('[id$="-toolbar"]');
        const uploadUrl = wrapper.dataset.uploadUrl;

        // Toolbar markup must exist before Quill is constructed - the
        // toolbar module scans its container for controls once, at init.
        toolbarEl.innerHTML = `
            <span class="ql-formats">
                <select class="ql-header">
                    <option value="2">Heading</option>
                    <option value="3">Subheading</option>
                    <option selected>Normal</option>
                </select>
            </span>
            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <button class="ql-blockquote"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-link"></button>
                <button class="ql-image"></button>
                <button class="ql-clean"></button>
            </span>
        `;

        const quill = new Quill(editorEl, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: toolbarEl,
                    handlers: {
                        image: () => uploadImage(quill, uploadUrl),
                    },
                },
            },
        });

        quill.root.innerHTML = input.value;

        quill.on('text-change', () => {
            input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
        });
    });
}

/**
 * Uploads the chosen file through the real Media Library endpoint (same one
 * the cover-image picker uses) and embeds the resulting URL - not a base64
 * data URI, which would bloat the content column and bypass Media Library
 * management entirely.
 */
function uploadImage(quill, uploadUrl) {
    const range = quill.getSelection(true);
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';

    input.onchange = async () => {
        const file = input.files[0];

        if (! file) {
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const response = await fetch(uploadUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        });

        if (! response.ok) {
            alert('Image upload failed. Please try a smaller image (max 5MB) in a standard format (JPG, PNG, GIF, WebP).');

            return;
        }

        const { item } = await response.json();

        quill.insertEmbed(range.index, 'image', item.file_url, 'user');
        quill.setSelection(range.index + 1);
    };

    input.click();
}

document.addEventListener('DOMContentLoaded', initRichTextEditors);

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

/**
 * Generic flat-list drag-and-drop reorder, shared by every admin screen
 * that just persists a single top-to-bottom id order (sections, galleries,
 * gallery items) - unlike the menu builder above, none of these nest.
 *
 * No-ops if #<rootId> isn't present on the page.
 */
function initFlatSortable(rootId, listSelector) {
    const root = document.getElementById(rootId);

    if (! root) {
        return;
    }

    const reorderUrl = root.dataset.reorderUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const list = root.querySelector(listSelector);

    new Sortable(list, {
        animation: 150,
        handle: '[data-drag-handle]',
        onEnd() {
            const order = Array.from(list.children).map((item) => parseInt(item.dataset.id, 10));

            fetch(reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ order }),
            });
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initFlatSortable('section-list-root', '[data-section-list]');
    initFlatSortable('gallery-list-root', '[data-sortable-list]');
    initFlatSortable('gallery-items-root', '[data-sortable-list]');
});

/**
 * AJAX pagination + search for [data-content-list] sections
 * (frontend/sections/content_list.blade.php).
 *
 * Clicking Prev/Next or submitting the section's own search box normally
 * reloads the whole page just to update one section. Instead we fetch the
 * target URL, pull the matching #content-list-{id} block out of the
 * response, and swap only that - the rest of the page (and any other
 * content_list sections on it) stays put. Delegated on document so it keeps
 * working after the container's own innerHTML is replaced.
 */
async function swapContentList(container, url) {
    container.classList.add('opacity-50', 'pointer-events-none', 'transition-opacity');

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (! response.ok) {
            throw new Error(`Unexpected status ${response.status}`);
        }

        const html = await response.text();
        const newContainer = new DOMParser()
            .parseFromString(html, 'text/html')
            .getElementById(container.id);

        if (! newContainer) {
            throw new Error(`#${container.id} missing from response`);
        }

        container.innerHTML = newContainer.innerHTML;
        history.pushState({}, '', url);
    } catch (error) {
        window.location.href = url;
    } finally {
        container.classList.remove('opacity-50', 'pointer-events-none', 'transition-opacity');
    }
}

function initAjaxContentListPagination() {
    if (! document.querySelector('[data-content-list]')) {
        return;
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('[data-content-list] nav a[href]');

        if (! link) {
            return;
        }

        event.preventDefault();
        swapContentList(link.closest('[data-content-list]'), link.href);
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-content-list] form[data-content-search]');

        if (! form) {
            return;
        }

        event.preventDefault();

        const params = new URLSearchParams(new FormData(form)).toString();
        const url = form.getAttribute('action') + (params ? `?${params}` : '');

        swapContentList(form.closest('[data-content-list]'), url);
    });

    window.addEventListener('popstate', () => window.location.reload());
}

document.addEventListener('DOMContentLoaded', initAjaxContentListPagination);
