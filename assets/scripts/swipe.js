document.addEventListener('DOMContentLoaded', function() {
    let startX = null;
    const threshold = 50; // Mindestabstand für Swipe

    function handleTouchStart(e) {
        startX = e.touches[0].clientX;
    }

    function handleTouchEnd(e) {
        if (startX === null) return;
        let endX = e.changedTouches[0].clientX;
        let diffX = startX - endX;

        if (diffX > threshold) {
            // Swipe nach links: Next
            const nextBtn = document.querySelector('.btn-next');
            if (nextBtn) nextBtn.click();
        } else if (diffX < -threshold) {
            // Swipe nach rechts: Before
            const beforeBtn = document.querySelector('.btn-before');
            if (beforeBtn) beforeBtn.click();
        }
        startX = null;
    }

    const pictureDiv = document.querySelector('.picture');
    if (pictureDiv) {
        pictureDiv.addEventListener('touchstart', handleTouchStart, false);
        pictureDiv.addEventListener('touchend', handleTouchEnd, false);
    }
});