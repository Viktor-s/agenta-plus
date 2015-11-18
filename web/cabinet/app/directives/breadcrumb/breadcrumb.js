;(function (angular) {
    "use strict";

    angular.module('AgentPlus')
        .directive('breadrcumb', function ($apTheme) {
            return {
                templateUrl: $apTheme.resolveDirectiveTemplate('breadcrumb', 'breadcrumb'),
                restrict: 'E',
                replace: true,
                transclude: true,
                link: function (scope,element,attrs,ctrl, transclude)
                {
                    element.find('.breadcrumb-content').replaceWith(transclude());
                }
            }
        });
})(window.angular);