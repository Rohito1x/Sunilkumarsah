<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die("Unauthorized access.");
}

$type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'enrollments';
$allowedTypes = ['enrollments', 'inquiries', 'subscribers'];

if (!in_array($type, $allowedTypes)) {
    http_response_code(400);
    die("Invalid export type.");
}

// Helpers
if (!function_exists('h')) {
    function h($s) {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

// Filters (passed from dashboard)
$search = isset($_GET['search']) ? trim((string)$_GET['search']) : '';
$status = isset($_GET['status']) ? trim((string)$_GET['status']) : '';
$level = isset($_GET['level']) ? trim((string)$_GET['level']) : '';
$faculty = isset($_GET['faculty']) ? trim((string)$_GET['faculty']) : '';
$city = isset($_GET['city']) ? trim((string)$_GET['city']) : '';
$country = isset($_GET['country']) ? trim((string)$_GET['country']) : '';

try {
    $sql = "";
    $params = [];
    
    if ($type === 'enrollments') {
        $sql = "SELECT * FROM enrollments WHERE 1=1";
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        if ($level !== '') {
            $sql .= " AND level = ?";
            $params[] = $level;
        }
        if ($faculty !== '') {
            $sql .= " AND faculty = ?";
            $params[] = $faculty;
        }
        if ($city !== '') {
            $sql .= " AND interested_city = ?";
            $params[] = $city;
        }
        if ($search !== '') {
            $sql .= " AND (full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY created_at DESC";
        
    } elseif ($type === 'inquiries') {
        $sql = "SELECT * FROM inquiries WHERE 1=1";
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        if ($country !== '') {
            $sql .= " AND country = ?";
            $params[] = $country;
        }
        if ($search !== '') {
            $sql .= " AND (full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY created_at DESC";
        
    } else { // subscribers
        $sql = "SELECT * FROM newsletter_subscribers WHERE 1=1";
        if ($search !== '') {
            $sql .= " AND email LIKE ?";
            $params[] = "%$search%";
        }
        $sql .= " ORDER BY created_at DESC";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error during PDF generation: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report - <?= ucfirst($type) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background-color: #fff;
            padding: 20px;
        }
        .header-title {
            color: #102e48;
            font-weight: 700;
            border-bottom: 2px solid #e56920;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8fafc !important;
            color: #102e48;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            vertical-align: middle;
        }
        .status-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fef3c7; color: #d97706; }
        .status-review { background-color: #e0f2fe; color: #0284c7; }
        .status-contacted { background-color: #ffedd5; color: #ea580c; }
        .status-admitted { background-color: #dcfce7; color: #16a34a; }
        .status-cancelled { background-color: #fee2e2; color: #dc2626; }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <span class="text-muted">Review the layout below. Use <strong>Ctrl + P</strong> or click print to save as PDF.</span>
        </div>
        <button class="btn btn-primary btn-sm rounded-pill px-4" onclick="window.print()"><i class="fa-solid fa-print me-1"></i> Print / Save as PDF</button>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-8">
            <h2 class="header-title mb-1">Krishna Education Foundation Bureau</h2>
            <h5 class="text-muted">Student Leads & Management Report: <?= ucfirst(h($type)) ?></h5>
        </div>
        <div class="col-4 text-end">
            <span class="small text-muted d-block">Report Generated:</span>
            <span class="fw-bold"><?= date('F j, Y, g:i a') ?></span>
        </div>
    </div>

    <?php if (empty($records)): ?>
        <div class="alert alert-info">No records matched the filter parameters.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <?php if ($type === 'enrollments'): ?>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 20%">Student Name</th>
                        <th style="width: 25%">Contact Details</th>
                        <th style="width: 20%">Prior Academics</th>
                        <th style="width: 20%">Faculty & City Preference</th>
                        <th style="width: 10%">Status</th>
                    </tr>
                <?php elseif ($type === 'inquiries'): ?>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 25%">Inquirer Name</th>
                        <th style="width: 30%">Contact Details</th>
                        <th style="width: 15%">Country / Course</th>
                        <th style="width: 15%">Submitted</th>
                        <th style="width: 10%">Status</th>
                    </tr>
                <?php else: ?>
                    <tr>
                        <th style="width: 10%">ID</th>
                        <th style="width: 60%">Email Address</th>
                        <th style="width: 30%">Subscription Date</th>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <?php if ($type === 'enrollments'): 
                        $statusClass = strtolower(str_replace(' ', '-', $row['status'] ?? 'pending'));
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><strong class="text-dark"><?= h($row['full_name']) ?></strong></td>
                            <td>
                                <div><span class="text-muted">Email:</span> <?= h($row['email']) ?></div>
                                <div><span class="text-muted">Phone:</span> <?= h($row['phone_number']) ?></div>
                                <div><span class="text-muted">Addr:</span> <?= h($row['address']) ?></div>
                            </td>
                            <td>
                                <div><span class="text-muted">Level:</span> <?= h($row['highest_education']) ?></div>
                                <div><span class="text-muted">Score:</span> <?= h($row['percentage']) ?></div>
                            </td>
                            <td>
                                <div><span class="text-muted">Faculty:</span> <?= h($row['faculty']) ?></div>
                                <div><span class="text-muted">Colg:</span> <?= h($row['interested_college']) ?></div>
                                <div><span class="text-muted">City:</span> <?= h($row['interested_city']) ?></div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $statusClass ?>">
                                    <?= h($row['status'] ?? 'Pending') ?>
                                </span>
                            </td>
                        </tr>
                    <?php elseif ($type === 'inquiries'): 
                        $statusClass = strtolower(str_replace(' ', '-', $row['status'] ?? 'pending'));
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><strong class="text-dark"><?= h($row['full_name']) ?></strong></td>
                            <td>
                                <div><span class="text-muted">Email:</span> <?= h($row['email']) ?></div>
                                <div><span class="text-muted">Phone:</span> <?= h($row['phone_number']) ?></div>
                                <?php if (!empty($row['address'])): ?>
                                    <div><span class="text-muted">Addr:</span> <?= h($row['address']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><span class="text-muted">Country:</span> <?= h($row['country']) ?></div>
                                <div><span class="text-muted">Course:</span> <?= h($row['course']) ?></div>
                            </td>
                            <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= $statusClass ?>">
                                    <?= h($row['status'] ?? 'Pending') ?>
                                </span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><strong><?= h($row['email']) ?></strong></td>
                            <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
