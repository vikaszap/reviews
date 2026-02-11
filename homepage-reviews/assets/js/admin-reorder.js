jQuery(document).ready(function($) {
    var $postList = $('#the-list');

    $postList.sortable({
        items: 'tr',
        handle: '.drag-handle',
        axis: 'y',
        update: function(event, ui) {
            var order = [];
            $postList.find('tr').each(function() {
                var id = $(this).attr('id');
                if (id) {
                    order.push(id.replace('post-', ''));
                }
            });

            $.post(homepage_reviews_reorder.ajax_url, {
                action: 'update_reviews_order',
                order: order,
                nonce: homepage_reviews_reorder.nonce
            }, function(response) {
                if (response.success) {
                    // Success!
                    // Re-apply zebra striping
                    $postList.find('tr').removeClass('alternate');
                    $postList.find('tr:even').addClass('alternate');
                } else {
                    alert('Error saving order.');
                }
            });
        }
    });
});
