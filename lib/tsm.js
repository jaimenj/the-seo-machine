"use strict";

function onLoadMain() {
    console.log('Loading The SEO Machine view..')

}

function studySite() {
    console.log('Starting study site..')
    document.getElementById('tsm-box-study-site-status').innerHTML = 'Processing..';

    let xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function (response) {
        if (xhr.readyState === 4) {
            document.getElementById('tsm-box-study-site-status').innerHTML = 'Finished with response: ' + xhr.responseText;
        }
    };

    xhr.open('POST', '/wp-admin/admin-ajax.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send('action=tsm_do_batch');
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
