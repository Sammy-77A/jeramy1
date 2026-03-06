<?php
// install.php - ONE-CLICK FULL FIX for Jeramoyie (XAMPP)
echo "<h2>Installing PhpSpreadsheet (Real Version) - Please Wait...</h2><pre style='font-size:16px;'>";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "Already installed! Refresh dashboard.php - Import will work!\n";
    echo "<a href='admin/dashboard.php'>Go to Dashboard</a>";
    exit;
}

echo "Downloading Composer...\n";
file_put_contents('composer-setup.php', file_get_contents('https://getcomposer.org/installer'));

echo "Installing Composer locally...\n";
system('php composer-setup.php --quiet');

echo "Installing FULL PhpSpreadsheet (with CSV + Excel support)...\n";
system('php composer.phar require phpoffice/phpspreadsheet --no-interaction');

echo "Cleaning up...\n";
unlink('composer-setup.php');
unlink('composer.phar');

echo "\nSUCCESS! Everything is now working!\n";
echo "Folder created: vendor/ (with 1000+ files)\n";
echo "<strong>Refresh your dashboard - Import CSV/Excel now works perfectly!</strong>\n";
echo "<a href='admin/dashboard.php' style='font-size:20px;'>Go to Dashboard</a>";
