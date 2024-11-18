<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <section class="contact">
        <h2>Contact</h2>
        <form action="/submit" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
            <input type="submit" value="Submit">
            <div id="error-message" style="color: red;"></div>
            <div id="success-message" style="color: green;"></div>
            <div id="loading-spinner" style="display: none; text-align: center;">
                <img src="loading.gif" alt="Loading...">
            </div>
            <script src="script.js"></script>
            <!-- Additional JavaScript code for form validation and AJAX request -->
    </section>
</body>
</html>