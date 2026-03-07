-- Module-based visibility for centralized wms_itemlist
-- Non-breaking rule:
-- - If an item has no active mapping rows, it stays visible to all modules.
-- - If an item has active mappings, it is visible only to mapped modules.

CREATE TABLE IF NOT EXISTS wms_item_module_visibility (
    id BIGINT NOT NULL AUTO_INCREMENT,
    item_id BIGINT NOT NULL,
    module_code VARCHAR(120) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_by VARCHAR(120) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uk_wms_item_module_visibility_item_module (item_id, module_code),
    KEY idx_wms_item_module_visibility_module (module_code),
    KEY idx_wms_item_module_visibility_active (active),
    CONSTRAINT fk_wms_item_module_visibility_item
        FOREIGN KEY (item_id) REFERENCES wms_itemlist(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed initial mappings from recipient (idempotent)
INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'Warehouse_Management', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) = 'WAREHOUSE'
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();

INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'Property_Custodian_System', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) = 'PROPERTY CUSTODIAN'
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();

INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'Branch_Ordering_System', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) = 'BRANCH'
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();

INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'FD_Branch_Ordering_System', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) = 'BRANCH'
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();

INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'DBC_Branch_Ordering_System', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) = 'BRANCH'
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();

INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'DBC_Seasonal_Branch_Ordering_System', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) = 'BRANCH'
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();

INSERT INTO wms_item_module_visibility (item_id, module_code, active, created_by)
SELECT wi.id, 'DBC_Management', 1, 'erp_seed'
FROM wms_itemlist wi
WHERE UPPER(TRIM(COALESCE(wi.recipient,''))) IN ('DAVAO BAKING CENTER','DBC')
ON DUPLICATE KEY UPDATE active=VALUES(active), updated_at=NOW();
