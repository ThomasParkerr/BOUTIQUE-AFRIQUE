<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BotiqueAfrique</title>
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
    body {
        font-family: 'Georgia', serif; /* Rustic font */
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        background-color: #f4f1e1; /* Soft beige background */
    }
    .message-box {
        text-align: center;
        padding: 30px;
        background-color: #ffffff;
        border: 1px solid #d2b38b; /* Soft brown border */
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); /* Slight shadow for depth */
        border-radius: 8px; /* Rounded corners for a softer look */
    }
    .message-box h1 {
        color: #2e8b57; /* Earthy green color */
        font-size: 36px; /* Larger heading */
        margin-bottom: 15px; /* Space under the heading */
    }
    .message-box p {
        font-size: 18px;
        color: #6b4f34; /* Warm brown text */
        margin-bottom: 20px; /* Add space under the paragraph */
    }
    .back-button {
        padding: 12px 25px;
        background-color: #8b4513; /* Rustic brown button */
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Soft shadow */
    }
    .back-button:hover {
        background-color: #5c3d26; /* Darker brown on hover */
    }
</style>

</head>
<body>
    <div class="message-box">
        <h1>No Payment Details Available</h1>
        <p>Set your payment details in your profile. In order to checkout your Purchases</p>
        <button class="back-button" onclick="window.location.href='homepage.php'">Back to Homepage</button>
    </div>
</body>
</html>
