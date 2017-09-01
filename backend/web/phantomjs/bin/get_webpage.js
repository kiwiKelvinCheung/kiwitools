$function() {
    var webPage = require('webpage');
    var page = webPage.create();

    page.open('http://test.qooza.hk/ypa_ui/index.html', function (status) {
        console.log(page.content);
        phantom.exit();
    });
}
