$(document).on('ajaxComplete ready', function () {

    var blockss = $('[data-provides="anomaly.field_type.blocks"]:not([data-initialized])');

    blockss.each(function () {

        $(this).attr('data-initialized', '');

        var wrapper = $(this);
        var field = wrapper.data('field_name');
        var modal = $('#' + field + '-modal');
        var items = $(this).find('.blocks-item');
        var instance = $(this).data('instance');
        var add = wrapper.find('.add-row[data-instance="' + instance + '"]');
        var cookie = 'blocks:' + $(this).closest('.blocks-container').data('field_name');

        var collapsed = Cookies.getJSON(cookie);

        items.each(function () {

            var item = $(this);
            var toggle = $(this).find('[data-toggle="collapse"]');
            var text = toggle.find('span');

            /**
             * Hide initial items.
             */
            if (typeof collapsed == 'undefined') {
                collapsed = {};
            }

            if (collapsed[items.index(item)] == true) {
                item
                    .toggleClass('collapsed')
                    .find('[data-toggle="collapse"] i')
                    .toggleClass('fa-compress')
                    .toggleClass('fa-expand');

                if (toggle.find('i').hasClass('fa-compress')) {
                    text.text(toggle.data('collapse'));
                } else {
                    text.text(toggle.data('expand'));
                }
            }
        });

        wrapper.on('click', '[data-toggle="collapse"]', function () {

            var toggle = $(this);
            var item = toggle.closest('.blocks-item');
            var text = toggle.find('span');

            item
                .toggleClass('collapsed')
                .find('[data-toggle="collapse"] i')
                .toggleClass('fa-compress')
                .toggleClass('fa-expand');

            if (toggle.find('i').hasClass('fa-compress')) {
                text.text(toggle.data('collapse'));
            } else {
                text.text(toggle.data('expand'));
            }

            toggle
                .closest('.dropdown')
                .find('.dropdown-toggle')
                .trigger('click');

            if (typeof collapsed == 'undefined') {
                collapsed = {};
            }

            collapsed[items.index(item)] = item.hasClass('collapsed');

            Cookies.set(cookie, JSON.stringify(collapsed), {path: window.location.pathname});

            return false;
        });

        wrapper.indexCollapsed = function () {

            wrapper.find('.blocks-list').find('.blocks-item').each(function (index) {

                var item = $(this);

                if (typeof collapsed == 'undefined') {
                    collapsed = {};
                }

                collapsed[index] = item.hasClass('collapsed');

                Cookies.set(cookie, JSON.stringify(collapsed), {path: window.location.pathname});
            });
        };

        wrapper.sort = function () {
            wrapper.find('.blocks-list').sortable({
                handle: '.blocks-handle',
                placeholder: '<div class="placeholder"></div>',
                containerSelector: '.blocks-list',
                itemSelector: '.blocks-item',
                nested: false,
                onDragStart: function ($item, container, _super, event) {

                    $item.css({
                        height: $item.outerHeight(),
                        width: $item.outerWidth()
                    });

                    $item.addClass('dragged');

                    adjustment = {
                        left: container.rootGroup.pointer.left - $item.offset().left,
                        top: container.rootGroup.pointer.top - $item.offset().top
                    };

                    _super($item, container);
                },
                onDrag: function ($item, position) {
                    $item.css({
                        left: position.left - adjustment.left,
                        top: position.top - adjustment.top
                    });
                },
                afterMove: function ($placeholder) {

                    $placeholder.closest('.blocks-list').find('.dragged').detach().insertBefore($placeholder);

                    wrapper.indexCollapsed();
                },
                serialize: function ($parent, $children, parentIsContainer) {

                    var result = $.extend({}, $parent.data());

                    if (parentIsContainer)
                        return [$children];
                    else if ($children[0]) {
                        result.children = $children[0]; // This needs to return [0] for some reason..
                    }

                    delete result.subContainers;
                    delete result.sortable;

                    return result
                }
            });
        };

        wrapper.sort();

        modal.on('click', '.add-row', function (e) {

            e.preventDefault();

            var count = wrapper.find('.blocks-item').length + 1;

            modal.trigger('loading');

            $(wrapper)
                .find('> .blocks-list')
                .first()
                .append($('<div class="blocks-item"><div class="blocks-loading">' + modal.data('loading') + '...</div></div>').load($(this).attr('href') + '&instance=' + count, function () {
                    wrapper.sort();
                    wrapper.indexCollapsed();
                    modal.modal('hide');
                }));
        });
    });
});
