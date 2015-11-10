;(function (angular) {
    "use strict";

    var factoryModule = angular.module('ap.controller.factory', ['ap.auth', 'ap.api.internal', 'ui.router', 'processing']);

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

    function FactorySearchController($scope, $apInternalApi, $apAuth, $location, $anchorScroll, $processing)
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

            if (factory && $processing.isNot(factory)) {
                factory.name = name;
                $processing.start(factory);

                $apInternalApi.factoryUpdate(factory)
                    .then(
                        function () {$processing.end(factory);},
                        function () {$processing.end(factory);}
                    );
            }
        }
    }

    function FactoryCreateController($scope, $apInternalApi, $state, $processing, Notification)
    {
        $scope.factory = {
            name: null
        };

        $scope.create = function ()
        {
            if ($processing.is($scope.factory)) {
                return;
            }

            $processing.start($scope.factory);

            $scope.factory.errors = null;

            $apInternalApi.factoryCreate($scope.factory)
                .then(
                    function () {
                        $processing.end($scope.factory);
                        $state.go('factory.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create factory.'
                                });
                            });

                    },

                    function (response) {
                        $processing.end($scope.factory);
                        if (response.isRequestNotValid()) {
                            $scope.factory.errors = response.errorData;
                        }
                    }
                );
        }
    }
})(window.angular);