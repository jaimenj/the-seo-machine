"use strict";

let tsm_interval_id
let tsm_status = 'stopped'


function tsmOnLoadMain() {
    console.log('Loading The SEO Machine view..')

    jQuery('#tsm-datatable tfoot th').each( function () {
        let title = jQuery(this).text()

        jQuery(this).html( '<input type="text" placeholder="Filtrar.." />' )
    })

    let ajaxDatatablesServerProcessingUrl = document.getElementById('tsm-datatable').dataset.ajax_datatables_server_processing_url
    let table = jQuery('#tsm-datatable').DataTable({
        dom: '<"float-left"i><"float-right"f>t<"float-left"l>B<"float-right"p><"clearfix">',
        responsive: true,
        order: [[ 0, "desc" ]],
        buttons: ['csv', 'excel', 'pdf'],
        initComplete: function () {
            this.api().columns().every( function () {
                let that = this

                jQuery( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw()
                        }
                })
            })
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxDatatablesServerProcessingUrl,
            type: 'POST'
        },
        columnDefs: [
            { "name": "id", "targets": 0 },
            { "name": "url", "targets": 1 },
            { "name": "updated_at", "targets": 2 },
            { "name": "level", "targets": 3 },
            { "name": "title", "targets": 4 },
            { "name": "curlinfo_response_code", "targets": 5 },
            { "name": "meta_charset", "targets": 6 },
            { "name": "meta_description", "targets": 7 },
            { "name": "meta_keywords", "targets": 8 },
            { "name": "meta_author", "targets": 9 },
            { "name": "meta_viewport", "targets": 10 },
            { "name": "qty_bases", "targets": 11 },
            { "name": "qty_css_external_files", "targets": 12 },
            { "name": "qty_css_internal_files", "targets": 13 },
            { "name": "qty_javascripts", "targets": 14 },
            { "name": "qty_h1s", "targets": 15 },
            { "name": "qty_h2s", "targets": 16 },
            { "name": "qty_h3s", "targets": 17 },
            { "name": "qty_h4s", "targets": 18 },
            { "name": "qty_h5s", "targets": 19 },
            { "name": "qty_h6s", "targets": 20 },
            { "name": "qty_hgroups", "targets": 21 },
            { "name": "qty_sections", "targets": 22 },
            { "name": "qty_navs", "targets": 23 },
            { "name": "qty_asides", "targets": 24 },
            { "name": "qty_articles", "targets": 25 },
            { "name": "qty_addresses", "targets": 26 },
            { "name": "qty_headers", "targets": 27 },
            { "name": "qty_footers", "targets": 28 },
            { "name": "qty_ps", "targets": 29 },
            { "name": "qty_total_links", "targets": 30 },
            { "name": "qty_internal_links", "targets": 31 },
            { "name": "qty_external_links", "targets": 32 },
            { "name": "qty_targeted_links", "targets": 33 },
            { "name": "content_study", "targets": 34 },
            { "name": "text_to_html_ratio", "targets": 35 },
            { "name": "curlinfo_efective_url", "targets": 36 },
            { "name": "curlinfo_http_code", "targets": 37 },
            { "name": "curlinfo_filetime", "targets": 38 },
            { "name": "curlinfo_total_time", "targets": 39 },
            { "name": "curlinfo_namelookup_time", "targets": 40 },
            { "name": "curlinfo_connect_time", "targets": 41 },
            { "name": "curlinfo_pretransfer_time", "targets": 42 },
            { "name": "curlinfo_starttransfer_time", "targets": 43 },
            { "name": "curlinfo_redirect_count", "targets": 44 },
            { "name": "curlinfo_redirect_time", "targets": 45 },
            { "name": "curlinfo_redirect_url", "targets": 46 },
            { "name": "curlinfo_primary_ip", "targets": 47 },
            { "name": "curlinfo_primary_port", "targets": 48 },
            { "name": "curlinfo_size_download", "targets": 49 },
            { "name": "curlinfo_speed_download", "targets": 50 },
            { "name": "curlinfo_request_size", "targets": 51 },
            { "name": "curlinfo_content_length_download", "targets": 52 },
            { "name": "curlinfo_content_type", "targets": 53 },
            { "name": "curlinfo_http_connectcode", "targets": 54 },
            { "name": "curlinfo_num_connects", "targets": 55 },
            { "name": "curlinfo_appconnect_time", "targets": 56 }
        ]
    })

    console.log('Loaded The SEO Machine view!')
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
