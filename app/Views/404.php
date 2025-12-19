<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Page Not Found</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    height: 100vh;
    background: linear-gradient(135deg, #1e1e2d, #2b2b40);
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
}

.error-container {
    text-align: center;
    background: #24243a;
    padding: 50px 70px;
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.4);
}

.error-container h1 {
    font-size: 96px;
    color: #ff6b6b;
    margin-bottom: 10px;
}

.error-container h2 {
    font-size: 28px;
    margin-bottom: 15px;
}

.error-container p {
    font-size: 16px;
    opacity: 0.85;
    margin-bottom: 30px;
}

.home-btn {
    display: inline-block;
    padding: 12px 28px;
    background: #ff6b6b;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: 0.3s;
}

.home-btn:hover {
    background: #ff4b4b;
}

        </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The page you are looking for does not exist</p>

        <a href="/user-home" class="home-btn">Go Home</a>
    </div>
</body>
</html>
