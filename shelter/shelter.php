<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Shelters - Paws & Hearts</title>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- External CSS -->
    <link rel="stylesheet" href="index.css">
    <style>
        .shelters-hero {
            background: linear-gradient(rgba(249, 115, 22, 0.05), rgba(249, 115, 22, 0.02));
            padding: 3rem 0;
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .shelters-hero h1 {
            font-size: 2.5rem;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .shelters-hero p {
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* 5-Column Grid Layout */
        .shelters-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
            margin: 3rem 0;
        }

        /* Shelter Card Design */
        .shelter-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .shelter-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-light);
        }

        .shelter-card-pfp {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1.25rem;
            border: 4px solid #fff7ed;
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .shelter-card-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 1.25rem;
            height: 2.8rem;
            line-height: 1.4;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .shelter-card-stats {
            width: 100%;
            border-top: 1px solid #f1f5f9;
            padding-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card-stat-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .stat-badge {
            background: #fff7ed;
            color: var(--primary-dark);
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .rate-badge {
            background: #dcfce7;
            color: #166534;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .shelters-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .shelters-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .shelters-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .shelters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.html" class="logo"><i data-lucide="paw-print"></i><span>Paws & Hearts</span></a>
            <div class="nav-links">
                <a href="index.html">Browse Pets</a>
                <a href="dashboard__.html">Dashboard</a>
                <!-- <a href="shelters.html" class="active">Shelters</a> -->
                                <a href="messages.html">Message</a>

                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <span id="userNameDisplay " >Loyal Friends Rescue</span>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="shelters-hero">
        <div class="container">
            <h1>Shelter Partners</h1>
            <p>Connect with local rescue organizations dedicated to finding every pet a permanent home.</p>
        </div>
    </section>

    <!-- Main Grid -->
    <main class="container">
        <div class="shelters-grid" id="shelterGrid">
            <!-- Cards will be generated or hardcoded here -->
            <script>
                const shelters = [
                    { name: "Happy Tails Shelter", pets: 12, rate: "92%", img: "https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=200" },
                    { name: "Pawsitive Rescue", pets: 24, rate: "88%", img: "https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?w=200" },
                    { name: "Green Valley Sanctuary", pets: 18, rate: "95%", img: "https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=200" },
                    { name: "Second Chance Haven", pets: 31, rate: "84%", img: "https://images.unsplash.com/photo-1548191265-cc70d3d45ba1?w=200" },
                    { name: "The Cat Cottage", pets: 15, rate: "98%", img: "https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200" },
                    { name: "Loyal Friends Rescue", pets: 22, rate: "87%", img: "https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=200" },
                    { name: "Fur-ever Family Org", pets: 9, rate: "91%", img: "https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=200" },
                    { name: "Tiny Paws Foundation", pets: 14, rate: "89%", img: "https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=200" },
                    { name: "Bark & Purr Shelter", pets: 27, rate: "82%", img: "https://images.unsplash.com/photo-1444212477490-ca407925329e?w=200" },
                    { name: "Safe Haven Animals", pets: 11, rate: "96%", img: "https://images.unsplash.com/photo-1544568100-847a948585b9?w=200" }, { name: "Green Valley Sanctuary", pets: 18, rate: "95%", img: "https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=200" },
                    { name: "Second Chance Haven", pets: 31, rate: "84%", img: "https://images.unsplash.com/photo-1548191265-cc70d3d45ba1?w=200" },
                    { name: "The Cat Cottage", pets: 15, rate: "98%", img: "https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=200" },
                    { name: "Loyal Friends Rescue", pets: 22, rate: "87%", img: "https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?w=200" },
                ];

                const grid = document.getElementById('shelterGrid');
                shelters.forEach(s => {
                    grid.innerHTML += `
                        <div class="shelter-card" onclick="location.href='shelterprofile.html'">
                            <img src="${s.img}" class="shelter-card-pfp" alt="Logo">
                            <h3 class="shelter-card-name">${s.name}</h3>
                            <div class="shelter-card-stats">
                                <div class="card-stat-line">
                                    <span>Total Available</span>
                                    <span class="stat-badge">${s.pets}</span>
                                </div>
                                <div class="card-stat-line">
                                    <span>Adoption Rate</span>
                                    <span class="rate-badge">${s.rate}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
            </script>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">
                        <i data-lucide="paw-print"></i>
                        <span>Paws & Hearts</span>
                    </div>
                    <p>Connecting loving families with pets in need. Our mission is to ensure every animal finds their
                        forever home.</p>
                </div>
                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#">Browse All Pets</a></li>
                        <li><a href="#">Adoption Process</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#">Pet Care Tips</a></li>
                        <li><a href="#">Vaccination Guide</a></li>
                        <li><a href="#">Success Stories</a></li>
                        <li><a href="#">Volunteer</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Connect With Us</h4>
                    <p style="margin-bottom: 1rem; font-size: 0.85rem;">Follow our social media for daily pet updates!
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-btn"><i data-lucide="facebook" size="18"></i></a>
                        <a href="#" class="social-btn"><i data-lucide="instagram" size="18"></i></a>
                        <a href="#" class="social-btn"><i data-lucide="twitter" size="18"></i></a>
                        <a href="#" class="social-btn"><i data-lucide="mail" size="18"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Paws & Hearts Adoption. All rights reserved. Made with ❤️ for animals.</p>
            </div>
        </div>
    </footer>

    <script>
        localStorage.clear();
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "Loyal Friends Rescue";
            lucide.createIcons();
        });

        function logout() {
            localStorage.clear();
            window.location.href = "login__.html";
        }
    </script>
</body>

</html>