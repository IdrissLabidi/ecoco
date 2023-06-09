const $ = require('jquery');

$(function() {
    var timers = {};

    $('input[id^="quantity-"]').on('change', function() {
        var id = $(this).attr('id').split('-')[1];
        var quantity = $(this).val();
        var url = $('#update-cart-' + id).attr('action');

        clearTimeout(timers[id]);

        timers[id] = setTimeout(function() {
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'quantity=' + quantity
            })
            .then(response => response.json())
            .then(json => {
                $('#product-total' + id).text((json.productTotal / 100) + " €");
                $('#cart-total').text("Total : "+(json.Total / 100)  + " €");
            })
        }, 250);
    });
});