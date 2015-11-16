;(function (angular) {
    "use strict";

    angular.module('ap.controllers', [
        'ap.controller.security',
        'ap.controller.dashboard',
        'ap.controller.profile',
        'ap.controller.client',
        'ap.controller.factory',
        'ap.controller.diary',
        'ap.controller.order',
        'ap.controller.stage',
        'ap.controller.catalog'
    ]);
})(window.angular);
