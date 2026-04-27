document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('[data-assignment-search]');
    const assignmentList = document.querySelector('[data-assignment-list]');
    const emptyState = document.querySelector('[data-empty-state]');
    const loadingState = document.querySelector('[data-loading-state]');
    const apiUrl = assignmentList?.dataset.apiUrl;

    if (!searchInput || !assignmentList || !emptyState || !loadingState || !apiUrl) {
        return;
    }

    let debounceTimer = null;
    let activeController = null;

    const escapeHtml = (value) => {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    };

    const renderAssignments = (assignments) => {
        if (!assignments.length) {
            assignmentList.innerHTML = '';
            emptyState.hidden = false;
            return;
        }

        emptyState.hidden = true;
        assignmentList.innerHTML = assignments
            .map((assignment) => `
                <article class="assignment-item">
                    <h3>${escapeHtml(assignment.title)}</h3>
                    <p>${escapeHtml(assignment.description).replace(/\n/g, '<br>')}</p>
                </article>
            `)
            .join('');
    };

    const setLoading = (isLoading) => {
        loadingState.hidden = !isLoading;
    };

    const loadAssignments = async () => {
        if (activeController) {
            activeController.abort();
        }

        activeController = new AbortController();
        const query = searchInput.value.trim();
        const url = new URL(apiUrl, window.location.href);

        if (query !== '') {
            url.searchParams.set('search', query);
        }

        setLoading(true);

        try {
            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'fetch',
                },
                signal: activeController.signal,
            });

            if (!response.ok) {
                throw new Error('Request failed');
            }

            const data = await response.json();
            renderAssignments(Array.isArray(data.assignments) ? data.assignments : []);
        } catch (error) {
            if (error.name !== 'AbortError') {
                emptyState.hidden = false;
                emptyState.textContent = 'Unable to load assignments right now.';
            }
        } finally {
            setLoading(false);
        }
    };

    searchInput.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(loadAssignments, 250);
    });
});
