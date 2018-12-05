
function passPageData(formid) {
    console.log(formid);
    var data = {
            'action': 'set_page_status',
            'page_id': formid
        };

        jQuery.post(ajaxurl, data, function(response) {
            console.log(response);
        });
}