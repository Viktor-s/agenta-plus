;(function (angular) {
    "use strict";

    var stageModule = angular.module('ap.controller.stage', ['ap.auth', 'ap.api.internal', 'ui.router', 'ap.loading']);

    stageModule.config(function ($stateProvider) {
        $stateProvider
            .state('stage', {
                url: '/stage',
                templateUrl: '/cabinet/views/stage/main.html'
            })
            .state('stage.search', {
                url: '/search',
                templateUrl: '/cabinet/views/stage/search.html',
                controller: StageSearchController
            })
            .state('stage.create', {
                url: '/create',
                templateUrl: '/cabinet/views/stage/create.html',
                controller: StageCreateController
            });
    });

    function StageSearchController($scope, $apInternalApi, $apAuth, $apLoading)
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

            if (stage && $apLoading.isNotProcessed(stage)) {
                $apLoading.startProcess(stage);

                stage.label = label;
                $apInternalApi.stageUpdate(stage)
                    .then(
                        function () {
                            $apLoading.endProcess(stage);
                        },

                        function () {
                            $apLoading.endProcess(stage);
                            // @todo: control error
                        }
                    )
            }
        };

        loadStages();
    }

    function StageCreateController($scope, $apInternalApi, $state)
    {
        $scope.stage = {
            label: null,
            position: 0
        };

        $scope.create = function ()
        {
            $scope.stage.errors = null;

            $apInternalApi.stageCreate($scope.stage)
                .then(
                    function () {
                        $state.go('stage.search');
                    },

                    function (response) {
                        if (response.isRequestNotValid()) {
                            $scope.stage.errors = response.errorData;
                        }
                    }
                );
        };
    }
})(window.angular);