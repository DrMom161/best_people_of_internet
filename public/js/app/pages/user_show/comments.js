define(function (require) {
    var date = require('app/helpers/date');
    var loader = require('app/helpers/loader');
    var $container = $('#comments-list');
    //работа с комментариями
    var main = {
        /**
         * Точка входа
         */
        init: function () {
            this.update();
            this.subscribe();
        },
        /**
         * Обновление списка комментариев
         */
        update: function () {
            loader.show($container);
            $.get('/comments', {userId: $('#userId').val()}, function (response) {
                var listHtml = ['<div class="col-sm-12 form-group">Пока нет ни одного комментария</div>'];
                if (response.list && response.list.length) {
                    listHtml = [];
                    $.each(response.list, function () {
                        listHtml.push(
                            '<div class="col-sm-12 form-group">'
                            + '<div>'
                            + this.comment
                            + '</div>'
                            + '<h4><small>'
                            + (this.gender == 0 ? 'Написал' : 'Написала')
                            + ' <a href="/users/show/' + this.authorId + '">' + this.author + '</a> '
                            + date.convertUnixToRusFormat(this.date)
                            + '</small></div>'
                            + '</div>'
                        )
                    });
                }
                $container.html(listHtml.join(''));
            })
        },
        /**
         * Подписка на события страницы
         */
        subscribe: function () {
            //отправка комментария
            $('#commentsForm').submit(function (e) {
                e.preventDefault();
                var $form = $(this);
                $form.attr('disabled', 'disabled');
                $.post('/comments/save', $form.serialize(), function () {
                    $form.trigger('reset');
                    main.update();
                }).fail(function () {
                    location.reload();
                }).always(function () {
                    $form.attr('disabled', false);
                });
            })
        }
    };

    return main;
});
