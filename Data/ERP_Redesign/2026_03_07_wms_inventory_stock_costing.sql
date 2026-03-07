-- Add costing fields directly on inventory balance table
-- NOTE: this SQL is for first-time execution only (non-idempotent).

ALTER TABLE wms_inventory_stock
    ADD COLUMN last_purchase_cost DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER stock_in_hand,
    ADD COLUMN moving_average_cost DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER last_purchase_cost,
    ADD COLUMN inventory_value DECIMAL(18,4) NOT NULL DEFAULT 0.0000 AFTER moving_average_cost;
