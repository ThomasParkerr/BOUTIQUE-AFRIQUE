<?php
session_start();
include '../../db/database.php'; // Adjust the path to your database connection file

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch admin role based on admin_id
$admin_id = $_SESSION['admin_id'];
try {
    $stmt = $pdo->prepare("SELECT role FROM AfriqueBotique_Admin WHERE admin_id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch();

    if (!$admin) {
        // Admin ID not found in the database
        header('Location: ../login.php');
        exit();
    }

    $role = $admin['role'];

    // Check if the admin has sufficient privileges
    if ($role !== 'SuperAdmin' && $role !== 'admin') {
        // Redirect unauthorized users
        header('Location: ../login.php');
        exit();
    }

    // Fetch data for the dashboard
    $stmt = $pdo->query("SELECT COUNT(*) AS active_sales FROM AfriqueBotique_Sales WHERE end_date > NOW()");
    $activeSales = $stmt->fetch()['active_sales'];

    $stmt = $pdo->query("SELECT COUNT(*) AS total_purchases FROM AfriqueBotique_Cart WHERE checkout = 'Yes'");
    $totalPurchases = $stmt->fetch()['total_purchases'];

    $stmt = $pdo->query("
        SELECT 
            c.cart_id, 
            u.username, 
            p.name AS product_name, 
            c.date_added
        FROM AfriqueBotique_Cart c
        JOIN AfriqueBotique_Users u ON c.user_id = u.user_id
        JOIN AfriqueBotique_Products p ON c.product_id = p.id
        WHERE c.checkout = 'Yes'
        ORDER BY c.date_added DESC
        LIMIT 10
    ");
    $recentPurchases = $stmt->fetchAll();

    $stmt = $pdo->query("SELECT COUNT(*) AS total_products FROM AfriqueBotique_Products");
    $totalProducts = $stmt->fetch()['total_products'];

    $stmt = $pdo->query("
        SELECT 
            product_type, 
            item_type, 
            COUNT(*) AS type_count 
        FROM AfriqueBotique_Products 
        GROUP BY product_type, item_type
    ");
    $productTypes = $stmt->fetchAll();

    $stmt = $pdo->query("
        SELECT 
            tier, 
            COUNT(*) AS tier_count, 
            AVG(points) AS avg_points 
        FROM afriquebotique_loyaltypoints 
        GROUP BY tier
    ");
    $loyaltyTiers = $stmt->fetchAll();

    if (isset($_GET['remove_sale']) && is_numeric($_GET['remove_sale'])) {
        $sale_id = $_GET['remove_sale'];
        $stmt = $pdo->prepare("DELETE FROM AfriqueBotique_Sales WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        header("Location: dashboard.php?status=sale_removed");
        exit();
    }

    if (isset($_GET['remove_product']) && is_numeric($_GET['remove_product'])) {
        $product_id = $_GET['remove_product'];
        $stmt = $pdo->prepare("DELETE FROM AfriqueBotique_Products WHERE id = ?");
        $stmt->execute([$product_id]);
        header("Location: dashboard.php?status=product_removed");
        exit();
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AfriqueBotique Admin Dashboard</title>
    <link rel="icon" href="../../logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            background-image:url("../../assets/images/cool.jpg");
            background-size: cover; /* Ensures the image covers the entire screen */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat;
        }

        h1 {
            text-align: center;
            background: #333;
            color: white;
            margin: 0;
            padding: 1em 0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px;
            padding: 20px;
        }

        .box {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>AfriqueBotique Admin Dashboard</h1>
    <div style="text-align: center; margin: 20px;">
    <a href="logout.php" style="font-size: 18px; font-weight: bold; color: White; text-decoration: none;">Logout</a>
</div>
    

    <?php if (isset($_GET['status'])): ?>
        <div class="status-message">
            <?php 
            switch($_GET['status']) {
                case 'sale_removed':
                    echo "Sale successfully removed.";
                    break;
                case 'product_removed':
                    echo "Product successfully removed.";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <div class="box">
            <h2>Sales & Purchases Overview</h2>
            <p><strong>Active Sales:</strong> <?= $activeSales ?></p>
            <p><strong>Total Purchases:</strong> <?= $totalPurchases ?></p>
            <p><strong>Total Products:</strong> <?= $totalProducts ?></p>
        </div>

        <?php if ($role === 'SuperAdmin'): ?>
        <div class="box">
            <h2>Loyalty Tiers</h2>
            <table>
                <tr>
                    <th>Tier</th>
                    <th>Users</th>
                    <th>Avg Points</th>
                </tr>
                <?php foreach ($loyaltyTiers as $tier): ?>
                <tr>
                    <td><?= htmlspecialchars($tier['tier']) ?></td>
                    <td><?= $tier['tier_count'] ?></td>
                    <td><?= number_format($tier['avg_points'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

        <div class="box">
            <h2>Product Types</h2>
            <table>
                <tr>
                    <th>Product Type</th>
                    <th>Item Type</th>
                    <th>Count</th>
                </tr>
                <?php foreach ($productTypes as $type): ?>
                <tr>
                    <td><?= htmlspecialchars($type['product_type']) ?></td>
                    <td><?= htmlspecialchars($type['item_type']) ?></td>
                    <td><?= $type['type_count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="box">
            <h2>Recent Purchases</h2>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Product</th>
                    <th>Purchase Date</th>
                </tr>
                <?php foreach ($recentPurchases as $purchase): ?>
                <tr>
                    <td><?= htmlspecialchars($purchase['username']) ?></td>
                    <td><?= htmlspecialchars($purchase['product_name']) ?></td>
                    <td><?= $purchase['date_added'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
