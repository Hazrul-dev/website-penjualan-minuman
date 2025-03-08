<?php
session_start();
include 'includes/db.php';

// Cek jika ada notifikasi pembayaran
$has_notification = isset($_SESSION['has_notification']) ? $_SESSION['has_notification'] : false;
$notification_message = isset($_SESSION['notification_message']) ? $_SESSION['notification_message'] : '';

if (isset($_GET['order_success'])) {
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = "Pesanan Anda berhasil dibuat!";
}

include 'includes/header.php';

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Menu - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Gaya Umum */
        body {
            color: #333;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff0f5; /* Light pink background */
        }

        /* Background dengan blur */
        .background-image {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('images/kede.jpg') no-repeat center center/cover;
            filter: blur(5px);
            opacity: 0.7;
            z-index: -1;
        }

        /* Container untuk menu */
        .menu-container {
            padding: 2rem;
            max-width: 1400px; /* Increased max-width for 4x4 grid */
            margin: 0 auto;
        }

        /* Judul menu */
        .menu-title {
            text-align: center;
            font-size: 2.8rem;
            color: #ff1493; /* Deep pink */
            margin-bottom: 2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        /* Grid untuk menampilkan menu 4x4 */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 columns */
            gap: 1.5rem;
            padding: 1rem;
        }

        /* Kartu menu */
        .menu-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(255, 105, 180, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 2px solid #ffd1dc; /* Light pink border */
        }

        .menu-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 30px rgba(255, 105, 180, 0.25);
            border-color: #ff69b4; /* Change border color on hover */
        }

        /* Gambar menu */
        .menu-card-image {
            height: 180px; /* Slightly smaller for 4x4 grid */
            overflow: hidden;
        }

        .menu-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .menu-card:hover .menu-card-image img {
            transform: scale(1.05);
        }

        /* Detail menu */
        .menu-card-details {
            padding: 1.2rem;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu-card-details h2 {
            font-size: 1.3rem;
            color: #ff1493; /* Deep pink */
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .menu-card-details .description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.5;
            /* Ensure text doesn't get cut off */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 4.5em; /* Adjusted for 4x4 grid */
        }

        .menu-card-details .price-container {
            margin-bottom: 1.2rem;
        }

        .menu-card-details .price {
            font-size: 1.1rem;
            color: #ff69b4; /* Pink */
            margin-bottom: 0.4rem;
            font-weight: bold;
            display: inline-block;
            background-color: #fff0f5;
            padding: 4px 12px;
            border-radius: 15px;
        }

        /* Tombol Order */
        .btn {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background: linear-gradient(to right, #ff69b4, #ff1493);
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 8px rgba(255, 105, 180, 0.3);
        }

        .btn:hover {
            background: linear-gradient(to right, #ff1493, #ff69b4);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(255, 105, 180, 0.4);
        }

        /* Responsiveness */
        @media (max-width: 1200px) {
            .menu-grid {
                grid-template-columns: repeat(3, 1fr); /* 3 columns on medium screens */
            }
        }

        @media (max-width: 900px) {
            .menu-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columns on smaller screens */
            }
        }

        @media (max-width: 600px) {
            .menu-grid {
                grid-template-columns: 1fr; /* 1 column on mobile */
            }
            
            .menu-title {
                font-size: 2rem;
            }
            
            .menu-card-details {
                padding: 1.2rem;
            }
            
            .menu-card-details h2 {
                font-size: 1.3rem;
            }

            .menu-card-image {
                height: 200px; /* Larger image on mobile */
            }
        }

        /* Add some animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }
        /* More delay classes for a 4x4 grid */
        .animate-delay-5 { animation-delay: 0.5s; }
        .animate-delay-6 { animation-delay: 0.6s; }
        .animate-delay-7 { animation-delay: 0.7s; }
        .animate-delay-8 { animation-delay: 0.8s; }
        .animate-delay-9 { animation-delay: 0.9s; }
        .animate-delay-10 { animation-delay: 1.0s; }
        .animate-delay-11 { animation-delay: 1.1s; }
        .animate-delay-12 { animation-delay: 1.2s; }
        .animate-delay-13 { animation-delay: 1.3s; }
        .animate-delay-14 { animation-delay: 1.4s; }
        .animate-delay-15 { animation-delay: 1.5s; }
        .animate-delay-16 { animation-delay: 1.6s; }
    </style>
</head>
<body>
<div class="background-image"></div>

<div class="menu-container">
    <h1 class="menu-title animate__animated animate__fadeIn">Our Delicious Smoothies</h1>
    <div class="menu-grid">
        <?php 
        $delay = 1;
        while($row = $result->fetch_assoc()): 
        ?>
            <div class="menu-card animate__animated animate__fadeInUp animate-delay-<?php echo $delay; ?>">
                <div class="menu-card-image">
                    <img src="uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                </div>
                <div class="menu-card-details">
                    <div>
                        <h2><?php echo $row['name']; ?></h2>
                        <p class="description"><?php echo $row['description']; ?></p>
                    </div>
                    <div>
                        <div class="price-container">
                            <p class="price">M  : Rp <?php echo number_format($row['price_medium']); ?></p>
                            <p class="price">L  : Rp <?php echo number_format($row['price_large']); ?></p>
                        </div>
                        <a href="order.php?id=<?php echo $row['id']; ?>" class="btn">Order Now</a>
                    </div>
                </div>
            </div>
        <?php 
        $delay++;
        if($delay > 16) $delay = 1; // Reset delay after 16 items for a 4x4 grid
        endwhile; 
        ?>
    </div>
</div>

<script>
    function toggleNotification() {
        let panel = document.getElementById("notification-panel");
        panel.style.display = (panel.style.display === "block") ? "none" : "block";

        if (panel.style.display === "block") {
            setTimeout(() => {
                panel.style.display = 'none';
            }, 5000);
        }
    }

    function checkNotifications() {
        fetch("check_notifications.php")
            .then(response => response.json())
            .then(data => {
                let bell = document.querySelector(".notification-bell");
                let dot = document.querySelector(".notification-dot");

                if (data.has_notification) {
                    if (!dot) {
                        let newDot = document.createElement("span");
                        newDot.classList.add("notification-dot");
                        bell.appendChild(newDot);
                    }
                } else {
                    if (dot) dot.remove();
                }
            });
    }

    // Check for notifications on page load
    document.addEventListener('DOMContentLoaded', checkNotifications);
    
    // Set interval to check periodically
    setInterval(checkNotifications, 5000);
</script>

<?php include 'includes/footer.php'; ?>

</body>
</html>