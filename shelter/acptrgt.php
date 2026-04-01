<!-- Pet Gallery Component -->
<div id="pet-gallery-container">
    <style>
        :root {
            --primary: #f97316;
            --primary-hover: #ea580c;
            --danger: #ef4444;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --bg-card: #ffffff;
        }

        .gallery-wrapper {
            font-family: system-ui, -apple-system, sans-serif;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header Styles */
        .section-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
        }

        .header-left h2 {
            font-size: 1.5rem;
            margin: 0 0 0.5rem 0;
            color: var(--text-main);
        }

        .header-underline {
            height: 2px;
            width: 40px;
            background: var(--primary);
            border-radius: 2px;
        }

        .btn-add-pet {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.2s;
        }

        .btn-add-pet:hover {
            background: var(--primary-hover);
        }

        /* Grid Styles */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .pet-card {
            background: var(--bg-card);
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .pet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Delete Icon */
        .btn-remove-pet {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--danger);
            z-index: 10;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-remove-pet:hover {
            background: var(--danger);
            color: white;
        }

        .image-wrapper {
            height: 200px;
            position: relative;
        }

        .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gender-icon {
            position: absolute;
            bottom: 0.75rem;
            left: 0.75rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.2rem 0.5rem;
            border-radius: 2rem;
            font-size: 0.9rem;
        }

        .card-content {
            padding: 1.25rem;
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .card-header-flex h3 {
            margin: 0;
            font-size: 1.2rem;
            color: var(--text-main);
        }

        .pet-age {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .pet-breed {
            margin: 0 0 1rem 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .card-tags {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .tag {
            background: #f1f5f9;
            color: #475569;
            padding: 0.2rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .btn-adopt {
            background: #f8fafc;
            color: var(--text-main);
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-adopt:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        /* Basic Overlay for Action Confirmation */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-box {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
    </style>

    <div class="gallery-wrapper">
        <!-- Pet Section Header -->
        <div class="section-header-flex">
            <div class="header-left">
                <h2>Current Residents</h2>
                <div class="header-underline"></div>
            </div>
            <button class="btn-add-pet" onclick="toggleAddPetModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add New Pet
            </button>
        </div>

        <!-- Pet Gallery -->
        <div class="pet-grid" id="petGrid">
            <!-- Max -->
            <div class="pet-card" data-id="max">
                <button class="btn-remove-pet" title="Remove Pet" onclick="confirmRemove('max')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                <div class="image-wrapper">
                    <img src="https://images.unsplash.com/photo-1633722715463-d30f4f325e24?auto=format&fit=crop&q=80&w=400">
                    <div class="gender-icon">♂️</div>
                </div>
                <div class="card-content">
                    <div class="card-header-flex">
                        <h3>Max</h3>
                        <span class="pet-age">3y</span>
                    </div>
                    <p class="pet-breed">Golden Retriever</p>
                    <div class="card-tags">
                        <span class="tag">large</span>
                        <span class="tag">dog</span>
                    </div>
                    <a href="#" class="btn-adopt" style="text-align:center; text-decoration:none; display:block;">Meet Max</a>
                </div>
            </div>

            <!-- Luna -->
            <div class="pet-card" data-id="luna">
                <button class="btn-remove-pet" onclick="confirmRemove('luna')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                <div class="image-wrapper">
                    <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&q=80&w=400">
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
                    <a href="#" class="btn-adopt" style="text-align:center; text-decoration:none; display:block;">Meet Luna</a>
                </div>
            </div>

            <!-- Charlie -->
            <div class="pet-card" data-id="charlie">
                <button class="btn-remove-pet" onclick="confirmRemove('charlie')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                <div class="image-wrapper">
                    <img src="https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?auto=format&fit=crop&q=80&w=400">
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
                    <a href="#" class="btn-adopt" style="text-align:center; text-decoration:none; display:block;">Meet Charlie</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-box">
            <h3 style="margin-top:0;">Remove Resident?</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Are you sure you want to remove this pet from the active directory?</p>
            <div style="display:flex; gap: 0.5rem;">
                <button id="confirmBtn" style="flex:1; padding: 0.75rem; border-radius: 0.5rem; border:none; background: var(--danger); color:white; font-weight:600; cursor:pointer;">Yes, Remove</button>
                <button onclick="closeModal()" style="flex:1; padding: 0.75rem; border-radius: 0.5rem; border:1px solid #e2e8f0; background:white; font-weight:600; cursor:pointer;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let currentTargetId = null;

        function confirmRemove(id) {
            currentTargetId = id;
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        document.getElementById('confirmBtn').addEventListener('click', () => {
            if (currentTargetId) {
                const card = document.querySelector(`.pet-card[data-id="${currentTargetId}"]`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        closeModal();
                    }, 200);
                }
            }
        });

        function toggleAddPetModal() {
            // Simplified for logic: in a real app this would open a form
            const name = prompt("Enter Pet Name:");
            if (name) {
                const grid = document.getElementById('petGrid');
                const id = name.toLowerCase().replace(/\s/g, '-');
                const newCard = `
                    <div class="pet-card" data-id="${id}">
                        <button class="btn-remove-pet" onclick="confirmRemove('${id}')">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                        <div class="image-wrapper">
                            <img src="https://images.unsplash.com/photo-1543466835-00a7907e9de1?auto=format&fit=crop&q=80&w=400">
                            <div class="gender-icon">🐾</div>
                        </div>
                        <div class="card-content">
                            <div class="card-header-flex">
                                <h3>${name}</h3>
                                <span class="pet-age">New</span>
                            </div>
                            <p class="pet-breed">New Arrival</p>
                            <div class="card-tags">
                                <span class="tag">Just added</span>
                            </div>
                            <a href="#" class="btn-adopt" style="text-align:center; text-decoration:none; display:block;">Meet ${name}</a>
                        </div>
                    </div>
                `;
                grid.insertAdjacentHTML('afterbegin', newCard);
            }
        }

          function logout() {
            localStorage.clear();
            window.location.href = "logout.php";
        }
    </script>
</div>