-- Phase 1E submission tables for MariaDB 10.4+ / MySQL 8-compatible InnoDB.
-- REVIEW AND BACK UP THE TARGET DATABASE BEFORE EXECUTION. This file does not create a database.

CREATE TABLE quotation_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quotation_reference VARCHAR(40) NOT NULL,
    idempotency_hash CHAR(64) NOT NULL,
    confirmation_token_hash CHAR(64) NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    organisation VARCHAR(160) NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    preferred_contact_method VARCHAR(16) NOT NULL,
    notes TEXT NULL,
    compatibility_json LONGTEXT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'received',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_quotation_reference (quotation_reference),
    UNIQUE KEY uq_quotation_idempotency (idempotency_hash),
    UNIQUE KEY uq_quotation_confirmation_token (confirmation_token_hash),
    KEY ix_quotation_created_at (created_at),
    CONSTRAINT ck_quotation_preferred_contact CHECK (preferred_contact_method IN ('email', 'phone')),
    CONSTRAINT ck_quotation_compatibility_json CHECK (compatibility_json IS NULL OR JSON_VALID(compatibility_json))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quotation_request_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    quotation_request_id BIGINT UNSIGNED NOT NULL,
    product_id VARCHAR(100) NOT NULL,
    product_code VARCHAR(80) NOT NULL,
    product_name VARCHAR(180) NOT NULL,
    brand VARCHAR(160) NOT NULL,
    category VARCHAR(100) NOT NULL,
    product_type VARCHAR(120) NOT NULL,
    specification_json LONGTEXT NOT NULL,
    quantity TINYINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_quotation_item_product (quotation_request_id, product_id),
    CONSTRAINT fk_quotation_item_request FOREIGN KEY (quotation_request_id) REFERENCES quotation_requests (id) ON DELETE CASCADE,
    CONSTRAINT ck_quotation_item_quantity CHECK (quantity BETWEEN 1 AND 99),
    CONSTRAINT ck_quotation_item_specification_json CHECK (JSON_VALID(specification_json))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enquiries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    enquiry_reference VARCHAR(40) NOT NULL,
    idempotency_hash CHAR(64) NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    organisation VARCHAR(160) NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(40) NULL,
    enquiry_type VARCHAR(80) NOT NULL,
    preferred_contact_method VARCHAR(16) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'received',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_enquiry_reference (enquiry_reference),
    UNIQUE KEY uq_enquiry_idempotency (idempotency_hash),
    KEY ix_enquiry_created_at (created_at),
    CONSTRAINT ck_enquiry_preferred_contact CHECK (preferred_contact_method IN ('email', 'phone'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE submission_rate_limits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    scope VARCHAR(20) NOT NULL,
    key_hash CHAR(64) NOT NULL,
    window_started_at DATETIME NOT NULL,
    attempt_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rate_limit_bucket (scope, key_hash, window_started_at),
    KEY ix_rate_limit_updated_at (updated_at),
    CONSTRAINT ck_rate_limit_scope CHECK (scope IN ('quotation', 'enquiry'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
