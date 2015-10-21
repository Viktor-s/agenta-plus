;(function (angular) {
    "use strict";

    angular.module('AgentPlus')
        .directive('pageHeader', function () {
            return {
                templateUrl: '/cabinet/app/directives/page-header/page-header.html',
                restrict: 'E',
                replace: true,
                transclude: true
            }
        });
})(window.angular);