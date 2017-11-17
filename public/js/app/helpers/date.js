define(function () {
    return {
        /**
         * Формирование читаемой даты из timestamp
         * @param {int} unixTime
         * @returns {string}
         */
        convertUnixToRusFormat: function (unixTime) {
            var date = new Date(unixTime * 1000);
            return date.toLocaleString('ru', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    };

});
