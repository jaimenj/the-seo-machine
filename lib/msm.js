"use strict";

function onLoadMain() {
    console.log('Loading My SEO Machine view..')
}

// Starts all JS..
window.addEventListener('load', () => {
    if (typeof weAreInMySEOMachine !== 'undefined') {
        onLoadMain()
    }
})
