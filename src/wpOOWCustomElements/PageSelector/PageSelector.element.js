jQuery(document).ready(function($) {

    group: 'page_selector',
    pageSelectorElementSettings.onDrop = function ($item, container, _super) {
        var $clonedItem = jQuery('<li/>').css({height: 0});
        $item.before($clonedItem);
        $clonedItem.animate({'height': $item.height()});

        $item.animate($clonedItem.position(), function  () {
            $clonedItem.detach();
            _super($item, container);



            var selectedItems = container.group.containers[1].serialize().children()
            var updated_val = [];

            for (var i = 0; i < selectedItems.length; i++) {
                updated_val.push(selectedItems[i].id);
            }
            //TODO _page_selector_input to php
            $("#{{id}}_page_selector_input").val(JSON.stringify(updated_val))
        });





    }

    $("ol.{{id}}").sortable(pageSelectorElementSettings);
    $("ol.{{id}}").sortable("{{enabled}}");
});
