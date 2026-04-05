<?php
/**
 * Audit Log Viewer - Admin only
 * Run this once to initialize the audit_log table, then visit to view logs.
 */
include "connection.php";
include "auth.php";
start_secure_session();
require_admin('login.php');

// Initialize the audit log table
init_audit_log_table();

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Filters
$filterEntity = $_GET['entity'] ?? '';
$filterAction = $_GET['action'] ?? '';
$filterUser = trim($_GET['user'] ?? '');

$where = [];
$params = [];
$types = '';

if ($filterEntity !== '') {
    $where[] = "entity = ?";
    $params[] = $filterEntity;
    $types .= 's';
}
if ($filterAction !== '') {
    $where[] = "action = ?";
    $params[] = $filterAction;
    $types .= 's';
}
if ($filterUser !== '') {
    $where[] = "username LIKE ?";
    $params[] = "%$filterUser%";
    $types .= 's';
}

$whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Total count
$countSql = "SELECT COUNT(*) FROM audit_log $whereSql";
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countStmt->bind_result($totalRows);
$countStmt->fetch();
$countStmt->close();

$totalPages = max(1, ceil($totalRows / $perPage));

// Fetch logs
$sql = "SELECT * FROM audit_log $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$logsStmt = $conn->prepare($sql);
$params[] = $perPage;
$offsetVal = $offset;
$types .= 'ii';
$logsStmt->bind_param($types, ...$params);
$logsStmt->execute();
$logsResult = $logsStmt->get_result();
$logs = $logsResult->fetch_all(MYSQLI_ASSOC);
$logsStmt->close();

// Distinct filter options
$entityOptions = $conn->query("SELECT DISTINCT entity FROM audit_log ORDER BY entity");
$actionOptions = $conn->query("SELECT DISTINCT action FROM audit_log ORDER BY action");
$userOptions = $conn->query("SELECT DISTINCT username FROM audit_log ORDER BY username");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log</title>
    <link rel="stylesheet" href="style/view.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f6f9; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { color: #1a2a6c; }
        .back-btn { background: #1a2a6c; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 600; }
        .filters { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; }
        .filters select, .filters input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .filters button { background: #1a2a6c; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; }
        .log-table { background: white; border-radius: 10px; overflow: hidden; width: 100%; border-collapse: collapse; }
        .log-table th { background: #1a2a6c; color: white; padding: 12px 15px; text-align: left; font-size: 13px; }
        .log-table td { padding: 10px 15px; border-bottom: 1px solid #eee; font-size: 13px; }
        .log-table tr:hover { background: #f8f9fa; }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; }
        .badge-create { background: #d4edda; color: #155724; }
        .badge-update { background: #fff3cd; color: #856404; }
        .badge-delete { background: #f8d7da; color: #721c24; }
        .badge-login { background: #d1ecf1; color: #0c5460; }
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .pagination a, .pagination span { padding: 6px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #333; }
        .pagination a:hover { background: #1a2a6c; color: white; }
        .pagination .current { background: #1a2a6c; color: white; }
        .empty-state { text-align: center; padding: 40px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-history"></i> Audit Log</h1>
            <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>

        <div class="filters">
            <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <select name="entity">
                    <option value="">All Entities</option>
                    <?php while ($e = $entityOptions->fetch_row()): ?>
                        <option value="<?= htmlspecialchars($e[0]) ?>" <?= $filterEntity === $e[0] ? 'selected' : '' ?>><?= htmlspecialchars($e[0]) ?></option>
                    <?php endwhile; ?>
                </select>
                <select name="action">
                    <option value="">All Actions</option>
                    <?php while ($a = $actionOptions->fetch_row()): ?>
                        <option value="<?= htmlspecialchars($a[0]) ?>" <?= $filterAction === $a[0] ? 'selected' : '' ?>><?= htmlspecialchars($a[0]) ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="user" placeholder="Filter by username..." value="<?= htmlspecialchars($filterUser) ?>">
                <button type="submit"><i class="fas fa-filter"></i> Filter</button>
                <a href="audit_log.php" style="padding:8px 12px;color:#666;text-decoration:none;"><i class="fas fa-times"></i> Clear</a>
            </form>
            <div style="margin-left:auto;font-size:13px;color:#666;">
                <?= number_format($totalRows) ?> total record(s)
            </div>
        </div>

        <?php if (count($logs) === 0): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list" style="font-size:40px;opacity:0.3;margin-bottom:10px;display:block;"></i>
                No audit log entries found.
            </div>
        <?php else: ?>
            <table class="log-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>Username</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Entity ID</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $badgeClass = match($log['action']) {
                            'create' => 'badge-create',
                            'update' => 'badge-update',
                            'delete' => 'badge-delete',
                            'login' => 'badge-login',
                            default => 'badge-create'
                        };
                        ?>
                        <tr>
                            <td><?= (int)$log['id'] ?></td>
                            <td><?= htmlspecialchars($log['created_at']) ?></td>
                            <td><?= htmlspecialchars($log['username']) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($log['action']) ?></span></td>
                            <td><?= htmlspecialchars($log['entity']) ?></td>
                            <td><?= $log['entity_id'] ? (int)$log['entity_id'] : '-' ?></td>
                            <td><?= htmlspecialchars($log['details'] ?? '') ?></td>
                            <td><?= htmlspecialchars($log['ip_address'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&entity=<?= urlencode($filterEntity) ?>&action=<?= urlencode($filterAction) ?>&user=<?= urlencode($filterUser) ?>">&laquo; Prev</a>
                    <?php endif; ?>
                    <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                        <?= $p === $page
                            ? '<span class="current">' . $p . '</span>'
                            : '<a href="?page=' . $p . '&entity=' . urlencode($filterEntity) . '&action=' . urlencode($filterAction) . '&user=' . urlencode($filterUser) . '">' . $p . '</a>'
                        ?>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&entity=<?= urlencode($filterEntity) ?>&action=<?= urlencode($filterAction) ?>&user=<?= urlencode($filterUser) ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
