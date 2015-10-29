;(function (angular){
    "use strict";

    var jsonRpc = angular.module('jsonRpc', []);

    jsonRpc.service('$jsonRpc', function ($http, $q) {
        /**
         * Prepare request data
         *
         * @param {String} method
         * @param {Object} parameters
         * @param {Number} id
         *
         * @returns {Object}
         */
        this.prepareData = function (method, parameters, id)
        {
            var data = {
                method: method
            };

            if (parameters) {
                data.params = parameters;
            }

            if (id) {
                data.id = id;
            }

            return data;
        };

        /**
         * Send JSON-RPC request
         *
         * @param {String} url
         * @param {String} method
         * @param {Object} [parameters]
         * @param {Number} [id]
         * @param {Object} [headers]
         *
         * @returns {Object}
         */
        this.request = function (url, method, parameters, id, headers)
        {
            if (typeof headers == 'undefined') {
                headers = {};
            }

            headers['Content-Type'] = 'application/json-rpc';

            var deferred = $q.defer();

            var request = {
                method: 'POST',
                url: url,
                data: this.prepareData(method, parameters, id),
                headers: headers
            };

            $http(request).then(
                function (response) {
                    var jsonRpcResponse = JsonRpcResponse.create(response);

                    if (jsonRpcResponse.isSuccessfully()) {
                        deferred.resolve(jsonRpcResponse);
                    } else {
                        deferred.reject(jsonRpcResponse);
                    }
                },

                function (response) {
                    var jsonRpcResponse = JsonRpcResponse.create(response);

                    deferred.reject(jsonRpcResponse);
                }
            );

            return deferred.promise;
        }
    });

    /**
     * JSON-RPC Response
     *
     * @param {Object} response
     *
     * @constructor
     */
    function JsonRpcResponse(response)
    {
        var _error = false,
            _criticalError = false,
            _self = this;

        if (response.data && response.data.hasOwnProperty('error')) {
            _error = true;
        }

        if (response.status != 200) {
            _criticalError = true;
        }

        Object.defineProperty(this, 'httpResponse', {
            get: function () { return response; }
        });

        Object.defineProperty(this, 'result', {
            get: function () {
                if (_self.isError()) {
                    return null;
                }

                return response.data.result;
            }
        });

        Object.defineProperty(this, 'id', {
            get: function () {
                if (_self.isCriticalError()) {
                    return null;
                }

                return response.data.id;
            }
        });

        Object.defineProperty(this, 'errorCode', {
            get: function () {
                if (!_self.isError()) {
                    return null;
                }

                if (_self.isCriticalError()) {
                    return null;
                }

                return response.data.error.code;
            }
        });

        Object.defineProperty(this, 'errorMessage', {
            get: function () {
                if (!_self.isError()) {
                    return null;
                }

                if (_self.isCriticalError()) {
                    return null;
                }

                return response.data.error.message;
            }
        });

        Object.defineProperty(this, 'errorData', {
            get: function () {
                if (!_self.isError()) {
                    return null;
                }

                if (_self.isCriticalError()) {
                    return null;
                }

                if (response.data.error.hasOwnProperty('data')) {
                    return response.data.error.data;
                }

                return null;
            }
        });

        Object.defineProperty(this, 'httpStatus', {
            get: function () {
                return response.status;
            }
        });

        /**
         * Is error
         *
         * @returns {boolean}
         */
        this.isError = function ()
        {
            return _error || _criticalError;
        };

        /**
         * Is critical error
         *
         * @returns {boolean}
         */
        this.isCriticalError = function ()
        {
            return _criticalError;
        };

        /**
         * Is successfully
         *
         * @returns {boolean}
         */
        this.isSuccessfully = function ()
        {
            return !_error;
        };

        /**
         * Is request not valid
         *
         * @returns {boolean}
         */
        this.isRequestNotValid = function ()
        {
            return this.errorCode == 3;
        };
    }

    /**
     * Factory method for create JSON-RPC response
     *
     * @param response
     *
     * @returns {JsonRpcResponse}
     */
    JsonRpcResponse.create = function (response)
    {
        return new JsonRpcResponse(response);
    };

    window.JsonRpcResponse = JsonRpcResponse;
})(window.angular);