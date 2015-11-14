;(function (angular) {
    "use strict";

    angular.module('ap.filter.money', [])
        .filter('money', function () {
            return function (input) {
                if (!input) {
                    return '';
                }

                return '<div class="money">' +
                        '<span class="amount">' + parseFloat(input.amount).toFixed(2) + '</span>' +
                        '<span class="currency">' + input.currency + '</span>' +
                    '</div>';
            }
        });
})(window.angular);