;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .directive('header', function ($apTheme) {
            return {
                templateUrl: $apTheme.resolveDirectiveTemplate('header', 'header'),
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);