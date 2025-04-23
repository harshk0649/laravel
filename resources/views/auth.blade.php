<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Authentication</title>
</head>
<body class="bg-white text-gray-800">
    <div class="container mx-auto p-6">
        <h1 class="text-center text-3xl font-bold mb-4">Authentication</h1>
        <div class="text-center mb-6">
            <p class="text-lg">Welcome! Please choose an option below to either log in or create a new account.</p>
            <p class="text-sm text-gray-600">If you already have an account, click "Go to Login". If you're new, click "Go to Register".</p>
        </div>
        <div class="flex justify-center space-x-10">
            <div class="w-1/3">
                <h2 class="text-center text-2xl font-bold mt-6">Login</h2>
                <a href="{{ route('login') }}" class="block text-blue-600 hover:underline text-center" title="Click here to log in to your account">Go to Login</a>
            </div>
            <div class="w-1/3">
                <h2 class="text-center text-2xl font-bold mt-6">Register</h2>
                <a href="{{ route('register') }}" class="block text-blue-600 hover:underline text-center" title="Click here to create a new account">Go to Register</a>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
