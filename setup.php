<?php
// Create necessary directories
$directories = [
    'config',
    'classes'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir\n";
    }
}

// Create .htaccess file
$htaccess = <<<EOT
RewriteEngine On
RewriteBase /sentiment%20analysis/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
EOT;

file_put_contents('.htaccess', $htaccess);
echo "Created .htaccess file\n";

echo "Setup completed successfully!\n";
