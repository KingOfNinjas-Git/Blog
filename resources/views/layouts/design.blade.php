<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    @include('partials.share-head')
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap">
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Press+Start+2P&family=Silkscreen:wght@400;700&display=swap">
    </noscript>
    <style>
        .press-start-2p-regular {
            font-family: "Press Start 2P", system-ui;
            font-weight: 400;
            font-style: normal;
        }

        .silkscreen-regular {
            font-family: "Silkscreen", sans-serif;
            font-weight: 400;
            font-style: normal;
        }

        .silkscreen-bold {
            font-family: "Silkscreen", sans-serif;
            font-weight: 700;
            font-style: normal;
        }

        .byauthor {
            text-align: left;
        }

        .pixelify-sans-category {
            font-family: "Pixelify Sans", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        .article-title {
            display: flex;
            text-align: left;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-500 via-cyan-500 to-blue-700">
    @include('partials.demo-bar')
    <main id="main-content" class="container text-center mx-auto rounded-lg w-screen h-screen py-4">
        @yield('content')
    </main>
</body>

</html>
