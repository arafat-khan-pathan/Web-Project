<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Guidelines - Paws & Hearts</title>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #f97316;
            --primary-light: #fff7ed;
            --primary-dark: #ea580c;
            --bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --white: #ffffff;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
        }



        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            margin-top: 10px;
        }

        /* Header Styling */
        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .header p {
            color: var(--text-muted);
            font-size: 1.1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .back-link {
            position: relative;
            top: 20px;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        /* Content Sections */
        .card {
            background: var(--white);
            border-radius: 1rem;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--primary-dark);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .guideline-list {
            list-style: none;
        }

        .guideline-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .guideline-item:last-child {
            border-bottom: none;
        }

        .step-number {
            background: var(--primary-light);
            color: var(--primary);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .item-content h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .item-content p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Fee Table & Requirements */
        .badge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .requirement-badge {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .requirement-badge i {
            color: #10b981;
        }

        .note-box {
            background: #fffbeb;
            border-left: 4px solid #fbbf24;
            padding: 20px;
            border-radius: 0 0.5rem 0.5rem 0;
            margin-top: 20px;
        }

        @media (max-width: 640px) {
            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
    <link rel="stylesheet" href="../index.css">
</head>

<body>
    <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.php" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <!-- <a href="dashboard__.php">Dashboard</a> -->
                <a href="guidelines__.php" class="active">Guidelines</a>
                <div class="user-info">
                    <span id="userNameDisplay">Guest User</span>
                    <button onclick="logout()" class="btn-outline">Login</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <a href="index.php" class="back-link">
            <i data-lucide="arrow-left" size="20"></i> Back to Browse
        </a>

        <header class="header">
            <h1>Adoption Guidelines</h1>
            <p>Everything you need to know about bringing your new friend home.</p>
        </header>

        <!-- Process Section -->
        <section class="card">
            <h2 class="section-title"><i data-lucide="clipboard-list"></i> The Adoption Process</h2>
            <div class="guideline-list">
                <div class="guideline-item">
                    <div class="step-number">1</div>
                    <div class="item-content">
                        <h3>Choose Your Pet</h3>
                        <p>Browse our available animals and find a companion that matches your lifestyle and energy
                            level.</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="step-number">2</div>
                    <div class="item-content">
                        <h3>Submit Application</h3>
                        <p>Fill out the adoption form with details about your living situation, family, and experience
                            with pets.</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="step-number">3</div>
                    <div class="item-content">
                        <h3>Shelter Review</h3>
                        <p>Our staff will review your application within 24-48 hours. We may contact you for a brief
                            chat or a home visit.</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="step-number">4</div>
                    <div class="item-content">
                        <h3>Meet & Greet</h3>
                        <p>The most important part! You'll meet the pet in person to ensure there's a genuine
                            connection.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Medical & Care Section -->
        <section class="card">
            <h2 class="section-title"><i data-lucide="shield-check"></i> Health & Safety Standards</h2>
            <p>At Paws & Hearts, we ensure every pet leaves our care in the best possible condition. Every adopted pet
                comes with:</p>
            <div class="badge-grid">
                <div class="requirement-badge"><i data-lucide="check-circle" size="18"></i> Up-to-date Vaccines</div>
                <div class="requirement-badge"><i data-lucide="check-circle" size="18"></i> Spayed / Neutered</div>
                <div class="requirement-badge"><i data-lucide="check-circle" size="18"></i> Microchipped</div>
                <div class="requirement-badge"><i data-lucide="check-circle" size="18"></i> Dewormed</div>
            </div>
            <div class="note-box">
                <p><strong>Note:</strong> While we provide initial medical records, we recommend establishing a
                    relationship with a local veterinarian within 14 days of adoption.</p>
            </div>
        </section>

        <!-- Requirements Section -->
        <section class="card">
            <h2 class="section-title"><i data-lucide="home"></i> Adopter Requirements</h2>
            <div class="guideline-list">
                <div class="guideline-item">
                    <div class="item-content">
                        <h3>Age Requirement</h3>
                        <p>Primary adopters must be at least 21 years of age with a valid government-issued ID.</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="item-content">
                        <h3>Housing Policy</h3>
                        <p>If you rent your home, we require written consent from your landlord or a copy of your lease
                            showing pets are permitted.</p>
                    </div>
                </div>
                <div class="guideline-item">
                    <div class="item-content">
                        <h3>Adoption Fees</h3>
                        <p>Fees range from $150 to $450 depending on the species and age. These fees help us cover the
                            cost of medical care and daily shelter operations.</p>
                    </div>
                </div>
            </div>
        </section>


    </div>
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
        lucide.createIcons();
      function logout() {
            localStorage.clear();
            window.location.href = "../login__.php";
        }
      
    </script>
</body>

</html>