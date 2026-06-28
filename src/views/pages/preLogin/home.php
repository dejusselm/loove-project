<?php
include ROOT_PATH . 'views/components/head.php';
?>
<title>Loove App</title>
<link href="/views/style/preLogin.css" rel="stylesheet">
</head>

<body>
    <main>
        <section id="index">
            <div class="carousel" id="carousel">

                <article class="carousel-item active" data-index="0">
                    <img src="/views/images/person.jpg" alt="Community">
                    <h3>Authentic</h3>
                    <p>A curated community of people who actually want to talk, listen, and build something real.</p>
                </article>

                <article class="carousel-item" data-index="1">
                    <img src="/views/images/second_person.jpg" alt="Quality">
                    <h3>Quality</h3>
                    <p>Quality over quantity. We keep our community respectful so you can focus on meaningful
                        connections.</p>
                </article>

                <article class="carousel-item" data-index="2">
                    <img src="/views/images/third_person.jpg" alt="Benevolent">
                    <h3>Benevolent</h3>
                    <p>Just direct, honest interactions with real people on your exact wavelength.</p>
                </article>
            </div>

            <div class="carousel-dots" id="carouselDots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>

            <div class="actions-group">
                <button onclick="location.href='/preLogin/register';">Create an account</button>
                <p>Already have an account? <a href='/preLogin/login'>Log in</a></p>
            </div>

        </section>
    </main>

    <script src="/views/scripts/carousel.js"></script>
</body>

</html>