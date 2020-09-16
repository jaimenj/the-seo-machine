"use strict";

function onLoadMain() {
    console.log('Loading My SEO Machine view..')

}

function studySite() {
    console.log('Starting study site..')

}

// Starts all JS..
window.addEventListener('load', () => {
    if (typeof weAreInMySEOMachine !== 'undefined') {
        onLoadMain()
        document.getElementById('msm-btn-study-site').addEventListener('click', function () {
            studySite()
        })
    }
})
