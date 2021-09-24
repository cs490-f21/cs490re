/**
 * Invoke paginator inside an element without refreshing the whole page.
 * @param {*} id Paginator container element id
 * @param {*} kind Paginator function id
 * @param {*} pageNum Target page number
 * @param {*} val User defined value
 */
 function invoke_paging(id, kind, pageNum, val = "") {
    var element = document.getElementById(id);
    var url = new URL('./api/paging.php', location.href);
    var params = {type: kind, page: pageNum, elem: id, value: val};
    url.search = new URLSearchParams(params).toString();

    fetch(url)
    .then((resp) => {
        if (resp.status !== 200) {
            console.log('Status Code: ' + resp.status);
        }

        resp.text().then(function(data) {
            element.innerHTML = data;
        })
    })
    .catch(function(err) {
        console.log('Fetch Error :-S', err);
    });
}
