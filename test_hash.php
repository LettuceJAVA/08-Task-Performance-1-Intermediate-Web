<?php
$password = 'Admin123!';
$hash_in_db = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "Testing password: $password\n";
echo "Hash in DB: $hash_in_db\n";

if (password_verify($password, $hash_in_db)) {
    echo "MATCH! The password is correct.\n";
} else {
    echo "NO MATCH. The hash in the DB does not match '$password'.\n";
    echo "Generating new hash for '$password'...\n";
    echo "New Hash: " . password_hash($password, PASSWORD_DEFAULT) . "\n";
}
