// scripts.js

function updateTier() {
    // Get the current points (could be fetched from the backend)
    const currentPoints = parseInt(document.getElementById('current-points').textContent);

    // Define the tiers and their thresholds
    const tiers = [
        { name: "Bronze", minPoints: 0, maxPoints: 499, reward: "5% Discount" },
        { name: "Silver", minPoints: 500, maxPoints: 999, reward: "10% Discount" },
        { name: "Gold", minPoints: 1000, maxPoints: 1999, reward: "15% Discount" },
        { name: "Platinum", minPoints: 2000, maxPoints: Infinity, reward: "20% Discount" }
    ];

    // Find the current tier based on the points
    let currentTier = "Bronze";
    let reward = "5% Discount";

    for (let i = 0; i < tiers.length; i++) {
        if (currentPoints >= tiers[i].minPoints && currentPoints <= tiers[i].maxPoints) {
            currentTier = tiers[i].name;
            reward = tiers[i].reward;
            break;
        }
    }

    // Update the current tier and reward on the page
    document.getElementById('current-tier').textContent = currentTier;
    document.getElementById('reward-text').textContent = `You are eligible for: ${reward}`;
}
