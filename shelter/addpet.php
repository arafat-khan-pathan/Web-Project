<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet - Paws & Hearts</title>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- External CSS -->
    <link rel="stylesheet" href="index.css">

    <style>
        .add-pet-wrapper {
            padding: 3rem 0;
            max-width: 900px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }

        .form-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
        }

        .form-header {
            border-bottom: 2px solid #fff7ed;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .form-header h1 {
            color: #7c2d12;
            font-size: 1.8rem;
        }

        .add-pet-form {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .full-width {
            grid-column: span 3;
        }

        .two-thirds {
            grid-column: span 2;
        }

        label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        input,
        select,
        textarea {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .section-title {
            grid-column: span 3;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            color: #7c2d12;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Medical Logs Styles */
        #medicalLogsContainer {
            grid-column: span 3;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .log-entry {
            display: flex;
            gap: 1rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            align-items: center;
        }

        .log-entry input[type="text"] {
            flex: 2;
        }

        .log-entry input[type="datetime-local"] {
            flex: 1;
        }

        .add-log-btn-container {
            grid-column: span 3;
            display: flex;
            justify-content: flex-start;
            margin-top: 0.5rem;
        }

        .btn-add-log {
            background: #fff7ed;
            color: var(--primary);
            border: 1px dashed var(--primary);
            padding: 0.6rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-add-log:hover {
            background: var(--primary-light);
            border-style: solid;
        }

        .btn-remove-log {
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
        }

        .btn-submit {
            grid-column: span 3;
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 800px) {
            .add-pet-form {
                grid-template-columns: 1fr 1fr;
            }

            .full-width,
            .two-thirds,
            .section-title,
            .btn-submit,
            #medicalLogsContainer,
            .add-log-btn-container {
                grid-column: span 2;
            }
        }

        @media (max-width: 500px) {
            .add-pet-form {
                grid-template-columns: 1fr;
            }

            .full-width,
            .two-thirds,
            .section-title,
            .btn-submit,
            #medicalLogsContainer,
            .add-log-btn-container {
                grid-column: span 1;
            }

            .log-entry {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <link rel="stylesheet" href="index.css">
</head>

<body>

    <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.html" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.html" class="hov active">Browse Pets</a>
                <a href="dashboard__.html">Dashboard</a>
                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <!-- <a href="shelter.html">Shelter</a> -->
                <a href="messages.html">Message</a>


               <div class="user-info">
                    <a href="shelterprofile.html" id="userNameDisplay" class="hov">Loyal Friends Rescue</a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container add-pet-wrapper">
        <a href="index.html" class="back-link">
            <i data-lucide="arrow-left"></i> Back to Home
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1>Add New Pet for Adoption</h1>
                <i data-lucide="dog" class="text-primary" size="32"></i>
            </div>

            <form id="addPetForm" class="add-pet-form" onsubmit="savePet(event)">
                <!-- Basic Info -->
                <div class="section-title">Basic Information</div>

                <div class="form-group">
                    <label for="petName">Pet Name</label>
                    <input type="text" id="petName"  placeholder="e.g. Max">
                </div>

                <div class="form-group">
                    <label for="petType">Animal Type</label>
                    <select id="petType" >
                        <option value="">Select Type</option>
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Rabbit">Rabbit</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="petBreed">Breed</label>
                    <input type="text" id="petBreed"  placeholder="e.g. Golden Retriever">
                </div>

                <div class="form-group">
                    <label for="petAge">Age</label>
                    <input type="text" id="petAge"  placeholder="e.g. 2 Years">
                </div>

                <div class="form-group">
                    <label for="petGender">Gender</label>
                    <select id="petGender" >
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="petSize">Size</label>
                    <select id="petSize" >
                        <option value="">Select Size</option>
                        <option value="Small">Small</option>
                        <option value="Medium">Medium</option>
                        <option value="Large">Large</option>
                        <option value="Extra Large">Extra Large</option>
                    </select>
                </div>

                <!-- Media -->
                <div class="section-title">Media & Story</div>

                <div class="form-group two-thirds">
                    <label for="petImg">Image URL</label>
                    <input type="url" id="petImg"  placeholder="https://images.unsplash.com/...">
                </div>

                <div class="form-group full-width">
                    <label for="petAbout">About / Biography</label>
                    <textarea id="petAbout" rows="4" 
                        placeholder="Tell the pet's story and personality..."></textarea>
                </div>

                <!-- Health Info -->
                <div class="section-title">
                    <span>Health & Medical Reports</span>
                </div>

                <div class="form-group">
                    <label for="petHealth">Overall Health Status</label>
                    <select id="petHealth" >
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Needs Special Care">Needs Special Care</option>
                    </select>
                </div>

                <div id="medicalLogsContainer">
                    <!-- Dynamic logs will be injected here -->
                </div>

                <div class="add-log-btn-container">
                    <button type="button" class="btn-add-log" onclick="addMedicalLogField()">
                        <i data-lucide="plus" size="16"></i> Add Another Report
                    </button>
                </div>

                <button type="submit" class="btn-submit">List Pet for Adoption</button>
            </form>
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
            document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "Guest Loyal Friends Rescue";
            // Add an initial log field on load
            addMedicalLogField();
            lucide.createIcons();
        });

        function addMedicalLogField() {
            const container = document.getElementById('medicalLogsContainer');
            const div = document.createElement('div');
            div.className = 'log-entry';

            // Get current local time for the input
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            const currentDateTime = now.toISOString().slice(0, 16);

            div.innerHTML = `
                <input type="datetime-local" class="log-date" value="${currentDateTime}" >
                <input type="text" class="log-note" placeholder="Health update or clinical note..." >
                <button type="button" class="btn-remove-log" onclick="this.parentElement.remove()">
                    <i data-lucide="trash-2" size="18"></i>
                </button>
            `;
            container.appendChild(div);
            lucide.createIcons();
        }

        function savePet(event) {
            event.preventDefault();

            // Collect all medical logs
            const logEntries = document.querySelectorAll('.log-entry');
            const medicalLogs = Array.from(logEntries).map(entry => ({
                date: entry.querySelector('.log-date').value,
                note: entry.querySelector('.log-note').value
            }));

            const petData = {
                name: document.getElementById('petName').value,
                species: document.getElementById('petType').value,
                breed: document.getElementById('petBreed').value,
                age: document.getElementById('petAge').value,
                gender: document.getElementById('petGender').value,
                size: document.getElementById('petSize').value,
                img: document.getElementById('petImg').value,
                about: document.getElementById('petAbout').value,
                health: document.getElementById('petHealth').value,
                medicalLog: medicalLogs,
                vaccines: []
            };

            // Simulation: In a real app, this would be an API call or Firestore update
            console.log("Saving Pet Data:", petData);

            alert(`${petData.name} has been successfully added with ${medicalLogs.length} medical reports!`);
            window.location.href = "index.html";
        }

        function logout() {
            localStorage.clear();
            window.location.href = "../login__.html";
        }
    </script>
</body>

</html>