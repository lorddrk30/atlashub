<?php

use App\Support\DatabaseConnectionDetector;
use Illuminate\Database\QueryException;

it('detects database connectivity issues from sqlstate', function (): void {
    $previous = new PDOException('SQLSTATE[08006] [7] FATAL: no existe la base de datos "atlashub"');
    $exception = new QueryException('pgsql', 'select 1', [], $previous);

    expect(DatabaseConnectionDetector::isConnectionIssue($exception))->toBeTrue();
});

it('detects database connectivity issues from common transport message', function (): void {
    $exception = new RuntimeException('SQLSTATE[HY000] [2002] Connection refused');

    expect(DatabaseConnectionDetector::isConnectionIssue($exception))->toBeTrue();
});

it('ignores query errors that are not connectivity related', function (): void {
    $previous = new PDOException('SQLSTATE[42703]: Undefined column: 7 ERROR: column does not exist');
    $exception = new QueryException('pgsql', 'select "missing"', [], $previous);

    expect(DatabaseConnectionDetector::isConnectionIssue($exception))->toBeFalse();
});

