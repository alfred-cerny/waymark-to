CREATE TABLE IF NOT EXISTS signpost
(
    id           CHAR(26) PRIMARY KEY,        -- ULID is 26 characters
    short_code   VARCHAR(10) NOT NULL UNIQUE, -- The short identifier in the URL
    original_url TEXT        NOT NULL,        -- The original long URL
    is_active    BOOLEAN   DEFAULT TRUE,      -- Whether the link is active
    expires_at   TIMESTAMP   NULL,            -- Optional expiration date
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted      BOOLEAN   DEFAULT FALSE
);

CREATE INDEX IF NOT EXISTS idx_signposts_short_code ON signpost (short_code);
CREATE INDEX IF NOT EXISTS idx_signposts_created_at ON signpost (created_at);
CREATE INDEX IF NOT EXISTS idx_signposts_expires_at ON signpost (expires_at);
CREATE INDEX IF NOT EXISTS idx_signposts_is_active ON signpost (is_active);

CREATE TABLE IF NOT EXISTS domain
(
    id         CHAR(26) PRIMARY KEY,         -- ULID is 26 characters
    name       VARCHAR(255) NOT NULL UNIQUE, -- Domain name (e.g., 'example.com')
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted    BOOLEAN   DEFAULT FALSE
);

CREATE INDEX IF NOT EXISTS idx_domains_name ON domain (name);
CREATE INDEX IF NOT EXISTS idx_domains_created_at ON domain (created_at);

CREATE TABLE IF NOT EXISTS signpost_domain
(
    id          CHAR(26) PRIMARY KEY, -- ULID is 26 characters
    signpost_id CHAR(26) NOT NULL,
    domain_id   CHAR(26) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT unique_signpost_domain UNIQUE (signpost_id, domain_id)
);

ALTER TABLE signpost_domain
    ADD CONSTRAINT fk_signpost_domain_signpost
        FOREIGN KEY (signpost_id) REFERENCES signpost (id) ON DELETE CASCADE;

ALTER TABLE signpost_domain
    ADD CONSTRAINT fk_signpost_domain_domain
        FOREIGN KEY (domain_id) REFERENCES domain (id) ON DELETE CASCADE;

CREATE INDEX IF NOT EXISTS idx_signpost_domain_signpost_id ON signpost_domain (signpost_id);
CREATE INDEX IF NOT EXISTS idx_signpost_domain_domain_id ON signpost_domain (domain_id);