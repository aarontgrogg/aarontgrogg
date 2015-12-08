/**
 * This function will bind the click event for entry items in the dashboard
 * that will toggle the entry description row
 */
function wsdplugin_bindEntryClick($)
{
    $('.entry-event').each(function(){
        var self = $(this);
        self.click(function(){
            var e = self.next('tr.entry-description');
            e.fadeToggle('fast','linear');
        });
    });
}
/**
 * Update the querystring param if found otherwise add it
 * @param uri
 * @param key
 * @param value
 * @returns string  The updated uri
 */
function updateQueryStringParam(uri, key, value) {
    var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i"),separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) { return uri.replace(re, '$1' + key + "=" + value + '$2'); }else { return uri + separator + key + "=" + value; }
}
