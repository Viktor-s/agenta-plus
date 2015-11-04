;(function (angular) {
    "use strict";

    angular.module('AgentPlus')
        .directive('title', function($rootScope, $timeout, $state) {
            return {
                link: function () {
                    $rootScope.$on('$stateChangeSuccess', function(event, toState) {
                        $timeout(function() {
                            var titles = ["AgentPlus"],
                                stateName, state;

                            for (stateName in $state.$current.includes) {
                                if ($state.$current.includes.hasOwnProperty(stateName)) {
                                    if (stateName) {
                                        state = $state.get(stateName);
                                        if (state && state.hasOwnProperty('pageTitle') && state.pageTitle) {
                                            titles.push(state.pageTitle);
                                        }
                                    }
                                }
                            }

                            titles.reverse();

                            $rootScope.title = titles.length > 0 ? titles.join(' :: ') : 'AgentPlus';
                        });
                    });
                }
            };
        });
})(window.angular);