;(function (angular) {
    "use strict";

    angular.module('AgentPlus')
        .directive('title', function($rootScope, $timeout) {
            return {
                link: function () {
                    $rootScope.$on('$stateChangeSuccess', function(event, toState) {
                        $timeout(function() {
                            $rootScope.title = (toState.hasOwnProperty('pageTitle') && toState.pageTitle)
                                ? toState.pageTitle + ' :: AgentPlus'
                                : 'AgentPlus';
                        });
                    });
                }
            };
        });
})(window.angular);