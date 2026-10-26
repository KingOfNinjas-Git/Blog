@php
    $shareText = 'A blog built with Laravel: browse articles by category, search by title or author, like, comment and publish your own. A portfolio demo by Francisco Cavaco.';
    $shareImage = url('/blog-share.jpg?v=1');
@endphp
<meta name="description" content="{{ $shareText }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="My Blog">
<meta property="og:title" content="My Blog: articles, likes and comments built with Laravel">
<meta property="og:description" content="{{ $shareText }}">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ $shareImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="My Blog: article cards with pictures on a blue background">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="My Blog: articles, likes and comments built with Laravel">
<meta name="twitter:description" content="{{ $shareText }}">
<meta name="twitter:image" content="{{ $shareImage }}">
<link rel="icon" href="/favicon.ico?v=2" sizes="any">
<link rel="icon" type="image/png" sizes="192x192" href="/icon-192.png?v=2">
<link rel="apple-touch-icon" href="/apple-touch-icon.png?v=2">
