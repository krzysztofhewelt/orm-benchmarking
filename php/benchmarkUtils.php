<?php
declare(strict_types=1);

function backupDatabase(): void
{
    exec("php ../../databaseBackup.php");
}

function restoreDatabase(): void
{
    exec("php ../../databaseRestore.php");
}

function getMethodArgumentForMethod(string $type, string $table = '', int $quantity = 1, array $data = []): int|string|array
{
    if ($type === 'select' || $type === 'update' || $type === 'delete')
        return $quantity;

    if ($type === 'insert') {
        if ($table === 'users')
            return array_slice($data, 0, $quantity);

        if ($table === 'courses')
            return array_slice($data, 0, $quantity);
    }

    return '';
}