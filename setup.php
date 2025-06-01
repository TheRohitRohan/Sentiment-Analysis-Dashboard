<?php
// Create necessary directories
$directories = [
    'logs',
    'cache',
    'config',
    'database',
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

// Create logs directory with proper permissions
chmod('logs', 0777);
echo "Set permissions for logs directory\n";

echo "Setup completed successfully!\n";
