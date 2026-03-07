-- Supplier master transition layer
-- Goal: use `suppliers` as central master while preserving legacy `wms_supplier` usage.

CREATE TABLE IF NOT EXISTS wms_supplier_master_map (
  map_id BIGINT NOT NULL AUTO_INCREMENT,
  legacy_supplier_id BIGINT NOT NULL,
  master_supplier_id BIGINT NULL,
  match_method VARCHAR(30) NULL,
  match_confidence DECIMAL(5,2) NULL,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  notes VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  PRIMARY KEY (map_id),
  UNIQUE KEY uk_wms_supplier_master_map_legacy (legacy_supplier_id),
  KEY idx_wms_supplier_master_map_master (master_supplier_id),
  KEY idx_wms_supplier_master_map_verified (is_verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add master supplier pointer to item-supplier bridge
ALTER TABLE wms_item_suppliers
  ADD COLUMN supplier_master_id BIGINT NULL AFTER supplier_id,
  ADD INDEX idx_wms_item_suppliers_master (supplier_master_id);

-- Seed all legacy suppliers
INSERT INTO wms_supplier_master_map (legacy_supplier_id, created_at)
SELECT ws.id, NOW()
FROM wms_supplier ws
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- Auto-map by TIN (high confidence)
UPDATE wms_supplier_master_map m
JOIN wms_supplier ws ON ws.id = m.legacy_supplier_id
JOIN suppliers s ON s.tin IS NOT NULL AND s.tin <> '' AND ws.tin IS NOT NULL AND ws.tin <> '' AND s.tin = ws.tin
SET m.master_supplier_id = s.id,
    m.match_method = 'TIN',
    m.match_confidence = 1.00,
    m.updated_at = NOW()
WHERE m.master_supplier_id IS NULL;

-- Auto-map by exact normalized name (fallback)
UPDATE wms_supplier_master_map m
JOIN wms_supplier ws ON ws.id = m.legacy_supplier_id
JOIN suppliers s ON UPPER(TRIM(s.name)) = UPPER(TRIM(ws.name))
SET m.master_supplier_id = s.id,
    m.match_method = 'NAME_EXACT',
    m.match_confidence = 0.85,
    m.updated_at = NOW()
WHERE m.master_supplier_id IS NULL;

-- Push mapping into item supplier bridge
UPDATE wms_item_suppliers wis
JOIN wms_supplier_master_map m ON m.legacy_supplier_id = wis.supplier_id
SET wis.supplier_master_id = m.master_supplier_id,
    wis.updated_at = NOW()
WHERE wis.supplier_master_id IS NULL;

-- Check status
SELECT COUNT(*) AS legacy_total FROM wms_supplier_master_map;
SELECT COUNT(*) AS mapped_total FROM wms_supplier_master_map WHERE master_supplier_id IS NOT NULL;
SELECT COUNT(*) AS unmapped_total FROM wms_supplier_master_map WHERE master_supplier_id IS NULL;
