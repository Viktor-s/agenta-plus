<?php

require 'recipe/composer.php';

// Set configurations
set('repository', 'todo');
set('shared_files', ['app/config.php']);
set('shared_dirs', ['var/logs']);
set('writable_dirs', ['var/logs']);

// Configure servers
server('production', '') // @todo: set name
    ->user('agentplus')
    ->password()
    ->stage('production')
    ->env('deploy_path', '/var/www/agentplus.com');

// Declare tasks (Attention: remove this task, is use not PHP-FPM!)
task('php-fpm:restart', function () {
    // Attention: The user must have rights for restart service
    // /etc/sudoers: agentplus ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo /bin/systemctl restart php-fpm.service');
})->desc('Restart PHP-FPM service');

task('remove-files', function () {
    // Remove development files (app_dev.php, app_test.php)
    run('rm -f {{release_path}}/web/app_*.php');
})->setPrivate();

task('migrations:migrate', function () {
    // Run migrations
    run('php {{release_path}}/bin/console doctrine:migrations:migrate -n');
})->desc('Run migrations');

task('cache:clear', function () {
    // Clear cache
    run('php {{release_path}}/bin/console cache:clear --env prod --no-debug');
})->desc('Clear cache');


// Run tasks
after('deploy:update_code', 'deploy:shared');
after('deploy:update_code', 'remove-files');
after('success', 'php-fpm:restart');
after('php-fpm:restart', 'cache:clear');
after('cache:clear', 'migrations:migrate');
