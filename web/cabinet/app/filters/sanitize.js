;(function (angular) {
    "use strict";

    angular.module("ap.filter.sanitize", ['ngResource'])
        .filter('sanitize', ['$sce', function ($sce) {
            return function (htmlCode) {
                return $sce.trustAsHtml(htmlCode);
            }
        }]);

})(window.angular);