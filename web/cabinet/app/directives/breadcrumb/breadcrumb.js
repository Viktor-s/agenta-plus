;(function (angular) {
    "use strict";

    angular.module('AgentPlus')
        .directive('breadrcumb', function () {
            return {
                templateUrl: '/cabinet/app/directives/breadcrumb/breadcrumb.html',
                restrict: 'E',
                replace: true,
                transclude: true
            }
        });
})(window.angular);