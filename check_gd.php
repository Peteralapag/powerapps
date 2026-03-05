<?php
// Check GD extension
echo "GD Extension: " . (extension_loaded('gd') ? 'INSTALLED' : 'NOT INSTALLED') . "\n";
echo "Imagick Extension: " . (extension_loaded('imagick') ? 'INSTALLED' : 'NOT INSTALLED') . "\n";
echo "\nAll loaded extensions:\n";
print_r(get_loaded_extensions());

if (extension_loaded('gd')) {
    echo "\n\nGD Info:\n";
    print_r(gd_info());
}
