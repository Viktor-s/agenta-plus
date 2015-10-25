# PostgreSQL configuration
class { 'postgresql::server':
}

postgresql::server::db { "agent_plus":
    user     => "${postgres_username}",
    password => postgresql_password("${postgres_username}", "${postgres_password}"),
}

postgresql::server::extension{ "uuid-ossp":
    database     => "agent_plus",
    package_name => "postgresql-contrib"
}