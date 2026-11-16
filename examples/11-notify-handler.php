<?php
// notify_url endpoint called by AfribaPay (POST), not a page to open in a browser.
// The JSON file storage is a demo: replace it with your database.

require_once dirname(__DIR__) . '/src/afribapay.notification.php';

function notify_log(string $message): void {
    error_log('[AfribaPay notification] ' . $message);
}

try {
    $payment = AfribaPayNotification::verify();
    // Stricter, one extra API call: AfribaPayNotification::verify(confirmWithApi: true);
} catch (AfribaPayNotificationException $e) {
    notify_log('rejected (HTTP ' . $e->getHttpCode() . '): ' . $e->getMessage());
    if ($e->getHttpCode() === 405) {
        header('Allow: POST');
    }
    AfribaPayNotification::acknowledge($e->getHttpCode(), $e->getMessage());
    exit;
}
notify_log("received: transaction={$payment->transactionId} order={$payment->orderId} status={$payment->status} signature=valid");

$storeFile = sys_get_temp_dir() . '/afribapay-demo-orders.json';
$handle = fopen($storeFile, 'c+');
if ($handle === false || !flock($handle, LOCK_EX)) {
    AfribaPayNotification::acknowledge(503, 'Service unavailable.');
    exit;
}
$store = json_decode((string)stream_get_contents($handle), true) ?: [];
$store['orders'] = ($store['orders'] ?? []) + [
    'ORDER-42' => ['amount' => 2000, 'currency' => 'XOF', 'country' => 'SN', 'status' => 'pending', 'transaction_id' => null],
];
$store['transactions'] = $store['transactions'] ?? [];
$store['conflicts'] = $store['conflicts'] ?? [];

$save = function () use ($handle, &$store): bool {
    return ftruncate($handle, 0) && rewind($handle)
        && fwrite($handle, json_encode($store, JSON_PRETTY_PRINT)) !== false && fflush($handle);
};
$finish = function (int $code, string $message) use ($handle): void {
    flock($handle, LOCK_UN);
    fclose($handle);
    AfribaPayNotification::acknowledge($code, $message);
    exit;
};
$flagConflict = function (string $reason) use (&$store, $payment, $save, $finish): void {
    $store['conflicts'][] = ['date' => date('c'), 'reason' => $reason, 'transaction_id' => $payment->transactionId,
        'order_id' => $payment->orderId, 'status' => $payment->status, 'amount' => $payment->amount, 'currency' => $payment->currency];
    notify_log("conflict: $reason (transaction={$payment->transactionId} order={$payment->orderId})");
    $save() ? $finish(200, 'OK') : $finish(500, 'Storage error.');
};

$known = $store['transactions'][$payment->transactionId] ?? null;
if ($known !== null) {
    if ($known['status'] === $payment->status) {
        $finish(200, 'OK');
    }
    $flagConflict("final status changed from {$known['status']} to {$payment->status}");
}

$order = $store['orders'][$payment->orderId] ?? null;
if ($order === null) {
    notify_log("unknown order {$payment->orderId}");
    $finish(400, 'Unknown order.');
}

if ($payment->isSuccessful()) {
    if ($order['status'] === 'paid') {
        $flagConflict('order already paid by transaction ' . $order['transaction_id']);
    }
    if (!$payment->matchesOrder($payment->orderId, $order['amount'], $order['currency'], $order['country'])) {
        $order['status'] = 'review';
        notify_log("amount/currency/country mismatch for order {$payment->orderId}");
    } else {
        $order['status'] = 'paid';
    }
    $order['transaction_id'] = $payment->transactionId;
} elseif ($order['status'] === 'pending') {
    $order['status'] = 'failed';
    $order['transaction_id'] = $payment->transactionId;
}

$store['orders'][$payment->orderId] = $order;
$store['transactions'][$payment->transactionId] = ['status' => $payment->status, 'order_id' => $payment->orderId, 'date' => date('c')];

// HTTP 200 only once the update is saved
$save() ? $finish(200, 'OK') : $finish(500, 'Storage error.');
