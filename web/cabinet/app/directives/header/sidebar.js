;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .directive('headerSidebar', function ($apTheme) {
            return {
                templateUrl: $apTheme.resolveDirectiveTemplate('header', 'sidebar'),
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
