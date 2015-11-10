;(function (angular) {
    "use strict";

    var stageModule = angular.module('ap.controller.stage', ['ap.auth', 'ap.api.internal', 'ui.router', 'processing']);

    stageModule.config(function ($stateProvider) {
        $stateProvider
            .state('stage', {
                url: '/stage',
                templateUrl: '/cabinet/views/stage/main.html',
                pageTitle: 'Stages'
            })
            .state('stage.search', {
                url: '/search',
                templateUrl: '/cabinet/views/stage/search.html',
                controller: StageSearchController,
                pageTitle: 'Search'
            })
            .state('stage.create', {
                url: '/create',
                templateUrl: '/cabinet/views/stage/create.html',
                controller: StageCreateController,
                pageTitle: 'Create'
            });
    });

    function StageSearchController($scope, $apInternalApi, $apAuth, $processing)
    {
        var
            loadStages = function ()
            {
                $apInternalApi.stages()
                    .then(function (stages) {
                        $scope.stages = stages;
                    });
            };

        $scope.accesses = $apAuth.isAccesses({
            stageEdit: 'STAGE_EDIT',
            stageCreate: 'STAGE_CREATE'
        });

        $scope.updateLabel = function (id, label)
        {
            var stage = $scope.stages.findById(id);

            if (stage && $processing.isNot(stage)) {
                $processing.start(stage);

                stage.label = label;
                $apInternalApi.stageUpdate(stage)
                    .then(
                        function () {
                            $processing.end(stage);
                        },

                        function () {
                            $processing.end(stage);
                        }
                    )
            }
        };

        loadStages();
    }

    function StageCreateController($scope, $apInternalApi, $state, $processing, Notification)
    {
        $scope.stage = {
            label: null,
            position: 0
        };

        $scope.create = function ()
        {
            if ($processing.is($scope.stage)) {
                return;
            }

            $processing.start($scope.stage);

            $scope.stage.errors = null;

            $apInternalApi.stageCreate($scope.stage)
                .then(
                    function () {
                        $processing.end($scope.stage);
                        $state.go('stage.search')
                            .then(function () {
                                Notification.success({
                                    message: 'Successfully create stage.'
                                });
                            });
                    },

                    function (response) {
                        $processing.end($scope.stage);
                        if (response.isRequestNotValid()) {
                            $scope.stage.errors = response.errorData;
                        }
                    }
                );
        };
    }
})(window.angular);