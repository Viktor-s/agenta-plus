;(function (angular) {
    "use strict";

    var loadingModule = angular.module('processing', []);

    loadingModule.service('$processing', function () {
        /**
         * Start process for object
         *
         * @param {Object} object
         */
        this.start = function (object)
        {
            object.processed = true;
        };

        /**
         * Is object processed
         *
         * @param {Object} object
         *
         * @returns {boolean}
         */
        this.is = function (object)
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
        this.isNot = function (object)
        {
            return !this.is(object);
        };

        /**
         * End process for object
         *
         * @param {Object} object
         */
        this.end = function (object)
        {
            object.processed = false;
        }
    });
})(window.angular);