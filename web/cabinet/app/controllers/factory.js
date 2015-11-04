;(function (angular) {
    "use strict";

    var factoryModule = angular.module('ap.controller.factory', ['ap.auth', 'ap.api.internal', 'ui.router']);

    factoryModule.config(function ($stateProvider) {
        $stateProvider
            .state('factory', {
                url: '/factory',
                templateUrl: '/cabinet/views/factory/main.html',
                pageTitle: 'Factories'
            })
            .state('factory.search', {
                url: '/search',
                templateUrl: '/cabinet/views/factory/search.html',
                controller: FactorySearchController,
                pageTitle: 'Search'
            })
            .state('factory.create', {
                url: '/create',
                templateUrl: '/cabinet/views/factory/create.html',
                controller: FactoryCreateController,
                pageTitle: 'Create'
            });
    });

    function FactorySearchController($scope, $apInternalApi, $apAuth, $location, $anchorScroll, $apLoading)
    {
        var
            query = {
                page: $location.search().page ? $location.search().page : 1
            },

            loadFactoriesByQuery = function ()
            {
                $anchorScroll();

                $apInternalApi.factories(query)
                    .then(
                        function (pagination) {$scope.pagination = pagination;},
                        function (response) { /** @todo: control error */}
                    );
            };


        loadFactoriesByQuery();

        $scope.accesses = $apAuth.isAccesses({
            factoryCreate: 'FACTORY_CREATE',
            factoryEdit: 'FACTORY_EDIT'
        });

        $scope.updateName = function (id, name)
        {
            var factory = $scope.pagination.storage.findById(id);

            if (factory && $apLoading.isNotProcessed(factory)) {
                factory.name = name;
                $apLoading.startProcess(factory);

                $apInternalApi.factoryUpdate(factory)
                    .then(
                        function () {$apLoading.endProcess(factory);},
                        function () {$apLoading.endProcess(factory); /** @todo: control error */}
                    );
            }
        }
    }

    function FactoryCreateController($scope, $apInternalApi, $state)
    {
        $scope.factory = {
            name: null
        };

        $scope.create = function ()
        {
            $scope.factory.errors = null;

            $apInternalApi.factoryCreate($scope.factory)
                .then(
                    function () {$state.go('factory.search');},
                    function (response) {
                        if (response.isRequestNotValid()) {
                            $scope.factory.errors = response.errorData;
                        }
                    }
                );
        }
    }
})(window.angular);