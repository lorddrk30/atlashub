<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Throwable;

class DatabaseConnectionDetector
{
    /**
     * Detects if a throwable chain is related to database connectivity.
     */
    public static function isConnectionIssue(Throwable $throwable): bool
    {
        $cursor = $throwable;

        while ($cursor) {
            if ($cursor instanceof QueryException && self::isConnectionSqlState($cursor)) {
                return true;
            }

            if (self::messageLooksLikeConnectionIssue((string) $cursor->getMessage())) {
                return true;
            }

            $cursor = $cursor->getPrevious();
        }

        return false;
    }

    private static function isConnectionSqlState(QueryException $exception): bool
    {
        $sqlState = strtoupper((string) ($exception->errorInfo[0] ?? ''));

        return in_array($sqlState, [
            '08001',
            '08003',
            '08004',
            '08006',
            '08007',
            '08P01',
            '57P03',
        ], true);
    }

    private static function messageLooksLikeConnectionIssue(string $message): bool
    {
        $normalized = strtolower($message);

        $signals = [
            'sqlstate[08001]',
            'sqlstate[08003]',
            'sqlstate[08004]',
            'sqlstate[08006]',
            'sqlstate[08007]',
            'sqlstate[08p01]',
            'sqlstate[57p03]',
            'sqlstate[hy000] [2002]',
            'could not find driver',
            'connection refused',
            'connection timed out',
            'server has gone away',
            'unable to connect',
            'could not connect',
            'no such host is known',
            'unknown database',
            'no existe la base de datos',
            'connection to server',
            'connection is broken',
        ];

        foreach ($signals as $signal) {
            if (str_contains($normalized, $signal)) {
                return true;
            }
        }

        return false;
    }
}

