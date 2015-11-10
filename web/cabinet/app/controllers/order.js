;(function (angular) {
    "use strict";

    var orderModule = angular.module('ap.controller.order', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ap.api.external',
        'ui.router',
        'processing',
        'angularFileUpload'
    ]);

    orderModule.config(function ($stateProvider) {
        $stateProvider
            .state('order', {
                url: '/order',
                templateUrl: '/cabinet/views/order/main.html',
                pageTitle: 'Orders'
            })
            .state('order.create', {
                url: '/create',
                templateUrl: '/cabinet/views/order/create.html',
                controller: OrderCreateController,
                pageTitle: 'Create'
            })
            .state('order.edit', {
                url: '/:order/edit',
                templateUrl: '/cabinet/views/order/edit.html',
                controller: OrderEditController,
                pageTitle: 'Edit'
            })
    });

    function OrderCreateController($scope, $apInternalApi, $apExternalApi, $processing, $state, FileUploader, Notification)
    {
        $scope.order = {
            client: null,
            factories: [],
            stage: null,
            money: {
                amount: null,
                currency: null
            },
            comment: null,
            attachments: []
        };

        $scope.uploader = new FileUploader({
            url: document.getElementsByTagName('html')[0].getAttribute('data-cabinet-upload-url'),
            autoUpload: true,
            onCompleteItem: function (item, response, status) {
                if (status == 200) {
                    $scope.order.attachments.push(response);
                }
            }
        });

        $scope.removeAttachment = function (item)
        {
            var index = $scope.uploader.getIndexOfItem(item);
            item.remove();
            $scope.order.attachments.splice(index, 1);
        };

        var
            loadFactories = function ()
            {
                $apInternalApi.factories()
                    .then(function (factories) {
                        $scope.factories = factories;
                    });
            },

            loadClients = function ()
            {
                $apInternalApi.clients()
                    .then(function (clients) {
                        $scope.clients = clients;
                    });
            },

            loadCurrencies = function ()
            {
                $apExternalApi.currencies()
                    .then(function (currencies) {
                        $scope.currencies = currencies;
                    });
            },

            loadStages = function ()
            {
                $apInternalApi.stages()
                    .then(function (stages) {
                        $scope.stages = stages;
                    });
            };

        $scope.create = function ()
        {
            if ($processing.is($scope.order)) {
                // Now processing
                return;
            }

            $scope.order.errors = null;

            $processing.start($scope.order);

            $apInternalApi.orderCreate($scope.order)
                .then(
                    function (order) {
                        $processing.end($scope.order);
                        $state.go('diary.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create order.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.order);

                        if (response.isRequestNotValid()) {
                            $scope.order.errors = response.errorData;
                        }
                    }
                );
        };

        loadFactories();
        loadClients();
        loadCurrencies();
        loadStages();
    }

    function OrderEditController($scope, $apInternalApi, $apExternalApi, $processing, $state, $stateParams, FileUploader)
    {
    }
})(window.angular);