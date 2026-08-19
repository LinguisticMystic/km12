const HIGHLIGHT_MS = 2000;
const SCROLL_SETTLE_MS = 700;

let backChip = null;
let backOriginId = null;
let backOfferToken = 0;
let originObserver = null;
let originWatchArmed = false;
let highlightTimer = null;
let scrollSettleTimer = null;
let scrollSettleListener = null;

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function scrollBehavior() {
    return prefersReducedMotion() ? 'auto' : 'smooth';
}

function highlightClassFor(element) {
    return element.id.startsWith('participant-')
        ? 'participant-highlight'
        : 'schedule-entry-highlight';
}

function highlightScrollTarget(element) {
    if (!(element instanceof HTMLElement)) {
        return;
    }

    if (highlightTimer) {
        window.clearTimeout(highlightTimer);
        highlightTimer = null;
        document.querySelectorAll('.schedule-entry-highlight, .participant-highlight').forEach((highlighted) => {
            highlighted.classList.remove('schedule-entry-highlight', 'participant-highlight');
        });
    }

    const highlightClass = highlightClassFor(element);
    element.classList.add(highlightClass);

    highlightTimer = window.setTimeout(() => {
        element.classList.remove(highlightClass);
        highlightTimer = null;
    }, HIGHLIGHT_MS);
}

function scrollToTarget(id) {
    if (!id) {
        return null;
    }

    const target = document.getElementById(id);

    if (!target) {
        return null;
    }

    target.scrollIntoView({ behavior: scrollBehavior(), block: 'center' });
    highlightScrollTarget(target);

    return target;
}

function isMostlyVisible(element) {
    const rect = element.getBoundingClientRect();
    const height = rect.height || 1;
    const visible = Math.min(rect.bottom, window.innerHeight) - Math.max(rect.top, 0);

    return visible / height >= 0.4;
}

function stopWatchingOrigin() {
    originObserver?.disconnect();
    originObserver = null;
    originWatchArmed = false;
}

function hideBackChip() {
    backOfferToken += 1;
    cancelPendingScroll();
    stopWatchingOrigin();
    backOriginId = null;

    if (backChip) {
        if (document.activeElement === backChip) {
            backChip.blur();
        }

        backChip.hidden = true;
    }
}

function watchOriginVisibility(origin) {
    stopWatchingOrigin();

    originObserver = new IntersectionObserver((entries) => {
        if (!originWatchArmed) {
            return;
        }

        if (entries.some((entry) => entry.isIntersecting)) {
            hideBackChip();
        }
    }, { threshold: 0.4 });

    originObserver.observe(origin);
    originWatchArmed = true;
}

function cancelPendingScroll() {
    window.clearTimeout(scrollSettleTimer);
    scrollSettleTimer = null;

    if (scrollSettleListener) {
        window.removeEventListener('scrollend', scrollSettleListener);
        scrollSettleListener = null;
    }
}

function onceScrolled(callback) {
    cancelPendingScroll();

    let done = false;

    const finish = () => {
        if (done) {
            return;
        }

        done = true;
        cancelPendingScroll();
        callback();
    };

    scrollSettleListener = finish;
    window.addEventListener('scrollend', finish, { once: true });
    scrollSettleTimer = window.setTimeout(finish, SCROLL_SETTLE_MS);
}

function backChipIcon() {
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('stroke-width', '1.5');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('aria-hidden', 'true');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('stroke-linejoin', 'round');
    path.setAttribute('d', 'M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18');
    svg.append(path);

    return svg;
}

function getBackChip() {
    if (backChip) {
        return backChip;
    }

    backChip = document.createElement('button');
    backChip.type = 'button';
    backChip.id = 'schedule-back-chip';
    backChip.className = 'schedule-back-chip';
    backChip.hidden = true;
    backChip.setAttribute('aria-live', 'polite');
    backChip.addEventListener('click', () => {
        const originId = backOriginId;
        hideBackChip();

        if (!originId) {
            return;
        }

        history.replaceState(null, '', `#${originId}`);
        scrollToTarget(originId);
    });

    document.body.append(backChip);

    return backChip;
}

function showBackChip(origin, label) {
    const chip = getBackChip();
    backOriginId = origin.id;

    chip.replaceChildren(backChipIcon(), document.createTextNode(label));
    chip.hidden = false;
    watchOriginVisibility(origin);
}

function offerBackChip(originId, label) {
    const origin = document.getElementById(originId);

    if (!origin || !label) {
        hideBackChip();
        return;
    }

    const token = ++backOfferToken;
    cancelPendingScroll();
    stopWatchingOrigin();

    if (backChip) {
        backChip.hidden = true;
    }

    onceScrolled(() => {
        if (token !== backOfferToken) {
            return;
        }

        if (isMostlyVisible(origin)) {
            return;
        }

        showBackChip(origin, label);
    });
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
    scrollToTarget(targetId);

    const originId = link.getAttribute('data-schedule-origin');
    const backLabel = link.getAttribute('data-schedule-back-label');

    if (originId && backLabel) {
        offerBackChip(originId, backLabel);
        return;
    }

    hideBackChip();
});

window.addEventListener('popstate', () => {
    hideBackChip();

    const hash = window.location.hash.replace(/^#/, '');

    if (hash) {
        scrollToTarget(hash);
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.replace(/^#/, '');

    if (hash.startsWith('schedule-entry-') || hash.startsWith('participant-')) {
        scrollToTarget(hash);
    }
});
