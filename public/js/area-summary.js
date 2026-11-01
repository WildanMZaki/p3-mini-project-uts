(function () {
    function el(tag, text, className) {
        const node = document.createElement(tag);
        if (text !== undefined) node.textContent = text;
        if (className) node.className = className;
        return node;
    }

    function render(target, data) {
        target.replaceChildren();

        const box = el('div', undefined, 'alert alert-info');
        box.append(el('strong', `${data.area.name}, ${data.area.regency}`));
        box.append(el('div', `Laporan aktif di area ini: ${data.active_count}`));

        const counts = data.by_status
            .filter((item) => item.total > 0)
            .map((item) => `${item.label}: ${item.total}`)
            .join(' · ');
        if (counts) box.append(el('div', counts));

        if (Array.isArray(data.latest) && data.latest.length > 0) {
            const list = el('ul');
            data.latest.forEach((report) => {
                const item = el('li');
                const link = el('a', report.code);
                link.href = report.url;
                item.append(link, ` — ${report.title} (${report.condition}, ${report.status}, ${report.occurred_at})`);
                list.append(item);
            });
            box.append(list);
        }

        target.append(box);
    }

    async function load(select) {
        const target = document.querySelector(select.dataset.areaSummary);
        if (!target) return;

        if (!select.value) {
            target.replaceChildren();
            return;
        }

        target.textContent = 'Memuat informasi area...';

        try {
            const url = select.dataset.url.replace('__AREA__', encodeURIComponent(select.value));
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            render(target, await response.json());
        } catch (error) {
            target.textContent = 'Gagal memuat informasi area. Coba lagi.';
            console.error(error);
        }
    }

    document.querySelectorAll('[data-area-summary]').forEach((select) => {
        select.addEventListener('change', () => load(select));
        if (select.value) load(select);
    });
})();
