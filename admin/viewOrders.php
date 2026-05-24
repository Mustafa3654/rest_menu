<?php
include "../includes/connection.php";
include "../includes/auth.php";
start_secure_session();
require_admin('../login');
check_session_timeout(30);
$csrfToken = ensure_csrf_token();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = "";
$params = [];
$types = "";

if ($search !== '') {
    $where .= " AND (customer_name LIKE ? OR whatsapp_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE 1=1" . $where);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalOrders = (int)$countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
$totalPages = max(1, (int)ceil($totalOrders / $perPage));
if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;

$sql = "SELECT * FROM orders WHERE 1=1" . $where . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$stmt->close();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['new_status'];
    $allowed = ['pending', 'sent', 'failed'];
    if (in_array($new_status, $allowed)) {
        if ($new_status === 'sent') {
            $upd = $conn->prepare("UPDATE orders SET status = ?, completed_at = NOW() WHERE id = ?");
        } else {
            $upd = $conn->prepare("UPDATE orders SET status = ?, completed_at = NULL WHERE id = ?");
        }
        $upd->bind_param("si", $new_status, $order_id);
        $upd->execute();
        $upd->close();
        header("Location: viewOrders?updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.8">
    <title>View Orders</title>
    <link rel="stylesheet" href="../assets/css/view.css?v=1.1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../assets/css/admin-shared.css?v=1.1">
</head>
<body>
    <div class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Orders</h1>
            <a href="dashboard" class="back-btn"><i class="fas fa-arrow-left"></i> BACK</a>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Order status updated.</div>
        <?php endif; ?>

        <div class="controls">
            <div class="search-box">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
        </div>

        <?php if (count($rows) > 0): ?>
            <div class="item-list">
                <div class="order-row order-header">
                    <span>ID</span>
                    <span>Customer</span>
                    <span>Phone</span>
                    <span>Total</span>
                    <span>Status</span>
                    <span>Ordered</span>
                    <span>Completed</span>
                    <span>Action</span>
                </div>

                <?php foreach ($rows as $r):
                    $total = $r['total_usd'] > 0 ? '$' . number_format($r['total_usd'], 2) : '-';
                ?>
                    <div class="order-row">
                        <span><?php echo (int)$r['id']; ?></span>
                        <span><?php echo htmlspecialchars($r['customer_name'] ?? ''); ?></span>
                        <span><?php echo htmlspecialchars($r['whatsapp_number'] ?? ''); ?></span>
                        <span><?php echo $total; ?></span>
                        <span>
                            <span class="status-badge status-<?php echo htmlspecialchars($r['status'] ?? 'pending'); ?>">
                                <?php echo htmlspecialchars($r['status'] ?? 'pending'); ?>
                            </span>
                        </span>
                        <span><?php echo $r['created_at'] ? date('M j, g:ia', strtotime($r['created_at'])) : '-'; ?></span>
                        <span><?php echo $r['completed_at'] ? date('M j, g:ia', strtotime($r['completed_at'])) : '-'; ?></span>
                        <span>
                            <form method="POST" class="status-form">
                                <input type="hidden" name="order_id" value="<?php echo (int)$r['id']; ?>">
                                <select name="new_status" class="status-select">
                                    <option value="pending" <?php echo $r['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="sent" <?php echo $r['status'] === 'sent' ? 'selected' : ''; ?>>Sent</option>
                                    <option value="failed" <?php echo $r['status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                </select>
                                <button type="submit" name="update_status" class="update-btn"><i class="fas fa-check"></i></button>
                            </form>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php
                $qp = [];
                if ($search !== '') $qp['search'] = $search;
                if ($page > 1):
                    $qp['page'] = $page - 1;
                    echo '<a href="viewOrders?' . http_build_query($qp) . '" class="page-link">&laquo; Prev</a>';
                endif;
                for ($i = 1; $i <= $totalPages; $i++):
                    $qp['page'] = $i;
                    $active = $i === $page ? ' class="page-link active"' : ' class="page-link"';
                    echo '<a href="viewOrders?' . http_build_query($qp) . '"' . $active . '>' . $i . '</a>';
                endfor;
                if ($page < $totalPages):
                    $qp['page'] = $page + 1;
                    echo '<a href="viewOrders?' . http_build_query($qp) . '" class="page-link">Next &raquo;</a>';
                endif;
                echo '<span class="page-info">' . $totalOrders . ' total orders</span>';
                ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info" style="text-align:center; padding:20px;">No orders found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
