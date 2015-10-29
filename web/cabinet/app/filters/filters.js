;(function (angular) {
    "use strict";

    angular.module('ap.filters', [
        'ap.filter.money',
        'ap.filter.sanitize'
    ]);
})(window.angular);