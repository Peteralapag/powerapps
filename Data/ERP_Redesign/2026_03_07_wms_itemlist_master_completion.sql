-- WMS Item Master Completion (ERP-safe, non-breaking)
-- Date: 2026-03-07

-- Added fields for costing, pricing clarity, and governance metadata.
-- This migration is designed to be executed via an adaptive script for idempotency.

ALTER TABLE wms_itemlist
  ADD COLUMN selling_price DECIMAL(18,4) NULL AFTER unit_price,
  ADD COLUMN standard_cost DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER selling_price,
  ADD COLUMN last_purchase_cost DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER standard_cost,
  ADD COLUMN moving_average_cost DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER last_purchase_cost,
  ADD COLUMN cost_method ENUM('FIFO','MOVING_AVG','STANDARD') NOT NULL DEFAULT 'MOVING_AVG' AFTER moving_average_cost,
  ADD COLUMN brand VARCHAR(100) NULL AFTER class,
  ADD COLUMN model VARCHAR(100) NULL AFTER brand,
  ADD COLUMN deleted_at DATETIME NULL AFTER updated_at;

-- Backfill pricing bridge: keep existing unit_price as initial selling price if missing.
UPDATE wms_itemlist
SET selling_price = unit_price
WHERE (selling_price IS NULL OR selling_price = 0)
  AND unit_price IS NOT NULL
  AND unit_price > 0;

-- Optional indexes for reporting/performance.
ALTER TABLE wms_itemlist
  ADD INDEX idx_wms_itemlist_cost_method (cost_method),
  ADD INDEX idx_wms_itemlist_deleted_at (deleted_at),
  ADD INDEX idx_wms_itemlist_brand (brand);
