<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$userName = $_SESSION['user_name'];
$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroSmart | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --accent: #4ade80;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&q=80&w=2000') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            color: white;
            margin: 0;
            overflow-x: hidden;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
            display: none;
            position: fixed;
            inset: 0;
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            outline: none;
            color: white;
        }

        .input-glass::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .input-glass:focus { border-color: var(--accent); }

        .loader {
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-top-color: #4ade80;
            border-radius: 50%;
            animation: spinner 0.6s linear infinite;
        }

        @keyframes spinner { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="p-4 md:p-8">

    <!-- MODAL: Crop Selection Form -->
    <div id="cropModal" class="modal-overlay">
        <div class="glass w-full max-w-2xl rounded-[2.5rem] p-8 md:p-10 relative animate-in fade-in zoom-in duration-300">
            <button onclick="closeModal()" class="absolute top-6 right-8 text-2xl opacity-50 hover:opacity-100">&times;</button>
            
            <!-- Step 1: Input Form -->
            <div id="modalStep1">
                <h2 class="text-3xl font-bold mb-2">Farm Details</h2>
                <p class="text-green-400 text-sm mb-8">Tell us about your land to find the perfect crop.</p>
                
                <form id="farmDetailsForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="area" placeholder="Area (District/State)" class="input-glass rounded-xl p-4 text-sm" required>
                    <input type="text" id="season" placeholder="Current Season" class="input-glass rounded-xl p-4 text-sm" required>
                    <input type="text" id="soil" placeholder="Soil Type" class="input-glass rounded-xl p-4 text-sm" required>
                    <input type="text" id="water" placeholder="Water Availability" class="input-glass rounded-xl p-4 text-sm" required>
                    <input type="text" id="landsize" placeholder="Land Size (Acres)" class="input-glass rounded-xl p-4 text-sm md:col-span-2" required>
                    
                    <button type="button" onclick="suggestCrops()" class="md:col-span-2 bg-green-500 hover:bg-green-600 py-4 rounded-xl font-bold text-black mt-4 transition-all flex justify-center items-center">
                        <span id="btnText">Find Top 3 Crops</span>
                        <div id="btnLoader" class="hidden loader w-5 h-5 ml-3"></div>
                    </button>
                </form>
                <p id="errorMsg" class="text-red-400 text-sm mt-4 hidden text-center"></p>
            </div>

            <!-- Step 2: Crop Recommendations -->
            <div id="modalStep2" class="hidden">
                <h2 class="text-3xl font-bold mb-2">Recommendations</h2>
                <p class="text-green-400 text-sm mb-6">Select a crop to see a detailed farming guide.</p>
                <div id="cropSuggestions" class="space-y-4">
                    <!-- Suggestions appear here -->
                </div>
                <button onclick="backToForm()" class="w-full mt-6 text-sm opacity-50 hover:opacity-100">← Change Details</button>
            </div>

            <!-- Step 3: Full Guide -->
            <div id="modalStep3" class="hidden max-h-[70vh] flex flex-col">
                <h2 class="text-2xl font-bold mb-4" id="guideTitle">Crop Guide</h2>
                <div id="guideContent" class="overflow-y-auto pr-4 text-white/80 leading-relaxed space-y-4 text-sm">
                    <!-- Guide appears here -->
                </div>
                <div class="pt-6 mt-auto flex gap-4">
                    <button onclick="backToSuggestions()" class="flex-1 bg-white/5 py-4 rounded-xl font-bold hover:bg-white/10 transition-all">Back</button>
                    <button onclick="closeModal()" class="flex-1 bg-green-500 text-black py-4 rounded-xl font-bold hover:bg-green-600 transition-all">Finished</button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <!-- Navigation -->
        <nav class="flex justify-between items-center mb-12">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-2xl">🌱</div>
                <h1 class="text-2xl font-bold tracking-tighter">AgroSmart</h1>
            </div>
            <a href="logout.php" class="glass px-5 py-2 rounded-xl text-sm font-bold border-red-500/20 hover:bg-red-500/10">Sign Out</a>
        </nav>

        <!-- Greeting -->
        <header class="mb-12">
            <h2 class="text-5xl font-bold mb-2">Hello, <span class="text-green-400"><?php echo htmlspecialchars($userName); ?></span></h2>
            <p class="opacity-60">Ready to optimize your farm today?</p>
        </header>

        <!-- Tools Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div onclick="openModal()" class="glass rounded-[2rem] p-10 cursor-pointer hover:bg-white/10 border-green-500/20 hover:border-green-500/50 transition-all flex flex-col items-center text-center">
                <div class="text-5xl mb-6">🌾</div>
                <h3 class="text-2xl font-bold mb-2">Crop Selection</h3>
                <p class="text-sm opacity-50">Find and get a full guide for the best crops based on AI analysis.</p>
            </div>

            <a href="disease_detect.php" class="glass rounded-[2rem] p-10 hover:bg-white/10 transition-all flex flex-col items-center text-center">
                <div class="text-5xl mb-6">🔍</div>
                <h3 class="text-2xl font-bold mb-2">Disease Scanner</h3>
                <p class="text-sm opacity-50">Diagnose plant health issues instantly using your camera.</p>
            </a>

            <a href="market.php" class="glass rounded-[2rem] p-10 hover:bg-white/10 transition-all flex flex-col items-center text-center">
                <div class="text-5xl mb-6">📈</div>
                <h3 class="text-2xl font-bold mb-2">Market Hub</h3>
                <p class="text-sm opacity-50">Check real-time local commodity prices and trends.</p>
            </a>
        </div>
    </div>

    <script>
        const apiKey = ""; // API Key handled by runtime

        function openModal() { document.getElementById('cropModal').style.display = 'flex'; }
        
        function closeModal() { 
            document.getElementById('cropModal').style.display = 'none';
            backToForm();
        }

        function backToForm() {
            document.getElementById('modalStep1').classList.remove('hidden');
            document.getElementById('modalStep2').classList.add('hidden');
            document.getElementById('modalStep3').classList.add('hidden');
        }

        function backToSuggestions() {
            document.getElementById('modalStep2').classList.remove('hidden');
            document.getElementById('modalStep3').classList.add('hidden');
        }

        /**
         * Robust fetch utility with exponential backoff
         */
        async function fetchWithRetry(url, options, maxRetries = 5) {
            for (let i = 0; i < maxRetries; i++) {
                try {
                    const response = await fetch(url, options);
                    if (response.ok) return await response.json();
                } catch (err) {
                    if (i === maxRetries - 1) throw err;
                }
                await new Promise(res => setTimeout(res, Math.pow(2, i) * 1000));
            }
        }

        async function suggestCrops() {
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const errorMsg = document.getElementById('errorMsg');
            
            const area = document.getElementById('area').value;
            const season = document.getElementById('season').value;
            const soil = document.getElementById('soil').value;
            const water = document.getElementById('water').value;

            if(!area || !season || !soil) {
                errorMsg.innerText = "Please fill in all details.";
                errorMsg.classList.remove('hidden');
                return;
            }

            errorMsg.classList.add('hidden');
            btnText.innerText = "Analyzing Environment...";
            btnLoader.classList.remove('hidden');

            const prompt = `Act as an expert agricultural consultant. Based on these conditions: 
            Location: ${area}, 
            Season: ${season}, 
            Soil: ${soil}, 
            Water Availability: ${water}.
            Recommend the top 3 most profitable and sustainable crops.
            Response must be strictly valid JSON in this format: [{"name": "Crop Name", "reason": "Detailed 1-sentence explanation"}]`;

            const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;

            try {
                const data = await fetchWithRetry(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contents: [{ parts: [{ text: prompt }] }],
                        generationConfig: { 
                            responseMimeType: "application/json",
                            responseSchema: {
                                type: "ARRAY",
                                items: {
                                    type: "OBJECT",
                                    properties: {
                                        name: { type: "STRING" },
                                        reason: { type: "STRING" }
                                    },
                                    required: ["name", "reason"]
                                }
                            }
                        }
                    })
                });
                
                const crops = JSON.parse(data.candidates[0].content.parts[0].text);
                
                const container = document.getElementById('cropSuggestions');
                container.innerHTML = '';
                
                crops.forEach(crop => {
                    const div = document.createElement('div');
                    div.className = "bg-white/5 border border-white/10 p-5 rounded-2xl hover:bg-white/10 hover:border-green-400 cursor-pointer transition-all group";
                    div.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-bold text-lg group-hover:text-green-400 transition-colors">${crop.name}</h4>
                                <p class="text-xs opacity-50 mt-1">${crop.reason}</p>
                            </div>
                            <span class="text-green-400 opacity-0 group-hover:opacity-100 transition-all">View Guide →</span>
                        </div>
                    `;
                    div.onclick = () => getFullGuide(crop.name, area, soil);
                    container.appendChild(div);
                });

                document.getElementById('modalStep1').classList.add('hidden');
                document.getElementById('modalStep2').classList.remove('hidden');

            } catch (e) {
                errorMsg.innerText = "Service temporarily unavailable. Please try again in a moment.";
                errorMsg.classList.remove('hidden');
            } finally {
                btnText.innerText = "Find Top 3 Crops";
                btnLoader.classList.add('hidden');
            }
        }

        async function getFullGuide(crop, area, soil) {
            const container = document.getElementById('guideContent');
            const title = document.getElementById('guideTitle');
            
            document.getElementById('modalStep2').classList.add('hidden');
            document.getElementById('modalStep3').classList.remove('hidden');
            
            title.innerText = `Generating ${crop} Guide...`;
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center p-12 text-center">
                    <div class="loader w-12 h-12 mb-4"></div>
                    <p class="opacity-50 animate-pulse">Our AI is drafting a customized roadmap for your ${crop} farm in ${area}...</p>
                </div>
            `;

            const prompt = `Generate a highly detailed, professional farming guide for growing ${crop} specifically for a farmer in ${area} with ${soil} soil. 
            Format the guide using Markdown-style headers for these sections:
            1. LAND PREPARATION & SOIL ENRICHMENT
            2. SEED SELECTION & SOWING TECHNIQUE
            3. WATER & NUTRIENT MANAGEMENT
            4. INTEGRATED PEST & DISEASE MANAGEMENT
            5. HARVESTING & POST-HARVEST CARE
            6. MARKET STRATEGY & PROFIT MAXIMIZATION
            
            Use bullet points for steps and emphasize critical success factors.`;

            const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`;

            try {
                const data = await fetchWithRetry(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        contents: [{ parts: [{ text: prompt }] }]
                    })
                });

                const text = data.candidates[0].content.parts[0].text;
                
                title.innerText = `${crop}: Expert Farming Guide`;
                
                // Convert simple markdown headers/newlines to HTML
                let htmlContent = text
                    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                    .replace(/^# (.*$)/gm, '<h3 class="text-xl font-bold text-green-400 mt-6 mb-2">$1</h3>')
                    .replace(/^## (.*$)/gm, '<h4 class="text-lg font-semibold text-green-300 mt-4 mb-2">$1</h4>')
                    .replace(/^\d\.(.*$)/gm, '<h4 class="text-lg font-semibold text-green-400 mt-6 mb-2">$1</h4>')
                    .split('\n')
                    .filter(line => line.trim() !== '')
                    .map(line => line.startsWith('<h') ? line : `<p class="mb-3">${line}</p>`)
                    .join('');

                container.innerHTML = htmlContent;
            } catch (e) {
                container.innerHTML = `
                    <div class="text-center p-12">
                        <p class="text-red-400 mb-4">Failed to connect to the AI Guide Generator.</p>
                        <button onclick="backToSuggestions()" class="px-6 py-2 bg-white/10 rounded-lg">Try Again</button>
                    </div>
                `;
            }
        }
    </script>
</body>
</html>