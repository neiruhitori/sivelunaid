(() => {
    const form = document.querySelector('[data-order-history-search]');
    const input = form?.querySelector('input[name="q"]');
    const results = document.querySelector('[data-order-history-results]');
    if (!form || !input || !results) return;

    const endpoint = form.dataset.endpoint || form.action;
    let timer;
    let controller;
    let requestId = 0;

    const loadResults = async (url, updateAddress = true) => {
        controller?.abort();
        controller = new AbortController();
        const currentRequest = ++requestId;
        results.setAttribute('aria-busy', 'true');
        results.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                signal: controller.signal,
            });
            if (!response.ok) throw new Error('Pencarian gagal dimuat.');
            const data = await response.json();
            if (currentRequest !== requestId) return;
            results.innerHTML = data.html;
            if (updateAddress) window.history.replaceState({}, '', url);
        } catch (error) {
            if (error.name !== 'AbortError') form.submit();
        } finally {
            if (currentRequest === requestId) {
                results.setAttribute('aria-busy', 'false');
                results.classList.remove('is-loading');
            }
        }
    };

    const makeUrl = () => {
        const url = new URL(endpoint, window.location.href);
        const query = input.value.trim();
        if (query) url.searchParams.set('q', query);
        else url.searchParams.delete('q');
        url.searchParams.delete('page');
        return url.toString();
    };

    input.addEventListener('input', () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => loadResults(makeUrl()), 300);
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(timer);
        loadResults(makeUrl());
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('.pagination a');
        if (!link) return;
        event.preventDefault();
        loadResults(link.href);
    });

    window.addEventListener('popstate', () => {
        const url = new URL(window.location.href);
        input.value = url.searchParams.get('q') || '';
        loadResults(url.toString(), false);
    });
})();
