/* Small helpers for the blog. Every page works without them; they only add comfort. */
const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

/* Light / dark: the choice is remembered in the browser (the page applies it before it paints, see partials/head) */
function initTheme() {
    const root = document.documentElement;
    const buttons = $$('[data-theme-toggle]');

    const show = () => {
        const dark = root.dataset.theme === 'dark';
        buttons.forEach((button) => {
            button.setAttribute('aria-pressed', String(dark));
            $('.theme-moon', button)?.classList.toggle('hidden', dark);
            $('.theme-sun', button)?.classList.toggle('hidden', !dark);
        });
    };

    buttons.forEach((button) => button.addEventListener('click', () => {
        root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        try { localStorage.setItem('theme', root.dataset.theme); } catch (e) { /* private window: just for this visit */ }
        show();
    }));
    show();
}

/* The thin bar at the top that fills while you read an article */
function initProgress() {
    const bar = $('#progress-bar');
    const prose = $('[data-prose]');
    if (!bar || !prose) return;

    let waiting = false;
    const update = () => {
        waiting = false;
        const box = prose.getBoundingClientRect();
        const total = box.height - window.innerHeight * 0.5;
        const done = total > 0 ? Math.min(1, Math.max(0, -box.top / total)) : 1;
        bar.style.transform = `scaleX(${done.toFixed(3)})`;
    };
    const onScroll = () => { if (!waiting) { waiting = true; requestAnimationFrame(update); } };

    addEventListener('scroll', onScroll, { passive: true });
    addEventListener('resize', onScroll);
    update();
}

/* "Share" opens the phone's share sheet where there is one; "Copy link" always works */
function initShare() {
    const row = $('[data-share]');
    if (!row) return;

    const { url, title } = row.dataset;
    const status = $('[data-copy-status]', row);
    const say = (text) => { status.textContent = text; setTimeout(() => { status.textContent = ''; }, 2500); };

    const native = $('[data-share-native]', row);
    if (navigator.share) {
        native.hidden = false;
        native.addEventListener('click', () => navigator.share({ title, url }).catch(() => { /* closed without sharing */ }));
    }

    $('[data-copy-link]', row).addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(url);
            say('Link copied!');
        } catch (e) {
            window.prompt('Copy this link:', url);
        }
    });
}

/* The notice after an action ("Article liked") fades away by itself */
function initFlash() {
    $$('[data-autohide]').forEach((note) => {
        setTimeout(() => {
            note.style.transition = 'opacity .5s ease';
            note.style.opacity = '0';
            setTimeout(() => note.remove(), 600);
        }, 4000);
    });
}

/* The account menu closes when you click elsewhere or press Escape */
function initMenus() {
    const menus = $$('details[data-menu]');
    if (!menus.length) return;

    addEventListener('click', (event) => menus.forEach((menu) => { if (!menu.contains(event.target)) menu.open = false; }));
    addEventListener('keydown', (event) => {
        if (event.key === 'Escape') menus.forEach((menu) => { if (menu.open) { menu.open = false; $('summary', menu)?.focus(); } });
    });
}

/* Writing: a live word / character count, and a preview of the picture you picked */
function initForms() {
    $$('[data-count]').forEach((field) => {
        const out = $(field.dataset.count);
        if (!out) return;
        const words = field.hasAttribute('data-count-words');
        const update = () => {
            const text = field.value.trim();
            if (words) {
                const n = text ? text.split(/\s+/).length : 0;
                out.textContent = `${n} ${n === 1 ? 'word' : 'words'} · about ${Math.max(1, Math.ceil(n / 200))} min read`;
            } else {
                out.textContent = String(field.value.length);
            }
        };
        field.addEventListener('input', update);
        update();
    });

    $$('input[type="file"][data-preview]').forEach((input) => {
        const image = $(input.dataset.preview);
        const figure = image?.closest('figure');
        if (!image) return;

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) return;
            image.src = URL.createObjectURL(file);
            figure?.classList.remove('hidden');
        });
    });

    // "Preview": shows the text with its formatting applied (the server turns it into safe HTML)
    const toggle = $('#preview-toggle');
    const box = $('#preview-box');
    const field = $('#content');
    if (toggle && box && field) {
        toggle.hidden = false;
        toggle.addEventListener('click', async () => {
            if (toggle.getAttribute('aria-expanded') === 'true') {
                box.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
                toggle.textContent = 'Preview';
                return;
            }

            try {
                const response = await fetch(toggle.dataset.previewUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({ content: field.value }),
                });
                if (!response.ok) throw new Error(String(response.status));
                const data = await response.json();
                box.innerHTML = data.html || '<p>Nothing to show yet.</p>';
            } catch (error) {
                box.textContent = 'The preview could not be loaded. Try again.';
            }

            box.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            toggle.textContent = 'Hide preview';
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initProgress();
    initShare();
    initFlash();
    initMenus();
    initForms();
});
