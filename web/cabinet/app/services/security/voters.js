;(function () {

    var authModule = angular.module('ap.auth');

    authModule.run(function ($apAuth) {
        $apAuth.addVoters([
            new FactoryListVoter(),
            new FactoryCreateVoter(),
            new FactoryEditVoter(),

            new ClientListVoter(),
            new ClientCreateVoter(),
            new ClientEditVoter(),

            new DiaryListVoter(),
            new DiaryCreateVoter(),
            new DiaryEditVoter(),

            new StageListVoter(),
            new StageCreateVoter(),
            new StageEditVoter()
        ]);
    });

    /**
     * Voter for factory list.
     * Access only for agent and personal.
     *
     * @constructor
     */
    function FactoryListVoter ()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'FACTORY_LIST') {
                return 0;
            }

            if (user.type == 1 || user.type == 2) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for create factory
     * Access only for agent
     *
     * @constructor
     */
    function FactoryCreateVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'FACTORY_CREATE') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for edit factory.
     * Access only for agent
     *
     * @constructor
     */
    function FactoryEditVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'FACTORY_EDIT') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted to client list.
     * Access only for agent and personal.
     *
     * @constructor
     */
    function ClientListVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'CLIENT_LIST') {
                return 0;
            }

            if (user.type == 1 || user.type == 2) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for create client
     * Access only for agent
     *
     * @constructor
     */
    function ClientCreateVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'CLIENT_CREATE') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for edit client
     * Access only for agent
     *
     * @constructor
     */
    function ClientEditVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'CLIENT_EDIT') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for diary list
     * Access to all users
     *
     * @constructor
     */
    function DiaryListVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'DIARY_LIST') {
                return 0;
            }

            return 1;
        }
    }

    /**
     * Voter for check granted for create diary record
     * Access to agent and employee
     *
     * @constructor
     */
    function DiaryCreateVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'DIARY_CREATE') {
                return 0;
            }

            if (user.type == 1 || user.type == 2) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for edit diary record.
     * Access for agent or only owners.
     *
     * @constructor
     */
    function DiaryEditVoter()
    {
        this.vote = function (user, attribute, object)
        {
            if (attribute != 'DIARY_EDIT') {
                return 0;
            }

            if (user.type == 1) {
                // Grant access for agent
                return 1;
            }

            if (user.id == object.creator.id) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for stage list
     * Access only to agent.
     *
     * @constructor
     */
    function StageListVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'STAGE_LIST') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for create stage.
     * Access only for agent.
     *
     * @constructor
     */
    function StageCreateVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'STAGE_CREATE') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }

    /**
     * Voter for check granted for edit stage.
     * Access only for agent.
     *
     * @constructor
     */
    function StageEditVoter()
    {
        this.vote = function (user, attribute)
        {
            if (attribute != 'STAGE_EDIT') {
                return 0;
            }

            if (user.type == 1) {
                return 1;
            }

            return -1;
        }
    }
})(window.angular);