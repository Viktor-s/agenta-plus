;(function (angular) {
    "use strict";

    var orderModule = angular.module('ap.controller.order', [
        'ap.auth',
        'ap.api.internal',
        'ap.api.external',
        'ap.api.external',
        'ui.router',
        'ap.loading',
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
            });
    });

    function OrderCreateController($scope, $apInternalApi, $apExternalApi, $apLoading, $state, FileUploader)
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
            if ($apLoading.isProcessed($scope.order)) {
                // Now processing
                return;
            }

            $scope.order.errors = null;

            $apLoading.startProcess($scope.order);

            $apInternalApi.orderCreate($scope.order)
                .then(
                    function (order) {
                        $apLoading.endProcess($scope.order);
                        $state.go('diary.search');
                    },

                    function (response) {
                        $apLoading.endProcess($scope.order);

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
})(window.angular);