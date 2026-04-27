<?php

declare(strict_types=1);

$host = 'localhost';
$dbName = 'assignment_system';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $exception) {
    exit('Database connection failed: ' . $exception->getMessage());
}

function requireColumn(PDO $pdo, string $table, string $column): void
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
         AND TABLE_NAME = :table_name
         AND COLUMN_NAME = :column_name'
    );
    $statement->execute([
        'table_name' => $table,
        'column_name' => $column,
    ]);

    if ((int) $statement->fetchColumn() === 0) {
        exit(
            "Database schema error: column '{$column}' is missing from table '{$table}'. " .
            "Please re-import database.sql or update your MySQL table structure."
        );
    }
}

requireColumn($pdo, 'users', 'user_id');
requireColumn($pdo, 'users', 'name');
requireColumn($pdo, 'users', 'email');
requireColumn($pdo, 'users', 'password');
requireColumn($pdo, 'users', 'role');
requireColumn($pdo, 'assignments', 'assignment_id');
requireColumn($pdo, 'assignments', 'title');
requireColumn($pdo, 'assignments', 'description');
requireColumn($pdo, 'submissions', 'id');
requireColumn($pdo, 'submissions', 'user_id');
requireColumn($pdo, 'submissions', 'assignment_id');
requireColumn($pdo, 'submissions', 'file');
