;(function (angular){
    "use strict";

    angular.module('AgentPlus')
        .controller('SidebarController', ['$scope', '$apAuth', '$state', function ($scope, $apAuth, $state) {
            $scope.access = $apAuth.isAccesses({
                factoryList: 'FACTORY_LIST',
                clientList: 'CLIENT_LIST',
                diaryList: 'DIARY_LIST',
                diaryTypeList: 'DIARY_TYPE_LIST',
                orderList: 'ORDER_LIST',
                stageList: 'STAGE_LIST',
                catalogList: 'CATALOG_LIST',
                gotCatalogList: 'GOT_CATALOG_LIST'
            });

            $scope.current = $state.current.name.split('.')[0];
        }])
        .directive('sidebar', function ($apTheme) {
            return {
                templateUrl: $apTheme.resolveDirectiveTemplate('sidebar', 'sidebar'),
                restrict: 'E',
                replace: true
            }
        });
})(window.angular);
