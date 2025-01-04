<?php
$passwords = ['admin111', 'admin222', 'admin333', 'member111', 'member222', 'member333', 'member444', 'member555', 'member666', 'member777', 'member888', 'member999', 'member000'];
$hashedPasswords = [];

foreach ($passwords as $password) {
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
    $hashedPasswords[] = $hashedPassword;
    echo "Hashed password for $password: $hashedPassword\n";
}
?>
