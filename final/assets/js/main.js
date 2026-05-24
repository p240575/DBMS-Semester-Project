document.addEventListener('DOMContentLoaded', () => {
    // Scroll progress bar
    window.addEventListener('scroll', () => {
        const scrollPx = document.documentElement.scrollTop;
        const winHeightPx = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = `${(scrollPx / winHeightPx) * 100}%`;
        const scrollBar = document.getElementById("scroll-bar");
        if(scrollBar) scrollBar.style.width = scrolled;
    });
});
