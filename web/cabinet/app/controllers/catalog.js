;(function (angular) {
    "use strict";

    var catalogModule = angular.module('ap.controller.catalog', [
        'ap.auth',
        'ap.api.internal',
        'ui.router',
        'processing',
        'angularFileUpload'
    ]);

    catalogModule.config(function ($stateProvider) {
        $stateProvider
            .state('catalog', {
                url: '/catalog',
                templateUrl: '/cabinet/views/catalog/main.html',
                pageTitle: 'Catalogs'
            })
            .state('catalog.search', {
                url: '/search',
                templateUrl: '/cabinet/views/catalog/search.html',
                controller: CatalogSearchController,
                pageTitle: 'Search'
            })
            .state('catalog.create', {
                url: '/create',
                templateUrl: '/cabinet/views/catalog/create.html',
                controller: CatalogCreateController,
                pageTitle: 'Create'
            })
            .state('catalog.edit', {
                url: '/:catalog/edit',
                templateUrl: '/cabinet/views/catalog/edit.html',
                controller: CatalogEditController,
                pageTitle: 'Edit'
            });
    });

    var
        __initializeFileUploader = function ($scope, FileUploader, $interval)
        {
            $scope.uploader = new FileUploader({
                url: document.getElementsByTagName('html')[0].getAttribute('data-cabinet-upload-url'),
                autoUpload: true,
                onCompleteItem: function (item, response, status) {
                    if (status == 200) {
                        $scope.catalog.images.push(response);
                    }
                },
                filters: [
                    {
                        name: 'only-images',
                        fn: function (item) {
                            var
                                mimeParts = item.type.split('/'),
                                primaryMime = mimeParts[0];

                            if (primaryMime == 'image') {
                                return true;
                            }

                            $scope.imageUploadError = 'Allow upload only images!';

                            $interval(function () {
                                $scope.imageUploadError = null;
                            }, 5000);

                            return false;
                        }
                    }
                ]
            });

            $scope.removeUploaderItem = function (item)
            {
                var index = $scope.uploader.getIndexOfItem(item);
                item.remove();
                $scope.catalog.images.splice(index, 1);
            };
        },

        __loadFactories  = function($scope, $apInternalApi)
        {
            $apInternalApi.factories()
                .then(function (factories) {
                    $scope.factories = factories;
                });
        };

    function CatalogSearchController($scope, $apAuth, $location, $apInternalApi)
    {
        $scope.accesses = $apAuth.isAccesses({
            catalogCreate: 'CATALOG_CREATE'
        });

        $scope.accesses.catalogs = {};

        var
            query = {
                page: 1,
                limit: 30
            },

            updateCatalogs = function ()
            {
                $apInternalApi.catalogs(query)
                    .then(function (pagination) {
                        var i, catalog;

                        for (i in pagination.storage) {
                            if (pagination.storage.hasOwnProperty(i)) {
                                catalog = pagination.storage[i];

                                $scope.accesses.catalogs[catalog.id] = {
                                    edit: false
                                };

                                (function (i, catalog) {
                                    $apAuth.isGranted('CATALOG_EDIT', catalog)
                                        .then(function (status) {
                                            $scope.accesses.catalogs[catalog.id].edit = status;
                                        });
                                })(i, catalog);
                            }
                        }

                        $scope.pagination = pagination;
                    });
            };

        if ($location.search().page) {
            query.page = $location.search().page;
        }

        if ($location.search().limit) {
            query.limit = $location.search().limit;
        }

        updateCatalogs();
    }

    function CatalogCreateController($scope, $apInternalApi, $processing, $state, Notification, FileUploader, $interval)
    {
        $scope.catalog = {
            name: null,
            factories: [],
            images: []
        };

        __initializeFileUploader($scope, FileUploader, $interval);
        __loadFactories($scope, $apInternalApi);

        $scope.create = function ()
        {
            if ($processing.is($scope.catalog)) {
                return;
            }

            $scope.catalog.errors = null;

            $processing.start($scope.catalog);

            $apInternalApi.catalogCreate($scope.catalog)
                .then(
                    function () {
                        $processing.end($scope.catalog);

                        $state.go('catalog.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create catalog.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.catalog);

                        if (response.isRequestNotValid()) {
                            $scope.catalog.errors = response.errorData;
                        }
                    }
                );
        };
    }

    function CatalogEditController($scope, $apInternalApi, $processing, $state, $stateParams, Notification, FileUploader, $interval)
    {
        __initializeFileUploader($scope, FileUploader, $interval);

        var
            catalogId = $stateParams.catalog,
            loadCatalog = function ()
            {
                $apInternalApi.catalog(catalogId)
                    .then(function (catalog) {
                        $scope.catalog = catalog;

                        __loadFactories($scope, $apInternalApi);
                    });
            };

        $scope.update = function ()
        {
            if ($processing.is($scope.catalog)) {
                return;
            }

            $scope.catalog.errorData = null;

            $processing.start($scope.catalog);

            $apInternalApi.catalogUpdate($scope.catalog)
                .then(
                    function () {
                        $processing.end($scope.catalog);

                        $state.go('catalog.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully update catalog.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.catalog);

                        if (response.isRequestNotValid()) {
                            $scope.catalog.errors = response.errorData;
                        }
                    }
                )
        };

        loadCatalog();
    }
})(window.angular);