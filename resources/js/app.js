function highlightScheduleEntry(element) {
    if (!(element instanceof HTMLElement)) {
        return;
    }

    element.classList.add('schedule-entry-highlight');

    window.setTimeout(() => {
        element.classList.remove('schedule-entry-highlight');
    }, 2000);
}

function scrollToScheduleTarget(id) {
    if (!id) {
        return;
    }

    const target = document.getElementById(id);

    if (!target) {
        return;
    }

    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    highlightScheduleEntry(target);
}

document.addEventListener('click', (event) => {
    const link = event.target.closest('a.js-schedule-link');

    if (!link) {
        return;
    }

    const targetId = link.getAttribute('data-schedule-target') || link.getAttribute('href')?.replace(/^#/, '');

    if (!targetId || !document.getElementById(targetId)) {
        return;
    }

    event.preventDefault();
    history.pushState(null, '', `#${targetId}`);
    scrollToScheduleTarget(targetId);
});

window.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace(/^#/, '');

    if (hash.startsWith('schedule-entry-')) {
        scrollToScheduleTarget(hash);
    }
});
