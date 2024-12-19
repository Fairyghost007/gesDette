<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../css/output.css" rel="stylesheet">
    <title>Document</title>
</head>
<body class="bg-green-500 flex justify-center items-center m-0 ">
    <!-- Navbar -->
    <nav class="bg-gray-800 p-4 w-full">
        <div class="container mx-auto flex items-center justify-between">
            <a href="/" class="text-white text-2xl font-bold">MyApp</a>
            <div>
                <a href="/" class="text-white px-4">Home</a>
                <a href="/services" class="text-white px-4">Our Services</a>
                <a href="/contact" class="text-white px-4">Contact Us</a>
            </div>
        </div>
    </nav>  

    <!-- Main content section -->
    <div class="container mx-auto py-8 w-full">
        <?php echo $content; // This is where the page content will be injected ?>
    </div>
</body>
</html>
