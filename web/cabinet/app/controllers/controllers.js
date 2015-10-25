;(function (angular) {
    "use strict";

    angular.module('ap.controller', [
        'ap.controller.security',
        'ap.controller.dashboard',
        'ap.controller.profile',
        'ap.controller.client',
        'ap.controller.factory'
    ]);
})(window.angular);
