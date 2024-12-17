<?php
// Start the session to access session data
session_start();

require_once "../db/database.php";

if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query to count completed checkouts
$sql = "SELECT COUNT(*) AS checkout_count FROM AfriqueBotique_Cart WHERE user_id = :user_id AND checkout = 'Yes'";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$completed_checkouts = $row['checkout_count'];
$user_points = $completed_checkouts * 20; // 20 points per checkout

// Determine the user's tier based on points
$user_tier = ($user_points >= 2000) ? 'Platinum' : (($user_points >= 1000) ? 'Gold' : (($user_points >= 500) ? 'Silver' : 'Bronze'));

// Update the points and tier in the afriquebotique_loyaltypoints table
$update_sql = "
    INSERT INTO afriquebotique_loyaltypoints (user_id, points, tier)
    VALUES (:user_id, :points, :tier)
    ON DUPLICATE KEY UPDATE 
        points = :points, 
        tier = :tier
";

$update_stmt = $pdo->prepare($update_sql);
$update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$update_stmt->bindParam(':points', $user_points, PDO::PARAM_INT);
$update_stmt->bindParam(':tier', $user_tier, PDO::PARAM_STR);
$update_stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique Loyalty</title>
    <link rel="stylesheet" href="../assets/css/NewLoyalty.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
</head>
<body>
    <div class="container">

    <div class="back-btn-wrap">
            <a href="useraccount.php" class="back-btn">Back to Account</a>
        </div>
        
        <h1>Loyalty Points</h1>
        <p>Earn points and reach new tiers to unlock exclusive rewards!</p>

        <!-- Loyalty Points and Tier Info -->
        <div class="loyalty-info">
            <p><strong>Current Points:</strong> <span id="current-points"><?= number_format($user_points); ?></span></p>
            <p><strong>Current Tier:</strong> <span id="current-tier"><?= htmlspecialchars($user_tier); ?></span></p>
        </div>

        <!-- Tier System -->
        <h2>Your Loyalty Tiers</h2>
        <div class="tiers">
            <div class="tier">
                <h3>Bronze</h3>
                <p>Points needed: 0 - 499</p>
                <p>Reward: 5% Discount</p>
            </div>
            <div class="tier">
                <h3>Silver</h3>
                <p>Points needed: 500 - 999</p>
                <p>Reward: 10% Discount</p>
            </div>
            <div class="tier">
                <h3>Gold</h3>
                <p>Points needed: 1000 - 1999</p>
                <p>Reward: 15% Discount</p>
            </div>
            <div class="tier">
                <h3>Platinum</h3>
                <p>Points needed: 2000+</p>
                <p>Reward: 20% Discount</p>
            </div>
        </div>

        <!-- Rewards Info -->
        <div class="rewards">
            <h2>Your Reward</h2>
            <p id="reward-text">
                <?php
                if ($user_points >= 2000) {
                    echo 'You are eligible for: 20% Discount (Platinum)';
                } elseif ($user_points >= 1000) {
                    echo 'You are eligible for: 15% Discount (Gold)';
                } elseif ($user_points >= 500) {
                    echo 'You are eligible for: 10% Discount (Silver)';
                } else {
                    echo 'You are eligible for: 5% Discount (Bronze)';
                }
                ?>
            </p>
        </div>
    </div>

    <script>
    // Function to update user's tier
    function updateTier() {
        const currentPoints = <?= $user_points; ?>;
        let tier = 'Bronze'; // Default tier

        if (currentPoints >= 2000) {
            tier = 'Platinum';
        } else if (currentPoints >= 1000) {
            tier = 'Gold';
        } else if (currentPoints >= 500) {
            tier = 'Silver';
        }

        // Update the tier in the page dynamically
        document.getElementById('current-tier').textContent = tier;
        document.getElementById('reward-text').textContent = `You are eligible for: ${tier === 'Platinum' ? '20%' : tier === 'Gold' ? '15%' : tier === 'Silver' ? '10%' : '5%'} Discount (${tier})`;
    }
    </script>
</body>
</html>
