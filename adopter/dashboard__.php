<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paws & Hearts | Admin Dashboard</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #f97316;
            --primary-soft: #fff7ed;
            --bg: #f8fafc;
            --card: #ffffff;
            --text-dark: #0f172a;
            --text-light: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --danger-soft: #fef2f2;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text-dark);
            /* padding: 20px; */
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .header-title p {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--card);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 16px;
            border-left: 5px solid var(--border);
        }

        .stat-card.total {
            border-left-color: var(--primary);
        }

        .stat-card.pending {
            border-left-color: var(--warning);
        }

        .stat-card.overdue {
            border-left-color: var(--danger);
        }

        .stat-card.complete {
            border-left-color: var(--success);
        }

        .stat-info .val {
            font-size: 1.5rem;
            font-weight: 800;
            display: block;
            line-height: 1.2;
        }

        .stat-info .label {
            font-size: 0.7rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }

        /* Grid Layouts */
        .grid-main {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            margin-bottom: 24px;
        }

        .grid-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
        }

        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Task Form */
        .task-controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 24px;
            background: var(--primary-soft);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #fed7aa;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 120px;
            gap: 15px;
            align-items: end;
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        label {
            font-size: 0.75rem;
            color: var(--text-light);
            font-weight: 700;
            margin-bottom: 6px;
            display: block;
        }

        .btn-add {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            height: 42px;
        }

        /* Lists Styles */
        .scroll-area {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .list-item {
            display: flex;
            align-items: flex-start;
            padding: 16px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            gap: 12px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }

        .list-item.is-overdue {
            border-color: #fca5a5;
            background: #fffafa;
        }

        .checkbox {
            width: 22px;
            height: 22px;
            border: 2px solid var(--border);
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .checkbox.checked {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .task-meta {
            display: flex;
            gap: 12px;
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .meta-tag {
            display: flex;
            align-items: center;
            gap: 4px;
            background: var(--bg);
            padding: 2px 8px;
            border-radius: 4px;
        }

        .meta-tag.done {
            background: #d1fae5;
            color: #065f46;
            font-weight: 600;
        }

        .meta-tag.overdue-label {
            background: var(--danger-soft);
            color: var(--danger);
            font-weight: 700;
            border: 1px solid #fecaca;
        }

        /* Chart Canvas Wrappers */
        .chart-container {
            position: relative;
            width: 100%;
            height: 220px;
        }

        @media (max-width: 1100px) {

            .grid-main,
            .grid-bottom,
            .stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="../index.css">

</head>

<body>


    <nav class="navbar">
        <div class="container nav-flex">
            <a href="#" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.html">Browse Pets</a>
                <a href="dashboard__.html" class="active hov">Dashboard</a>
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

    <div class="container">
        <header>
            <div class="header-title">
                <h1>Sanctuary Control Center</h1>
                <p id="current-date"></p>
            </div>
            <div>
                <span id="digital-clock"
                    style="font-size: 1.8rem; font-weight: 800; color: var(--primary); font-variant-numeric: tabular-nums;"></span>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="stats-row">
            <div class="stat-card total">
                <div class="stat-info"><span class="val" id="count-total">0</span><span class="label">Total Tasks</span>
                </div>
            </div>
            <div class="stat-card pending">
                <div class="stat-info"><span class="val" id="count-pending">0</span><span class="label">In
                        Progress</span></div>
            </div>
            <div class="stat-card overdue">
                <div class="stat-info"><span class="val" id="count-overdue">0</span><span class="label">Overdue</span>
                </div>
            </div>
            <div class="stat-card complete">
                <div class="stat-info"><span class="val" id="count-completed">0</span><span
                        class="label">Finished</span></div>
            </div>
        </div>

        <!-- Main Workspace -->
        <div class="grid-main">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i data-lucide="clipboard-list"></i> Task Manager</h2>
                    <span style="font-size: 0.8rem; color: var(--text-light)">Overdue alerts enabled</span>
                </div>
                <div class="task-controls">
                    <div class="form-grid">
                        <div><label>Task Description</label><input type="text" id="taskInput"
                                placeholder="Feed kittens..."></div>
                        <div><label>Due Date</label><input type="datetime-local" id="dueDate"></div>
                        <button class="btn-add" onclick="addTask()">Add Task</button>
                    </div>
                </div>
                <div class="scroll-area" id="taskList"></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i data-lucide="pie-chart"></i> Task Distribution</h2>
                </div>
                <div class="chart-container"><canvas id="taskPieChart"></canvas></div>
                <div style="margin-top: 24px;">
                    <h4
                        style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-light); margin-bottom: 12px;">
                        Environmental Status</h4>
                    <div
                        style="display: flex; justify-content: space-around; background: var(--bg); padding: 15px; border-radius: 12px;">
                        <div style="text-align: center;">
                            <i data-lucide="thermometer" size="18" style="color: var(--primary)"></i>
                            <p style="font-size: 0.9rem; font-weight: 700;">72°F</p>
                        </div>
                        <div style="text-align: center;">
                            <i data-lucide="droplets" size="18" style="color: var(--info)"></i>
                            <p style="font-size: 0.9rem; font-weight: 700;">45%</p>
                        </div>
                        <div style="text-align: center;">
                            <i data-lucide="wind" size="18" style="color: var(--success)"></i>
                            <p style="font-size: 0.9rem; font-weight: 700;">Clean</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Analytics Row -->
        <div class="grid-bottom">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i data-lucide="trending-up"></i> Adoption Rate</h2>
                    <span
                        style="font-size: 0.75rem; background: var(--primary-soft); color: var(--primary); padding: 2px 8px; border-radius: 10px;">+12%
                        vs last month</span>
                </div>
                <div class="chart-container"><canvas id="adoptionLineChart"></canvas></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i data-lucide="home"></i> Animal Spaces</h2>
                </div>
                <div class="chart-container"><canvas id="spacePieChart"></canvas></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i data-lucide="package"></i> Inventory Status</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:0.8rem; margin-bottom:5px;">
                            <span>Kitten Dry Food</span><span>85%</span>
                        </div>
                        <div style="height:6px; background:var(--border); border-radius:3px; overflow:hidden;">
                            <div style="width:85%; height:100%; background:var(--primary)"></div>
                        </div>
                    </div>
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:0.8rem; margin-bottom:5px;">
                            <span>Puppy Wet Food</span><span>32%</span>
                        </div>
                        <div style="height:6px; background:var(--border); border-radius:3px; overflow:hidden;">
                            <div style="width:32%; height:100%; background:var(--danger)"></div>
                        </div>
                    </div>
                    <div
                        style="margin-top: 10px; padding: 10px; border-radius: 8px; background: var(--bg); font-size: 0.75rem; color: var(--text-light);">
                        <strong>System Note:</strong> Red tags on tasks indicate overdue status requiring immediate
                        attention.
                    </div>
                </div>
            </div>
        </div>



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
        // Initial data with one task already overdue for demo
        let tasks = [
            {
                id: 1,
                text: "Morning medical checkup for Luna",
                status: "pending",
                start: "08:00",
                end: null,
                due: new Date(Date.now() - 3600000).toISOString() // 1 hour ago
            },
            {
                id: 2,
                text: "Prepare adoption folders",
                status: "completed",
                start: "10:15",
                end: "11:00",
                due: new Date(Date.now() + 7200000).toISOString() // 2 hours from now
            }
        ];

        let charts = {};

        function init() {
            // Set default due date to now + 4 hours
            const defaultDate = new Date();
            defaultDate.setHours(defaultDate.getHours() + 4);
            document.getElementById('dueDate').value = defaultDate.toISOString().slice(0, 16);

            initCharts();
            updateTime();
            setInterval(() => {
                updateTime();
                render(); // Refresh every minute to update overdue status colors
            }, 30000);
            render();
        }

        function updateTime() {
            const now = new Date();
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
            document.getElementById('digital-clock').textContent = now.toLocaleTimeString([], { hour12: false });
        }

        function render() {
            const list = document.getElementById('taskList');
            list.innerHTML = '';
            let stats = { total: tasks.length, completed: 0, pending: 0, overdue: 0 };
            const now = new Date();

            tasks.forEach(t => {
                const isDone = t.status === 'completed';
                const isLate = !isDone && new Date(t.due) < now;

                if (isDone) stats.completed++;
                else if (isLate) stats.overdue++;
                else stats.pending++;

                const div = document.createElement('div');
                div.className = `list-item ${isLate ? 'is-overdue' : ''}`;

                const dueLabel = new Date(t.due).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                div.innerHTML = `
                <div class="checkbox ${isDone ? 'checked' : ''}" onclick="toggleTask(${t.id})">
                    ${isDone ? '<i data-lucide="check" size="14"></i>' : ''}
                </div>
                <div style="flex:1">
                    <p style="${isDone ? 'text-decoration:line-through; color:var(--text-light)' : 'font-weight:600'}">${t.text}</p>
                    <div class="task-meta">
                        <div class="meta-tag"><i data-lucide="clock" size="10"></i> ${t.start}</div>
                        ${isDone ? `<div class="meta-tag done"><i data-lucide="check-circle" size="10"></i> Completed ${t.end}</div>` : ''}
                        <div class="meta-tag ${isLate ? 'overdue-label' : ''}">
                            <i data-lucide="calendar" size="10"></i> 
                            ${isLate ? 'OVERDUE: ' : 'Due: '} ${dueLabel}
                        </div>
                    </div>
                </div>
                <button onclick="deleteTask(${t.id})" style="border:0; background:0; cursor:pointer; color:var(--text-light); padding: 5px;"><i data-lucide="trash-2" size="16"></i></button>
            `;
                list.appendChild(div);
            });

            document.getElementById('count-total').textContent = stats.total;
            document.getElementById('count-pending').textContent = stats.pending;
            document.getElementById('count-overdue').textContent = stats.overdue;
            document.getElementById('count-completed').textContent = stats.completed;

            charts.taskPie.data.datasets[0].data = [stats.completed, stats.pending, stats.overdue];
            charts.taskPie.update();
            lucide.createIcons();
        }

        function addTask() {
            const input = document.getElementById('taskInput');
            const dueInput = document.getElementById('dueDate');
            if (!input.value.trim()) return;

            const now = new Date();
            tasks.unshift({
                id: Date.now(),
                text: input.value,
                status: 'pending',
                start: now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }),
                end: null,
                due: new Date(dueInput.value).toISOString()
            });
            input.value = '';
            render();
        }

        function toggleTask(id) {
            const t = tasks.find(x => x.id === id);
            if (t.status === 'pending') {
                t.status = 'completed';
                t.end = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            } else {
                t.status = 'pending';
                t.end = null;
            }
            render();
        }

        function deleteTask(id) {
            tasks = tasks.filter(x => x.id !== id);
            render();
        }

        function initCharts() {
            charts.taskPie = new Chart(document.getElementById('taskPieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Done', 'Pending', 'Overdue'],
                    datasets: [{ data: [0, 0, 0], backgroundColor: ['#10b981', '#f59e0b', '#ef4444'], borderWidth: 0 }]
                },
                options: { maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } }
            });

            new Chart(document.getElementById('adoptionLineChart'), {
                type: 'line',
                data: {
                    labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Adoptions',
                        data: [15, 22, 18, 28, 35, 42],
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { display: false }, x: { grid: { display: false } } } }
            });

            new Chart(document.getElementById('spacePieChart'), {
                type: 'pie',
                data: {
                    labels: ['Dogs', 'Cats', 'Small Pets', 'Vacant'],
                    datasets: [{
                        data: [45, 30, 15, 20],
                        backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } } } }
            });
        }

        window.onload = init;
        function logout() {
            localStorage.clear();
            window.location.href = "../login__.html";
        }
    </script>
</body>

</html>