;(function (angular) {
    "use strict";

    var loadingModule = angular.module('ap.loading', []);

    loadingModule.service('$apLoading', function () {
        /**
         * Start process for object
         *
         * @param {Object} object
         */
        this.startProcess = function (object)
        {
            object.loading = true;
            object.processed = true;
        };

        /**
         * Is object processed
         *
         * @param {Object} object
         *
         * @returns {boolean}
         */
        this.isProcessed = function (object)
        {
            return object.hasOwnProperty('processed') && object.processed;
        };

        /**
         * Is object not processed
         *
         * @param {Object} object
         *
         * @returns {boolean}
         */
        this.isNotProcessed = function (object)
        {
            return !this.isProcessed(object);
        };

        /**
         * End process for object
         *
         * @param {Object} object
         */
        this.endProcess = function (object)
        {
            object.loading = false;
            object.processed = false;
        }
    });
})(window.angular);