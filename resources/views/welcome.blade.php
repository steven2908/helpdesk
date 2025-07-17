<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jaringan Pintar Nusantara</title>
    <style>
        body {
    margin: 0;
    padding: 0;
    background: url('{{ asset('images/background.jpg') }}') no-repeat center center/cover;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: white;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1;
}

.content-wrapper {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 2rem 1rem;
}

.content {
    max-width: 600px;
    width: 100%;
}

h1 {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 1rem;
    line-height: 1.3;
}

p {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-primary,
.btn-dark {
    padding: 0.75rem 1.5rem;
    border-radius: 9999px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: background 0.3s;
    display: inline-block;
}

.btn-primary {
    background-color: #3b82f6;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #2563eb;
}

.btn-dark {
    background-color: rgba(0,0,0,0.5);
    color: white;
    border: 1px solid #ccc;
}

.btn-dark:hover {
    background-color: rgba(0,0,0,0.7);
}

.logo-main {
    max-width: 100%;
    height: auto;
    margin-bottom: 1rem;
}

/* Footer Styling */
footer {
    background: rgba(0, 0, 0, 0.8);
    color: #ccc;
    text-align: center;
    padding: 1rem 0;
    z-index: 2;
    position: relative;
    font-size: 0.875rem;
}

.footer-instagram {
    margin-top: 0.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.footer-instagram img {
    width: 22px;
    height: 22px;
    vertical-align: middle;
}

.footer-instagram a {
    color: #ccc;
    text-decoration: underline;
}

/* Responsive tweaks */
@media (min-width: 768px) {
    h1 {
        font-size: 2.5rem;
    }

    p {
        font-size: 1.125rem;
    }

    .btn-primary,
    .btn-dark {
        font-size: 1.125rem;
        padding: 0.75rem 2rem;
    }
}

    </style>
</head>
<body>

    <div class="overlay"></div>

    <div class="content-wrapper">
        <div class="content">
            <!-- Logo Utama -->
            <img src="{{ asset('images/JARINGAN_PINTAR_NUSANTARA__1_-removebg-preview.png') }}" alt="Logo" class="logo-main">

            <h1>Platform Konsultasi - <br>Jaringan Pintar Nusantara</h1>
            <p>Jaringan Pintar Nusantara hadir untuk membantu Anda menyelesaikan berbagai permasalahan teknologi, langsung dari tim berpengalaman.</p>
            
            <div class="buttons">
                <a href="{{ route('register') }}" class="btn-primary">Mulai Diskusi!</a>
                <a href="{{ route('login') }}" class="btn-dark">Login</a>
            </div>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} Jaringan Pintar Nusantara. All rights reserved.
        <div class="footer-instagram">
            <img src="{{ asset('images/icons8-instagram-logo-94.png') }}" alt="Instagram Icon">
            <a href="https://www.instagram.com/jp_nusantara" target="_blank">@jp_nusantara</a>
        </div>
    </footer>

</body>
</html>
