;(function (angular) {
    "use strict";

    var requestErrorsModule = angular.module('request.errors', ['ui-notification']);

    requestErrorsModule.config(function ($httpProvider) {
        $httpProvider.interceptors.push(RequestErrorsInterceptors);
    });

    var _errors = {
        "-32601": 'Method ":method" not found.',
        "-32500": 'Internal server error'
    };

    function RequestErrorsInterceptors($q, $injector)
    {
        return {
            request: function (config)
            {
                return config;
            },

            response: function (response)
            {
                if (response.headers('Content-Type') == 'application/json') {
                    // Try parse data
                    var data = response.data;

                    if (data.hasOwnProperty('jsonrpc')) {
                        // This is a JSON-RPC response. Parse error.

                        if (data.hasOwnProperty('error')) {
                            var error = data.error,
                                code = error.code,
                                message = error.message,
                                params = {
                                    ':method': response.config.data.method
                                },
                                i;

                            if (_errors.hasOwnProperty(code)) {
                                message = _errors[code];
                            }

                            for (i in params) {
                                if (params.hasOwnProperty(i)) {
                                    message = message.split(i).join(params[i]);
                                }
                            }

                            $injector.get('Notification').error({
                                message: message
                            });
                        }
                    }
                }

                return response;
            },

            responseError: function (rejection)
            {
                if (rejection.status == 401) {
                    // Unauthorized
                    $injector.get('$state').go('login');
                } else if (rejection.status == 500) {
                    $injector.get('Notification').error({
                        message: 'Server returned 500 error (Internal Server Error).'
                    });
                }

                return $q.reject(rejection);
            }
        }
    }
})(window.angular);
