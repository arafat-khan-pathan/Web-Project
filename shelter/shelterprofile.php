<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelter Dashboard - Tiny Paws Foundation</title>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --primary-light: #ffedd5;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .userNameDisplay {
            color: #ea580c;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-main);
            line-height: 1.5;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 800;
            font-size: 1.25rem;
            text-decoration: none;
        }

        /* Dashboard Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
            padding: 2rem 0;
        }

        /* Card Base Styling */
        .card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        /* Hero Card (Shelter Info) */
        .hero-card {
            grid-column: span 8;
            flex-direction: row;
            align-items: center;
            gap: 2rem;
        }

        .pfp-large {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .shelter-info h1 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .meta-list {
            list-style: none;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Graph Cards */
        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-wrapper {
            position: relative;
            flex-grow: 1;
            min-height: 220px;
            /* Forces enough space for graphs */
            width: 100%;
        }

        /* Sizing Specific Graph Cards */
        .stats-pie {
            grid-column: span 4;
        }

        .stats-line {
            grid-column: span 5;
        }

        .stats-rating {
            grid-column: span 3;
            text-align: center;
            justify-content: center;
        }

        .stats-bar {
            grid-column: span 4;
        }

        /* Rating UI */
        .rating-num {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stars {
            color: #fbbf24;
            display: flex;
            justify-content: center;
            gap: 2px;
            margin: 0.5rem 0;
        }

        /* Pet Section */
        .section-header {
            grid-column: span 12;
            margin-top: 1rem;
        }

        /* Pet Grid & Cards */
        .pet-grid {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .pet-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .pet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .img-container {
            position: relative;
            aspect-ratio: 4 / 3;
        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gender-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 1.25rem;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .breed-text {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .tag-group {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .tag {
            background: #f1f5f9;
            color: var(--text-muted);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-meet {
            display: block;
            text-align: center;
            background: var(--primary-light);
            color: var(--primary-dark);
            text-decoration: none;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 700;
            transition: 0.2s;
        }

        .btn-meet:hover {
            background: var(--primary);
            color: white;
        }

        /* Action Buttons */
        .btn-group {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            border: none;
            font-size: 0.875rem;
        }

        .btn-white {
            background: white;
            border: 1px solid #e2e8f0;
            color: var(--text-main);
        }

        .btn-orange {
            background: var(--primary);
            color: white;
        }

        /* Controls Area */
        .controls-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .btn-msg {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: 0.2s;
            box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
        }

        .btn-msg:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        /* Responsive Adjustments */
        @media (max-width: 1024px) {

            .hero-card,
            .stats-pie,
            .stats-line,
            .stats-rating,
            .stats-bar {
                grid-column: span 6;
            }
        }

        @media (max-width: 768px) {
            .hero-card {
                flex-direction: column;
                text-align: center;
            }

            .meta-list {
                grid-template-columns: 1fr;
            }

            .hero-card,
            .stats-pie,
            .stats-line,
            .stats-rating,
            .stats-bar {
                grid-column: span 12;
            }
        }

        .pet-card {
            position: relative;
            /* Ensure X icon stays inside the card */
        }

        .remove-pet {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 28px;
            height: 28px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
            border: none;
            color: #ff4757;
            font-weight: bold;
            font-size: 16px;
        }

        .remove-pet:hover {
            background: #ff4757;
            color: white;
            transform: scale(1.1);
        }

        /* Animation for removal */
        .fade-out {
            opacity: 0;
            transform: scale(0.9);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <nav class="navbar">
        <div class="container nav-flex">
            <a href="#" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.html" class="hov">Browse Pets</a>
                <a href="dashboard__.html">Dashboard</a>
                <!-- <a href="shelter.html" class="hov ">Shelter</a> -->
                <a href="messages.html">Message</a>

                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <a href="shelterprofile.html" id="userNameDisplay" class="hov active">Loyal Friends Rescue</a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="controls-flex">
            <a href="shelter.html" class="back-link">
                <i data-lucide="arrow-left" size="20"></i> Back to Browse
            </a>
            <!-- <a href="messages.html" class="btn-msg">
                <i data-lucide="message-square" size="18"></i> Message Shelter
            </a> -->
        </div>


        <div class="dashboard-grid">


            <!-- Shelter Hero -->
            <div class="card hero-card">
                <img src="https://images.unsplash.com/photo-1592194996308-7b43878e84a6?w=400" class="pfp-large"
                    alt="Logo">
                <div class="shelter-info">
                    <h1>Tiny Paws Foundation</h1>
                    <ul class="meta-list">
                        <li class="meta-item"><i data-lucide="map-pin" size="14"></i> Dhaka, Bangladesh</li>
                        <li class="meta-item"><i data-lucide="users" size="14"></i> 12 Volunteers</li>
                        <li class="meta-item"><i data-lucide="heart" size="14"></i> 450+ Adopted</li>
                        <li class="meta-item"><i data-lucide="calendar" size="14"></i> Joined 2021</li>
                    </ul>
                </div>
            </div>

            <!-- Species Pie -->
            <div class="card stats-pie">
                <h3 class="card-title"><i data-lucide="pie-chart" size="18"></i> Population</h3>
                <div class="chart-wrapper">
                    <canvas id="speciesChart"></canvas>
                </div>
            </div>

            <!-- Adoption Success -->
            <div class="card stats-line">
                <h3 class="card-title"><i data-lucide="trending-up" size="18"></i> Adoption Trends</h3>
                <div class="chart-wrapper">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>

            <!-- Trust Rating -->
            <div class="card stats-rating">
                <h3 class="card-title" style="justify-content: center;">Trust Score</h3>
                <div class="rating-num">4.9</div>
                <div class="stars">
                    <i data-lucide="star" fill="currentColor" size="18"></i>
                    <i data-lucide="star" fill="currentColor" size="18"></i>
                    <i data-lucide="star" fill="currentColor" size="18"></i>
                    <i data-lucide="star" fill="currentColor" size="18"></i>
                    <i data-lucide="star" fill="currentColor" size="18"></i>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted);">Based on 240+ reviews</p>
            </div>

            <!-- Intake Bar -->
            <div class="card stats-bar">
                <h3 class="card-title"><i data-lucide="bar-chart-3" size="18"></i> Monthly Intake</h3>
                <div class="chart-wrapper">
                    <canvas id="intakeChart"></canvas>
                </div>
            </div>

            <!-- Pet Section Header -->
            <div class="section-header">
                <div>
                    <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Current Residents</h2>
                    <div style="height: 2px; width: 40px; background: var(--primary); border-radius: 2px;"></div>
                </div>
                <button id="addPetBtn" class="btn-adopt" style="width: auto; padding: 8px 16px; margin: 0;">+ Add
                    Pet</button>
            </div>

            <!-- Pet Gallery -->
            <div class="pet-grid">


                <div class="pet-card" data-species="cat">
                    <button class="remove-pet" title="Remove Pet">✕</button>
                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Luna</h3>
                            <span class="pet-age">2y</span>
                        </div>
                        <p class="pet-breed">Persian</p>
                        <div class="card-tags">
                            <span class="tag">medium</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__profile.html?id=Luna" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Luna</a>
                    </div>
                </div>

                <div class="pet-card" data-species="dog">
                    <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♂️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Charlie</h3>
                            <span class="pet-age">1y</span>
                        </div>
                        <p class="pet-breed">Beagle</p>
                        <div class="card-tags">
                            <span class="tag">medium</span>
                            <span class="tag">dog</span>
                        </div>
                        <a href="petdetails__profile.html?id=Charlie" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Charlie</a>
                    </div>
                </div>

                <div class="pet-card" data-species="cat">
                    <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Bella</h3>
                            <span class="pet-age">4y</span>
                        </div>
                        <p class="pet-breed">Siamese</p>
                        <div class="card-tags">
                            <span class="tag">small</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__profile.html?id=Bella" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Bella</a>
                    </div>
                </div>

                <div class="pet-card" data-species="rabbit"> <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Daisy</h3>
                            <span class="pet-age">1y</span>
                        </div>
                        <p class="pet-breed">Holland Lop</p>
                        <div class="card-tags">
                            <span class="tag">small</span>
                            <span class="tag">rabbit</span>
                        </div>
                        <a href="petdetails__profile.html?id=Daisy" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Daisy</a>
                    </div>
                </div>


                <div class="pet-card" data-species="dog"> <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♂️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Rocky</h3>
                            <span class="pet-age">5y</span>
                        </div>
                        <p class="pet-breed">Bulldog</p>
                        <div class="card-tags">
                            <span class="tag">medium</span>
                            <span class="tag">dog</span>
                        </div>
                        <a href="petdetails__profile.html?id=Rocky" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Rocky</a>
                    </div>
                </div>

                <div class="pet-card" data-species="cat"> <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♂️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Milo</h3>
                            <span class="pet-age">3y</span>
                        </div>
                        <p class="pet-breed">Maine Coon</p>
                        <div class="card-tags">
                            <span class="tag">large</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__profile.html?id=Milo" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Milo</a>
                    </div>
                </div>

                <div class="pet-card" data-species="dog"> <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Coco</h3>
                            <span class="pet-age">2y</span>
                        </div>
                        <p class="pet-breed">Mini Rex</p>
                        <div class="card-tags">
                            <span class="tag">small</span>
                            <span class="tag">dog</span>
                        </div>
                        <a href="petdetails__profile.html?id=Coco" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Coco</a>
                    </div>
                </div>

                <div class="pet-card" data-species="dog"> <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Lucy</h3>
                            <span class="pet-age">2y</span>
                        </div>
                        <p class="pet-breed">Labrador</p>
                        <div class="card-tags">
                            <span class="tag">large</span>
                            <span class="tag">dog</span>
                        </div>
                        <a href="petdetails__profile.html?id=Lucy" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Lucy</a>
                    </div>
                </div>

                <div class="pet-card" data-species="cat"> <button class="remove-pet" title="Remove Pet">✕</button>

                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1592194996308-7b43878e84a6?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♂️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Leo</h3>
                            <span class="pet-age">4y</span>
                        </div>
                        <p class="pet-breed">British Shorthair</p>
                        <div class="card-tags">
                            <span class="tag">medium</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__profile.html?id=Leo" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Leo</a>
                    </div>
                </div>

            </div>

            <div class="section-header">
                <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Adopted Pets</h2>
                <div style="height: 2px; width: 40px; background: var(--primary); border-radius: 2px;"></div>
            </div>

            <!-- Pet Gallery adoption given -->
            <div class="pet-grid">


                <div class="pet-card" data-species="cat">
                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Luna</h3>
                            <span class="pet-age">2y</span>
                        </div>
                        <p class="pet-breed">Persian</p>
                        <div class="card-tags">
                            <span class="tag">medium</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__static2.html?id=Luna" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Luna</a>
                    </div>
                </div>



                <div class="pet-card" data-species="cat">
                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1513245543132-31f507417b26?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Bella</h3>
                            <span class="pet-age">4y</span>
                        </div>
                        <p class="pet-breed">Siamese</p>
                        <div class="card-tags">
                            <span class="tag">small</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__static2.html?id=Bella" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Bella</a>
                    </div>
                </div>

                <div class="pet-card" data-species="rabbit">
                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♀️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Daisy</h3>
                            <span class="pet-age">1y</span>
                        </div>
                        <p class="pet-breed">Holland Lop</p>
                        <div class="card-tags">
                            <span class="tag">small</span>
                            <span class="tag">rabbit</span>
                        </div>
                        <a href="petdetails__static2.html?id=Daisy" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Daisy</a>
                    </div>
                </div>


                <div class="pet-card" data-species="dog">
                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♂️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Rocky</h3>
                            <span class="pet-age">5y</span>
                        </div>
                        <p class="pet-breed">Bulldog</p>
                        <div class="card-tags">
                            <span class="tag">medium</span>
                            <span class="tag">dog</span>
                        </div>
                        <a href="petdetails__static2.html?id=Rocky" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Rocky</a>
                    </div>
                </div>

                <div class="pet-card" data-species="cat">
                    <div class="image-wrapper">
                        <img
                            src="https://images.unsplash.com/photo-1533738363-b7f9aef128ce?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">♂️</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>Milo</h3>
                            <span class="pet-age">3y</span>
                        </div>
                        <p class="pet-breed">Maine Coon</p>
                        <div class="card-tags">
                            <span class="tag">large</span>
                            <span class="tag">cat</span>
                        </div>
                        <a href="petdetails__static2.html?id=Milo" class="btn-adopt"
                            style="text-align:center; text-decoration:none; display:block;">Meet Milo</a>
                    </div>
                </div>




            </div>
        </div>
        </div>
        <!-- Adoption Requests Component -->
        <div id="adoption-requests-container">
            <style>
                .adoption-section-wrapper {
                    background: #ffffff;
                    border-radius: 1rem;
                    padding: 1.5rem;
                    margin-bottom: 2rem;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    font-family: system-ui, -apple-system, sans-serif;
                }

                .section-header {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    margin-bottom: 1.5rem;
                    color: #1e293b;
                }

                .request-list {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }

                .applicant-card {
                    border: 1px solid #e2e8f0;
                    border-radius: 0.75rem;
                    padding: 1.25rem;
                    transition: all 0.2s ease;
                }

                .applicant-card:hover {
                    border-color: #f97316;
                    background: #fffaf7;
                }

                .applicant-top {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    margin-bottom: 1rem;
                }

                .applicant-profile {
                    display: flex;
                    gap: 0.75rem;
                    align-items: center;
                }

                .avatar-circle {
                    width: 42px;
                    height: 42px;
                    border-radius: 50%;
                    background: #ffedd5;
                    color: #ea580c;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 0.9rem;
                }

                .applicant-name-text {
                    font-weight: 700;
                    color: #0f172a;
                    margin: 0;
                    font-size: 1rem;
                }

                .applicant-subtext {
                    font-size: 0.8rem;
                    color: #64748b;
                    margin: 0;
                }

                .details-btn {
                    background: none;
                    border: none;
                    color: #f97316;
                    font-size: 0.85rem;
                    font-weight: 600;
                    text-decoration: underline;
                    cursor: pointer;
                    padding: 0;
                }

                .message-box {
                    background: #f1f5f9;
                    padding: 0.75rem;
                    border-radius: 0.5rem;
                    font-size: 0.9rem;
                    color: #334155;
                    margin-bottom: 1.25rem;
                    line-height: 1.5;
                    border-left: 3px solid #cbd5e1;
                }

                .action-group {
                    display: flex;
                    gap: 0.75rem;
                }

                .btn-ui {
                    flex: 1;
                    padding: 0.6rem;
                    border-radius: 0.5rem;
                    font-weight: 600;
                    font-size: 0.85rem;
                    cursor: pointer;
                    border: 1px solid transparent;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.4rem;
                    transition: 0.2s;
                }

                .btn-approve-ui {
                    background: #f0fdf4;
                    color: #15803d;
                    border-color: #bbf7d0;
                }

                .btn-approve-ui:hover {
                    background: #15803d;
                    color: #ffffff;
                }

                .btn-reject-ui {
                    background: #fef2f2;
                    color: #b91c1c;
                    border-color: #fecaca;
                }

                .btn-reject-ui:hover {
                    background: #b91c1c;
                    color: #ffffff;
                }

                /* Modal specific */
                .overlay-ui {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    backdrop-filter: blur(4px);
                    z-index: 10000;
                    align-items: center;
                    justify-content: center;
                }

                .modal-ui {
                    background: white;
                    width: 90%;
                    max-width: 400px;
                    padding: 2rem;
                    border-radius: 1rem;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                }

                .dp {
                    display: flex;
                    align-items: flex-end;
                    justify-content: center;
                }

                .dp button {
                    margin-left: 10px;
                }

                .dp button:hover {
                    color: #f974167f;
                    transform: scale(1.1);
                }
            </style>

            <div class="adoption-section-wrapper">
                <div class="section-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        style="color: #f97316;">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h3 style="margin:0; font-size: 1.1rem;">Pending Adoption Requests</h3>
                </div>

                <div class="request-list">
                    <!-- Card 1 -->
                    <div class="applicant-card" id="card-jdoe">
                        <div class="applicant-top">
                            <div class="applicant-profile">
                                <div class="avatar-circle">JD</div>
                                <div>
                                    <p class="applicant-name-text">John Doe</p>
                                    <p class="applicant-subtext">2 days ago • Fenced Yard</p>
                                </div>
                            </div>
                            <div class="dp"><button class="details-btn"
                                    onclick="openAdoptionModal('Sarah Miller', 'Professional dog trainer. Has two other dogs. Looking for a high-energy companion for agility training.')">Details</button>
                                <button class="details-btn"
                                    onclick="openAdoptionModal('print','Thank You')">Print</button>
                            </div>
                        </div>
                        <div class="message-box">
                            "We have a quiet home and lots of love to give. Max would be the center of our world..."
                        </div>
                        <div class="action-group">
                            <button class="btn-ui btn-approve-ui"
                                onclick="processAdoption('card-jdoe', 'Approved')">Approve</button>
                            <button class="btn-ui btn-reject-ui"
                                onclick="processAdoption('card-jdoe', 'Rejected')">Reject</button>
                            <!-- <button class="btn-ui btn-reject-ui" onclick="processAdoption('card-jdoe', 'Rejected')">Print</button> -->

                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="applicant-card" id="card-smiller">
                        <div class="applicant-top">
                            <div class="applicant-profile">
                                <div class="avatar-circle">SM</div>
                                <div>
                                    <p class="applicant-name-text">Sarah Miller</p>
                                    <p class="applicant-subtext">5 hours ago • Multi-pet</p>
                                </div>
                            </div>
                            <div class="dp"><button class="details-btn"
                                    onclick="openAdoptionModal('Sarah Miller', 'Professional dog trainer. Has two other dogs. Looking for a high-energy companion for agility training.')">Details</button>
                                <button class="details-btn"
                                    onclick="openAdoptionModal('print','Thank You')">Print</button>
                            </div>

                        </div>
                        <div class="message-box">
                            "Max caught my eye immediately. He looks like he has the perfect energy for our pack..."
                        </div>
                        <div class="action-group">
                            <button class="btn-ui btn-approve-ui"
                                onclick="processAdoption('card-smiller', 'Approved')">Approve</button>
                            <button class="btn-ui btn-reject-ui"
                                onclick="processAdoption('card-smiller', 'Rejected')">Reject</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Overlay -->
            <div id="adoptionOverlay" class="overlay-ui" onclick="closeAdoptionModal(event)">
                <div class="modal-ui" onclick="event.stopPropagation()">
                    <h2 id="modalTitle" style="margin-top:0; color: #1e293b; font-size: 1.25rem;">Applicant Profile</h2>
                    <p id="modalContent"
                        style="color: #475569; line-height: 1.6; font-size: 0.95rem; margin-bottom: 2rem;"></p>
                    <button onclick="document.getElementById('adoptionOverlay').style.display='none'"
                        style="width:100%; padding: 0.75rem; border-radius: 0.5rem; border: none; background: #1e293b; color: white; font-weight: 600; cursor: pointer;">Close
                        Window</button>
                </div>
            </div>

            <script>
                function openAdoptionModal(name, bio) {
                    document.getElementById('modalTitle').innerText = name + "'s Application";
                    document.getElementById('modalContent').innerText = bio;
                    document.getElementById('adoptionOverlay').style.display = 'flex';
                }

                function closeAdoptionModal(e) {
                    document.getElementById('adoptionOverlay').style.display = 'none';
                }

                function processAdoption(cardId, action) {
                    const card = document.getElementById(cardId);
                    if (action === 'Approved') {
                        card.style.background = '#f0fdf4';
                        card.style.borderColor = '#22c55e';
                        card.innerHTML = `<div style="text-align:center; padding: 1rem; color: #15803d; font-weight: bold;">✓ Application Approved</div>`;
                    } else {
                        card.style.opacity = '0.4';
                        card.style.transform = 'scale(0.95)';
                        setTimeout(() => card.remove(), 600);
                    }
                }
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
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Generic chart options to ensure responsiveness
            const chartConfig = {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            padding: 20,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            };

            // Species Pie
            new Chart(document.getElementById('speciesChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Dogs', 'Cats', 'Rabbits'],
                    datasets: [{
                        data: [45, 35, 20],
                        backgroundColor: ['#f97316', '#fb923c', '#fdba74'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    ...chartConfig,
                    cutout: '70%'
                }
            });

            // Trends Line
            new Chart(document.getElementById('trendsChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Adoptions',
                        data: [12, 19, 15, 25, 22, 30],
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            display: false
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Intake Bar
            new Chart(document.getElementById('intakeChart'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Animals',
                        data: [8, 12, 10, 15, 12, 18],
                        backgroundColor: '#f97316',
                        borderRadius: 4
                    }]
                },
                options: {
                    ...chartConfig,
                    scales: {
                        y: {
                            display: false
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        function logout() {
            localStorage.clear();
            window.location.href = "login__.html";
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const petGrid = document.querySelector('.pet-grid');
            const addPetBtn = document.getElementById('addPetBtn');

            // Function to remove a pet with safety check
            if (petGrid) {
                petGrid.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-pet')) {
                        const card = e.target.closest('.pet-card');
                        if (card) {
                            card.classList.add('fade-out');
                            setTimeout(() => {
                                card.remove();
                            }, 300);
                        }
                    }
                });
            }

            // Add Pet functionality with safety check
            if (addPetBtn && petGrid) {
                addPetBtn.addEventListener('click', () => {
                    const name = prompt("Enter Pet Name:");
                    if (!name) return;

                    const breed = prompt("Enter Breed:", "Mixed Breed");
                    const species = prompt("Enter Species (dog/cat):", "dog").toLowerCase();

                    const newCard = document.createElement('div');
                    newCard.className = 'pet-card';
                    newCard.setAttribute('data-species', species);

                    newCard.innerHTML = `
                    <button class="remove-pet" title="Remove Pet">✕</button>
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&q=80&w=400">
                        <div class="gender-icon">🐾</div>
                    </div>
                    <div class="card-content">
                        <div class="card-header-flex">
                            <h3>${name}</h3>
                            <span class="pet-age">New</span>
                        </div>
                        <p class="pet-breed">${breed}</p>
                        <div class="card-tags">
                            <span class="tag">small</span>
                            <span class="tag">${species}</span>
                        </div>
                        <a href="petdetails__profile.html?id=${name}" class="btn-adopt"
                           style="text-align:center; text-decoration:none; display:block;">Meet ${name}</a>
                    </div>
                `;

                    petGrid.prepend(newCard);
                });
            }
        });
    </script>
</body>

</html>