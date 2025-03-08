<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="social-icons">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
        <p class="copyright">&copy; <?php echo date('Y'); ?> FLOAT SMOOTHIES MEDAN. All Rights Reserved.</p>
    </div>
</footer>

<style>
    /* Footer */
    .footer {
        background: linear-gradient(135deg, #ff69b4, #ff1493); /* Warna pink gradient */
        color: white;
        text-align: center;
        padding: 20px 0; /* Padding diperkecil */
        margin-top: 30px; /* Margin atas diperkecil */
        position: relative;
        overflow: hidden;
    }
    
    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: -50%;
        width: 200%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(-5deg);
        z-index: 1;
    }
    
    .container {
        position: relative;
        z-index: 2;
    }
    
    .social-icons {
        margin: 10px 0; /* Margin ikon sosial diperkecil */
    }
    
    .social-icons a {
        color: white;
        margin: 0 10px; /* Jarak antar ikon diperkecil */
        font-size: 1.2em; /* Ukuran ikon diperkecil */
        transition: transform 0.3s ease, color 0.3s ease;
    }
    
    .social-icons a:hover {
        color: #ffeb3b; /* Warna hover kuning untuk kontras */
        transform: translateY(-3px); /* Efek hover lebih kecil */
    }
    
    .copyright {
        font-size: 0.8em; /* Ukuran teks copyright diperkecil */
        color: #fff; /* Warna teks lebih terang */
        margin-top: 5px; /* Margin atas teks copyright diperkecil */
    }
    
    /* Animasi */
    @keyframes float {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px); /* Animasi lebih kecil */
        }
    }
    
    .social-icons a {
        animation: float 4s ease-in-out infinite;
    }
    
    .social-icons a:nth-child(2) {
        animation-delay: 0.5s;
    }
    
    .social-icons a:nth-child(3) {
        animation-delay: 1s;
    }
</style>
</body>
</html>