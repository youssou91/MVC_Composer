<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <section class="contact py-8">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold text-blue-600 mb-4 text-center">Contactez nous !</h2>
            <form action="/submit" method="post" class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-1">Name:</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-indigo-300"
                        placeholder="Your Name">
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-1">Email:</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-indigo-300"
                        placeholder="Your Email">
                </div>
                <!-- Message -->
                <div>
                    <label for="message" class="block text-gray-700 font-medium mb-1">Message:</label>
                    <textarea 
                        id="message" 
                        name="message" 
                        required 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-indigo-300"
                        rows="5" 
                        placeholder="Your Message"></textarea>
                </div>
                <!-- Submit Button -->
                <div class="text-center">
                    <button 
                        type="submit" 
                        class="bg-indigo-500 text-white px-6 py-2 rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring focus:ring-indigo-300">
                        Submit
                    </button>
                </div>
            </form>
            <!-- Error and Success Messages -->
            <div id="error-message" class="text-red-500 text-sm mt-2 hidden"></div>
            <div id="success-message" class="text-green-500 text-sm mt-2 hidden"></div>
            <!-- Loading Spinner -->
            <div id="loading-spinner" class="flex justify-center mt-4 hidden">
                <img src="loading.gif" alt="Loading..." class="h-8 w-8">
            </div>
        </div>
    </section>
    <script src="script.js"></script>
</body>
</html>
