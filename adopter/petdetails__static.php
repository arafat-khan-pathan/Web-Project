<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Max - Pet Details | Paws & Hearts</title>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #f97316;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: var(--text-main);
            line-height: 1.6;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

        /* Details Wrapper */
        .details-wrapper {
            padding: 2rem 0 3rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }
.back-link {
            position: relative;
            top: 10px;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }
        .back-link:hover {
            color: var(--primary);
            transform: translateX(-5px);
        }

        /* Card Layout */
        .details-card {
            background: white;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 900px) {
            .details-card {
                flex-direction: row;
                min-height: 600px;
            }
        }

        /* Left Section: Image */
        .pet-gallery {
            flex: 0 0 450px;
            background: #f1f5f9;
            position: relative;
        }

        .pet-gallery img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 900px) {
            .pet-gallery {
                flex: auto;
                height: 350px;
            }
        }

        /* Right Section: Info */
        .pet-info {
            flex: 1;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .info-header h1 {
            font-size: 2.5rem;
            color: #7c2d12;
            margin-bottom: 0.5rem;
        }

        /* Grid */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .detail-item {
            background: #f8fafc;
            padding: 0.8rem;
            border-radius: 0.6rem;
            border: 1px solid #f1f5f9;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.3rem;
        }

        .detail-value {
            font-weight: 700;
            color: var(--text-main);
            font-size: 1rem;
        }

        /* Info Section */
        .info-section h3 {
            font-size: 1.1rem;
            color: var(--text-main);
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-section p {
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* Medical Grid */
        .medical-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.8rem;
            border: 1px solid #f1f5f9;
        }

        @media (min-width: 600px) {
            .medical-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .medical-box h4 {
            font-size: 0.9rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.8rem;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.4rem;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .status-check { color: #16a34a; }

        /* Medical Table */
        .log-table-container {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #f1f5f9;
            border-radius: 0.5rem;
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .log-table th {
            text-align: left;
            padding: 0.8rem;
            background: #f1f5f9;
            color: var(--text-muted);
        }

        .log-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Action Buttons */
        .action-area {
            margin-top: auto;
            display: flex;
            gap: 1rem;
        }

        .btn-large {
            padding: 0.8rem 2rem;
            border-radius: 0.5rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            flex: 1;
            transition: transform 0.2s;
        }

        .btn-large:hover { transform: translateY(-2px); }

        .btn-adopt-now {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .btn-sponsor {
            background: white;
            border: 2px solid #e2e8f0;
            color: var(--text-main);
        }


                /* Action Buttons */
        .action-area {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-large {
            padding: 0.9rem 1.5rem;
            border-radius: 0.6rem;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            flex: 1;
            min-width: 200px;
        }

        .btn-adopt {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .btn-adopt:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-chat {
            background: var(--chat-blue);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-chat:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }
        /* Container to hold the buttons */
.action-area {
    margin-top: auto;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    padding-top: 1.5rem;
}

/* Base button styles */
.btn-large {
    flex: 1;
    min-width: 180px;
    padding: 1rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 1rem;
    text-align: center;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
}

/* Hover effect for both */
.btn-large:hover {
    transform: translateY(-2px);
    filter: brightness(1.1);
}

/* Chat Button - Blue Theme */
.btn-chat {
    background-color: #2563eb;
    color: white;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

/* Adopt Button - Orange Theme */
.btn-adopt {
    background-color: #f97316;
    color: white;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
}

    </style>
    <link rel="stylesheet" href="../index.css">
</head>

<body>

    <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.html" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.html" class="active hov">Browse Pets</a>
                <a href="dashboard__.html">Dashboard</a>
                <!-- <a href="shelter.html">Shelter</a> -->
                <a href="messages.html">Message</a>
                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <a href="profile.html" id="userNameDisplay" class="hov ">Arafat Khan</a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container details-wrapper">
        <a href="index.html" class="back-link">
            <i data-lucide="arrow-left"></i> Back to Browse
        </a>

        <div class="details-card">
            <div class="pet-gallery">
                <img src="https://images.unsplash.com/photo-1633722715463-d30f4f325e24?auto=format&fit=crop&q=80&w=800" alt="Max">
            </div>
            
            <div class="pet-info">
                <div class="info-header">
                    <h1>Max</h1>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Animal Type</div>
                            <div class="detail-value">Dog</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Breed</div>
                            <div class="detail-value">Golden Retriever</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Size</div>
                            <div class="detail-value">Large</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Gender</div>
                            <div class="detail-value">Male</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Age</div>
                            <div class="detail-value">3 Years</div>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3><i data-lucide="info"></i> About Max</h3>
                    <p>Max is the definition of a 'Good Boy'. He was rescued from a situation where he had too much energy for a small apartment. He loves tennis balls, swimming, and is incredibly gentle with children. He knows basic commands like 'sit', 'stay', and 'paw'.</p>
                </div>

                <div class="medical-grid">
                    <div class="medical-box">
                        <h4>Vaccination Status</h4>
                        <div class="status-item">
                            <i data-lucide="check-circle-2" class="status-check" width="16"></i>
                            <span>Rabies: Up to Date (Oct 2024)</span>
                        </div>
                        <div class="status-item">
                            <i data-lucide="check-circle-2" class="status-check" width="16"></i>
                            <span>DHPP: Up to Date (Jan 2024)</span>
                        </div>
                        <div class="status-item">
                            <i data-lucide="alert-circle" style="color: #ea580c" width="16"></i>
                            <span>Bordetella: Due Soon (Mar 2023)</span>
                        </div>
                    </div>
                    <div class="medical-box">
                        <h4>Health Summary</h4>
                        <p><strong>Overall Health:</strong> Excellent</p>
                        <p style="font-size: 0.85rem; margin-top: 0.5rem; color: var(--text-muted);">Last comprehensive vet check performed on Jan 15, 2024.</p>
                    </div>
                </div>

                <div class="info-section">
                    <h3><i data-lucide="clipboard-list"></i> Medical Log</h3>
                    <div class="log-table-container">
                        <table class="log-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-15</td>
                                    <td>Annual Checkup - Healthy weight (32kg)</td>
                                </tr>
                                <tr>
                                    <td>2023-11-02</td>
                                    <td>Minor ear infection treated</td>
                                </tr>
                                <tr>
                                    <td>2023-06-10</td>
                                    <td>Neutered</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                  <div class="action-area">
                    <a href="messages.html?pet=Max" class="btn-large btn-chat">
                        <i data-lucide="message-circle"></i> Chat with Shelter Staff
                    </a>
                   <a href="apply.html"
                                 class="btn-large btn-adopt-now">Apply to Adopt</a>
                </div>
            </div>
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
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

          function logout() {
            localStorage.clear();
            window.location.href = "../login__.html";
        }
    </script>
</body>
</html>