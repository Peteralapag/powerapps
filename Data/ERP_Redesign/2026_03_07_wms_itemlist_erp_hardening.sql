-- ERP hardening for wms_itemlist (safe, backward-compatible)
-- Date: 2026-03-07

-- 1) Expand item description capacity
ALTER TABLE wms_itemlist
  MODIFY COLUMN item_description VARCHAR(255) NULL;

-- 2) Ensure timestamps are populated before enforcing defaults
UPDATE wms_itemlist
SET created_at = COALESCE(created_at, date_added, NOW())
WHERE created_at IS NULL;

UPDATE wms_itemlist
SET updated_at = COALESCE(updated_at, date_updated, created_at, NOW())
WHERE updated_at IS NULL;

-- 3) Normalize item_status from active where possible
UPDATE wms_itemlist
SET item_status = CASE
  WHEN active = 1 THEN 'active'
  WHEN active = 0 THEN 'inactive'
  ELSE item_status
END
WHERE item_status IS NULL OR item_status = '';

-- 4) Enforce stronger defaults/constraints
ALTER TABLE wms_itemlist
  MODIFY COLUMN item_uuid CHAR(36) NOT NULL,
  MODIFY COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  MODIFY COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 5) Add unique key for item_code if your business requires unique SKU/code
-- (execute only if no duplicates exist)
ALTER TABLE wms_itemlist
  ADD UNIQUE KEY uk_wms_itemlist_item_code (item_code);
