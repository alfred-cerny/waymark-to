-- Table: signpost
CREATE TABLE IF NOT EXISTS signpost
(
    id           CHAR(26) PRIMARY KEY,         -- ULID is 26 characters, automatically indexed by PRIMARY KEY
    alias        VARCHAR(256) NOT NULL UNIQUE, -- UNIQUE constraint creates an implicit unique index (no need for idx_signposts_short_code)
    original_url TEXT         NOT NULL UNIQUE, -- UNIQUE constraint creates an implicit unique index
    is_active    BOOLEAN   DEFAULT TRUE,       -- Index on boolean columns is often less effective, depending on data distribution and query patterns. Removed for optimization.
    expires_at   TIMESTAMP    NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted      BOOLEAN   DEFAULT FALSE       -- Index on boolean columns is often less effective. Removed for optimization.
);

-- Indexes for signpost table
CREATE INDEX IF NOT EXISTS idx_signposts_expires_at ON signpost (expires_at);
CREATE INDEX IF NOT EXISTS idx_signposts_created_at ON signpost (created_at);

-- Trigger to update 'updated_at' column automatically for signpost table
CREATE TRIGGER update_signpost_updated_at
    BEFORE UPDATE
    ON signpost
    FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();


-- Table: domain
CREATE TABLE IF NOT EXISTS domain
(
    id         CHAR(26) PRIMARY KEY,         -- ULID is 26 characters, automatically indexed by PRIMARY KEY
    name       VARCHAR(255) NOT NULL UNIQUE, -- UNIQUE constraint creates an implicit unique index (no need for idx_domains_name)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted    BOOLEAN   DEFAULT FALSE       -- Index on boolean columns is often less effective. Removed for optimization.
);

-- Indexes for domain table
CREATE INDEX IF NOT EXISTS idx_domains_created_at ON domain (created_at);

-- Trigger to update 'updated_at' column automatically for domain table
CREATE TRIGGER update_domain_updated_at
    BEFORE UPDATE
    ON domain
    FOR EACH ROW
EXECUTE FUNCTION update_updated_at_column();


-- Table: signpost_domain (Junction Table)
CREATE TABLE IF NOT EXISTS signpost_domain
(
    id          CHAR(26) PRIMARY KEY, -- ULID is 26 characters, automatically indexed by PRIMARY KEY
    signpost_id CHAR(26) NOT NULL,
    domain_id   CHAR(26) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- This unique constraint creates a composite unique index on (signpost_id, domain_id).
    -- It also covers lookups solely on signpost_id (left-most prefix matching),
    -- making idx_signpost_domain_signpost_id redundant.
    CONSTRAINT unique_signpost_domain UNIQUE (signpost_id, domain_id)
);

-- Foreign Key Constraints for signpost_domain
ALTER TABLE signpost_domain
    ADD CONSTRAINT fk_signpost_domain_signpost
        FOREIGN KEY (signpost_id) REFERENCES signpost (id) ON DELETE CASCADE;

ALTER TABLE signpost_domain
    ADD CONSTRAINT fk_signpost_domain_domain
        FOREIGN KEY (domain_id) REFERENCES domain (id) ON DELETE CASCADE;

-- Indexes for signpost_domain table
-- The composite unique index (signpost_id, domain_id) covers queries filtering by signpost_id.
-- However, an index on domain_id alone is crucial for efficient lookups by domain.
CREATE INDEX IF NOT EXISTS idx_signpost_domain_domain_id ON signpost_domain (domain_id);