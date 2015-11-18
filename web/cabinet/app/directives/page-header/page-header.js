;(function (angular) {
    "use strict";

    angular.module('AgentPlus')
        .directive('pageHeader', function ($apTheme) {
            return {
                templateUrl: $apTheme.resolveDirectiveTemplate('page-header', 'page-header'),
                restrict: 'E',
                replace: true,
                transclude: true
            }
        });
})(window.angular);