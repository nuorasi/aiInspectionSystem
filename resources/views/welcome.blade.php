<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white min-h-screen flex flex-col">

<!-- Top Navigation -->
<nav class="bg-[#262626] text-white w-full shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="font-semibold text-lg">
            <a href="/">
                <img src="/images/victaulicLogo_200.png"
                     alt="Home"
                     style="width:135px;">
            </a>
        </div>
        <ul class="flex gap-6 text-sm">
            <li><a href="login" class="hover:underline">Login</a></li>
            <li><a href="register" class="hover:underline">Register</a></li>
        </ul>
    </div>
</nav>

<!-- Main Section (Black Background) -->
<main class="flex-grow flex items-center justify-center bg-black">
    <div class="flex flex-col items-center text-center">
        <img src="/images/victaulicLogo.png"
             alt="Centered Image"
             class="w-[764px] h-auto mb-6 md:mb-10">

        <p class="text-white text-[50px] leading-tight italic">
            AI Image Inspection System
        </p>
    </div>
</main>

</body>
</html>
