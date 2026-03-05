<?php
class FIFOInventory
{
    private $inventory = array();

    public function purchase($item, $quantity, $cost)
    {
        if (!isset($this->inventory[$item])) {
            $this->inventory[$item] = array();
        }

        array_push($this->inventory[$item], array('quantity' => $quantity, 'cost' => $cost));
    }

    public function sell($item, $quantity)
    {
        if (!isset($this->inventory[$item])) {
            echo "Item not found in inventory.";
            return;
        }

        $remainingQuantity = $quantity;

        while ($remainingQuantity > 0 && count($this->inventory[$item]) > 0) {
            $itemEntry = reset($this->inventory[$item]);
            $availableQuantity = $itemEntry['quantity'];

            if ($availableQuantity > $remainingQuantity) {
                $this->inventory[$item][key($this->inventory[$item])]['quantity'] -= $remainingQuantity;
                $remainingQuantity = 0;
            } else {
                array_shift($this->inventory[$item]);
                $remainingQuantity -= $availableQuantity;
            }
        }
        if ($remainingQuantity > 0) {
            echo "Not enough quantity of $item available in inventory.";
        }
    }
    public function getInventory()
    {
        return $this->inventory;
    }
}

// Example usage:
$inventoryManager = new FIFOInventory();

// Purchasing items
$inventoryManager->purchase('itemA', 50, 10.00);
$inventoryManager->purchase('itemB', 30, 20.00);
$inventoryManager->purchase('itemA', 20, 12.50);

// Selling items
$inventoryManager->sell('itemA', 40);
$inventoryManager->sell('itemB', 15);

// Get remaining inventory
$remainingInventory = $inventoryManager->getInventory();
print_r($remainingInventory);
?>