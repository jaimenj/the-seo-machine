"use strict";

function onLoadMain() {
    console.log('Loading The SEO Machine view..')

}

function studySite() {
    console.log('Starting study site..')

}

// Starts all JS..
window.addEventListener('load', () => {
    if (typeof weAreInTheSeoMachine !== 'undefined') {
        onLoadMain()
        document.getElementById('tsm-btn-study-site').addEventListener('click', function () {
            studySite()
        })
    }
})
