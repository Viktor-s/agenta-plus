;(function (angular) {
    "use strict";

    var typesModule = angular.module('ap.controller.diary_type', [
        'ap.auth',
        'ap.api.internal',
        'ap.theme',
        'ui.router',
        'processing'
    ]);

    typesModule.config(function ($stateProvider, $apThemeProvider) {
        $stateProvider
            .state('diary-type', {
                url: '/diary-type',
                templateUrl: $apThemeProvider.layoutUrl,
                pageTitle: 'Types'
            })
            .state('diary-type.search', {
                url: '/search',
                templateUrl: '/cabinet/views/diary/type/search.html',
                controller: TypeSearchController,
                pageTitle: 'Search'
            })
            .state('diary-type.create', {
                url: '/create',
                templateUrl: '/cabinet/views/diary/type/create.html',
                controller: TypeCreateController,
                pageTitle: 'Create'
            })
            .state('diary-type.edit', {
                url: '/:type/edit',
                templateUrl: '/cabinet/views/diary/type/edit.html',
                controller: TypeEditController,
                pageTitle: 'Edit'
            })
    });

    function TypeSearchController($scope, $apInternalApi, $apAuth)
    {
        $scope.accesses = $apAuth.isAccesses({
            diaryTypeCreate: 'DIARY_TYPE_CREATE'
        });

        var
            loadTypes = function ()
            {
                $apInternalApi.diaryTypes(true)
                    .then(function (types) {
                        $scope.types = types;
                    });
            };

        $scope.levels = function (type)
        {
            return new Array(type.level);
        };

        loadTypes();
    }

    function TypeCreateController($scope, $apInternalApi, $processing, $state, Notification)
    {
        $scope.type = {
            parent: null,
            name: null
        };

        var
            loadTypes = function ()
            {
                $apInternalApi.diaryTypes(true)
                    .then(function (types) {
                        $scope.types = types;
                    });
            };

        $scope.levels = function (type)
        {
            return new Array(type.level);
        };

        $scope.create = function ()
        {
            if ($processing.is($scope.type)) {
                return;
            }

            $scope.type.errors = null;

            $processing.start($scope.type);

            $apInternalApi.diaryTypeCreate($scope.type)
                .then(
                    function () {
                        $processing.end($scope.type);

                        $state.go('diary-type.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create diary type.'
                                });
                            });
                    },

                    function (r) {
                        $processing.end($scope.type);

                        if (r.isRequestNotValid()) {
                            $scope.type.errors = r.errorData;
                        }
                    }
                )
        };

        loadTypes();
    }

    function TypeEditController($scope, $apInternalApi, $processing, $state, $stateParams, Notification)
    {
        var
            typeId = $stateParams.type,

            loadTypes = function ()
            {
                $apInternalApi.diaryTypes(true)
                    .then(function (types) {

                        var parentId = $scope.type.parentId;

                        if (parentId) {
                            $scope.type.parent = types.findById(parentId);
                            types.removeById($scope.type.id);
                        }

                        $scope.types = types;
                    });
            },

            loadType = function ()
            {
                $apInternalApi.diaryType(typeId)
                    .then(function (type) {
                        $scope.type = type;

                        loadTypes();
                    });
            };

        $scope.levels = function (type)
        {
            return new Array(type.level);
        };

        $scope.update = function ()
        {
            if ($processing.is($scope.type)) {
                return;
            }

            $scope.errorData = null;

            $processing.start($scope.type);

            $apInternalApi.diaryTypeUpdate($scope.type)
                .then(
                    function (type) {
                        $processing.end($scope.type);

                        $state.go('diary-type.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully update diary type.'
                                });
                            });
                    },

                    function (r) {
                        $processing.end($scope.type);

                        if (r.isRequestNotValid()) {
                            $scope.type.errors = r.errorData;
                        }
                    }
                )
        };

        loadType();
    }
})(window.angular);