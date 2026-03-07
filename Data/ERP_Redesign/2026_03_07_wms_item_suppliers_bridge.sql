-- ERP bridge migration: item <-> supplier normalization
-- Date: 2026-03-07

CREATE TABLE IF NOT EXISTS wms_item_suppliers (
  item_supplier_id BIGINT NOT NULL AUTO_INCREMENT,
  item_id BIGINT NOT NULL,
  supplier_id BIGINT NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 1,
  lead_time_days INT NULL,
  last_price DECIMAL(18,4) NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by VARCHAR(100) NULL,
  updated_at DATETIME NULL,
  updated_by VARCHAR(100) NULL,
  PRIMARY KEY (item_supplier_id),
  UNIQUE KEY uk_wms_item_suppliers_item_supplier (item_id, supplier_id),
  KEY idx_wms_item_suppliers_item (item_id),
  KEY idx_wms_item_suppliers_supplier (supplier_id),
  KEY idx_wms_item_suppliers_primary (is_primary)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Backfill from legacy single-supplier column in wms_itemlist
INSERT INTO wms_item_suppliers (item_id, supplier_id, is_primary, active, created_at)
SELECT wi.id,
       wi.supplier_id,
       1,
       CASE WHEN COALESCE(wi.active,0)=1 THEN 1 ELSE 0 END,
       COALESCE(wi.created_at, wi.date_added, NOW())
FROM wms_itemlist wi
INNER JOIN wms_supplier ws ON ws.id = wi.supplier_id
WHERE wi.supplier_id IS NOT NULL
ON DUPLICATE KEY UPDATE
  is_primary = VALUES(is_primary),
  active = VALUES(active),
  updated_at = NOW();
