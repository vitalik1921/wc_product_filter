jQuery(document).ready(function ($) {
    var wc_pr_attr_selector = '#wc_pf_attributes';
    var products_selector = 'ul.products';
    var pagination_selector = 'nav.woocommerce-pagination';

    var wc_pr_attr = $(wc_pr_attr_selector);
    var products = $(products_selector);
    var pagination = $(pagination_selector);

    //AJAX for term links
    wc_pr_attr.on('click', 'li.term > a', function (e) {
        e.preventDefault();

        var href = $(this).attr('href');

        wc_pr_attr.addClass('wc-pf-loading');
        products.addClass('wc-pf-loading');

        $.ajax({
            type: 'GET',
            url: href,
            success: function (data) {

                wc_pr_attr.removeClass('wc-pf-loading');
                products.removeClass('wc-pf-loading');

                wc_pr_attr.empty();
                wc_pr_attr.html($(data).find(wc_pr_attr_selector).html());

                products.empty();
                products.html($(data).find(products_selector).html());

                pagination.empty();
                pagination.html($(data).find(pagination_selector).html());

                //update browser history (IE doesn't support it)
                if (!navigator.userAgent.match(/msie/i)) {
                    window.history.pushState({"pageTitle": data.pageTitle}, "", href);
                }

                $(document).trigger("wc-pf-attr-ajax-ready");
                $(document).trigger("wc-pf-ajax-ready");
            },
            error: function (xhr, type, exception) {
                alert("WooCommerce product filter ajax error response type " + type);
            }
        });
    });
});