# PostgreSQL configuration
class { 'postgresql::server':
}

postgresql::server::db { "agent_plus":
    user     => "${postgres_username}",
    password => postgresql_password("${postgres_username}", "${postgres_password}"),
}
