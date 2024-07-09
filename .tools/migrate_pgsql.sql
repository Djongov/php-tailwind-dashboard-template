CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    email VARCHAR(512) DEFAULT NULL,
    name VARCHAR(255) DEFAULT NULL,
    last_ips TEXT,
    origin_country VARCHAR(25) DEFAULT NULL,
    role VARCHAR(255) DEFAULT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    theme VARCHAR(20) DEFAULT NULL,
    picture VARCHAR(255) DEFAULT NULL,
    provider VARCHAR(255) DEFAULT NULL,
    enabled BOOLEAN DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS cache (
    id SERIAL PRIMARY KEY,
    value VARCHAR(5000) NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiration TIMESTAMP NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    type VARCHAR(255) NOT NULL,
    unique_property VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS firewall (
    id SERIAL PRIMARY KEY,
    ip_cidr VARCHAR(256) NOT NULL,
    created_by VARCHAR(1000) DEFAULT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comment VARCHAR(1000) DEFAULT NULL
);

INSERT INTO firewall (ip_cidr, created_by, comment)
VALUES 
    ('127.0.0.1/32', 'System', 'private range'),
    ('10.0.0.0/8', 'System', 'private range'),
    ('172.16.0.0/12', 'System', 'private range'),
    ('192.168.0.0/16', 'System', 'private range');

CREATE TABLE IF NOT EXISTS csp_reports (
    id SERIAL PRIMARY KEY,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data JSON NOT NULL,
    domain VARCHAR(60) DEFAULT NULL,
    url VARCHAR(2500) DEFAULT NULL,
    referrer VARCHAR(2500) DEFAULT NULL,
    violated_directive TEXT,
    effective_directive VARCHAR(2500) DEFAULT NULL,
    original_policy VARCHAR(5000) DEFAULT NULL,
    disposition VARCHAR(60) DEFAULT NULL,
    blocked_uri TEXT,
    line_number INT DEFAULT NULL,
    column_number INT DEFAULT NULL,
    source_file VARCHAR(1500) DEFAULT NULL,
    status_code INT DEFAULT NULL,
    script_sample VARCHAR(1500) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS csp_approved_domains (
    id SERIAL PRIMARY KEY,
    domain VARCHAR(255) NOT NULL,
    created_by VARCHAR(60) DEFAULT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS system_log (
    id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    client_ip VARCHAR(256) NOT NULL,
    user_agent TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    uri TEXT NOT NULL,
    method VARCHAR(20) NOT NULL
);
