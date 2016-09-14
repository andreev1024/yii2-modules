(function ($) {
    $.fileManager = {};
    $.fileManager.method = {
        init: function(options) {

            var _s = options.selectors,
                trigger = $("#" + _s.id.modalTrigger),
                modal = $('#' + _s.id.modal),
                container = $('#' + _s.id.container),
                dataPrfx = options.dataPrfx;

            if (options.onclickAction === 'showPopover') {
                $.fileManager.method.popoverInit(options);
            } else {
                $.fileManager.method.linksInit(options);
            }

            if (options.displayInModal) {
                trigger.on('click', function (e) {
                    modal.modal('toggle');
                });
            }

            //  click on `dirrectory` element
            container.on('click', '.' + _s.class.directory, function (e) {
                var url = $(this).data('url');
                $.fileManager.method.load(url, options);
            });

            //  click on `pagination` element
            container.on('click', '.pagination li', function (e) {
                var url = $('a', $(this)).attr('href');
                if (url) {
                    $.fileManager.method.load(url, options);
                }
                return false;
            });

            //  click on `display type` btn
            container.on('click', '.' + _s.class.ajax, function (e) {
                var url = $(this).attr('href');
                if (url) {
                    $.fileManager.method.load(url, options);
                }
                return false;
            });

            //  click on popover close btn
            container.on('click', '.' + _s.class.filesContainer + ' .close', function (e) {
                var fileId = $(this).data('fileId');
                $('.' + _s.class.fileContainer + '[data-' + dataPrfx + '-id=' + fileId + ']', container).popover('hide');
            });

            container.on('click', '.' + _s.class.copyUrl, function (e) {
                var url = $(this).data('url');
                if (url) {
                    $.fileManager.method.copyToClipboard(options.messages.copyUrlAction, url);
                }
            });
        },
        load: function (url, options) {
            url = url + '&options=' + $('#' + options.selectors.id.container).data('options');
            $.ajax({
                url: url
            })
            .done(function (data) {
                if (options.displayInModal) {
                    $('.modal-body', '#' + options.selectors.id.modal).html(data);
                } else {
                    $('#' + options.selectors.id.container).html(data);
                }

                if (options.onclickAction === 'showPopover') {
                    $.fileManager.method.popoverInit(options);
                }
            })
            .fail(function (data) {
                console.log("server error!");
            });
        },
        popoverInit: function (options) {
            var _s = options.selectors,
                files = $('.' + _s.class.fileContainer, '#' + _s.id.container);

            files.each(function (index) {
                var data = $(this).data();

                if (typeof data !== 'object' && $.isEmptyObject(data)) {
                    return; // continue
                }

                var dataPrfx = options.dataPrfx,
                    pattern = new RegExp('^' + dataPrfx + '*');
                infoData = {};

                for (var p in data) {
                    if (pattern.test(p)) {
                        infoData[p.replace(dataPrfx, '')] = data[p];
                    }
                }

                if ($.isEmptyObject(infoData)) {
                    return; // continue
                }

                $(this).popover({
                    'content': $.fileManager.method.getPopoverContent(infoData, options),
                    'html': true,
                    'template': $.fileManager.method.getPopoverTemplate(infoData, options)
                });
            })
        },
        linksInit : function (options) {
            var _s = options.selectors;
            $('#' + _s.id.container).on('click', '.' + _s.class.fileContainer, function(e) {
                var url = $(this).data(options.dataPrfx + '-url');
                if(url && window.open) {
                    window.open(url);
                }
            });
        },
        getPopoverContent: function (data, options) {
            var _s = options.selectors,
                html = '<table class="' + _s.class.fileInfo + '">';

            for (var p in data) {
                html += '<tr>';
                html += '<td class="right1padding"><strong><small>' + p + '</small></strong></td>';
                if(p.toLowerCase() === 'url') {
                    html +=
                        '<td>' +
                            '<button type="button" class="btn btn-primary btn-xs ' + _s.class.copyUrl + '"' +
                            ' data-url="' + data[p] + '">' +
                                options.messages.copyUrl +
                            '</button>' +
                        '</td>';
                } else {
                    html += '<td><small>' + data[p] + '</small></td>';
                }

                html += '</tr>';
            }
            html += '</table>';
            return html;
        },
        getPopoverTemplate: function (data, options) {
            var html =
                '<div class="popover" role="tooltip">' +
                '<div class="arrow"></div>' +
                '<div class="popover-header">' +
                '<button type="button" class="close" aria-hidden="true" data-file-id="' + data.Id + '">×</button>' +
                '<h3 class="popover-title"></h3>' +
                '</div>' +
                '<div class="popover-content">' +
                '</div>';

            return html;
        },
        copyToClipboard: function(prompt, text) {
            window.prompt(prompt, text);
        }
    };

})(window.jQuery);