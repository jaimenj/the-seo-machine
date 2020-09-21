"use strict";

let tsm_interval_id
let tsm_status = 'stopped'

function tsmOnLoadMain() {
    console.log('Loading The SEO Machine view..')
}

function tsmStudySite() {
    console.log('Starting study site..')
    document.getElementById('tsm-btn-study-site').innerHTML = 'Stop'
    document.getElementById('tsm-btn-study-site').classList.add('tsm-button-studying')

    let quantity_per_batch = document.getElementById('quantity_per_batch').value
    let time_between_batches = document.getElementById('time_between_batches').value
    console.log('Quantity per batch: ' + quantity_per_batch + ', time between batches: ' + time_between_batches)

    tsm_interval_id = setInterval(tsmStudySiteSendAjax, time_between_batches * 1000)
    tsm_status = 'studying'
}

function tsmStudySiteSendAjax() {
    document.getElementById('tsm-box-study-site-status').innerHTML = 'Processing..'

    let xhr = new XMLHttpRequest()

    xhr.onreadystatechange = function (response) {
        if (xhr.readyState === 4) {
            document.getElementById('tsm-box-study-site-status').innerHTML = 'Studying with response: ' + xhr.responseText
            if(xhr.responseText.includes('finished')) {
                tsmStopAll()
            }
        }
    };

    xhr.open('POST', '/wp-admin/admin-ajax.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send('action=tsm_do_batch');
}

function tsmStopAll(){
    clearInterval(tsm_interval_id)
        document.getElementById('tsm-btn-study-site').innerHTML = 'Study Site'
        document.getElementById('tsm-btn-study-site').classList.remove('tsm-button-studying')
        tsm_status = 'stopped'
}

// Starts all JS..
window.addEventListener('load', () => {
    if (typeof weAreInTheSeoMachine !== 'undefined') {
        tsmOnLoadMain()
        document.getElementById('tsm-btn-study-site').addEventListener('click', function () {
            if (tsm_status == 'stopped') {
                tsmStudySite()
            } else {
                tsmStopAll()
            }
        })
    }
})
