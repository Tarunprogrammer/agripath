<?php
session_start();
$dbPath = __DIR__ . '/includes/db.php';

if (file_exists($dbPath)) {
    include $dbPath;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($conn)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        try {
            // Prepare statement to fetch user
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Verify user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Redirect to dashboard
                header("Location: dashboard.php"); 
                exit();
            } else {
                $message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $message = "Database Error: " . $e->getMessage();
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroSmart | Farmer Login</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
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
                        url('https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&q=80&w=2000') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin: 0;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 15px rgba(74, 222, 128, 0.3);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-gradient:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(74, 222, 128, 0.4);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-up {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>
</head>
<body class="p-4">

    <div class="w-full max-w-md animate-up py-10">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold tracking-tight mb-2">Welcome Back</h1>
            <p class="text-green-400 font-medium tracking-wide">Continue your journey with AgroSmart.</p>
        </div>

        <!-- Login Form Container -->
        <div class="glass rounded-[2.5rem] p-8 md:p-10">
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-500/50 text-red-200 text-center text-sm">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.15em] opacity-50 mb-1.5 ml-1">Email Address</label>
                    <input type="email" name="email" placeholder="farmer@example.com" required
                           class="w-full input-glass rounded-2xl px-5 py-4 text-white placeholder-white/30">
                </div>
                
                <!-- Password Field -->
                <div>
                    <div class="flex justify-between items-center mb-1.5 ml-1">
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] opacity-50">Password</label>
                        <a href="#" class="text-[10px] font-bold text-green-400 hover:underline uppercase tracking-tighter">Forgot?</a>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" required
                           class="w-full input-glass rounded-2xl px-5 py-4 text-white placeholder-white/30">
                </div>

                <!-- Login Button -->
                <button type="submit" 
                        class="w-full btn-gradient py-4 rounded-2xl font-bold text-lg shadow-lg mt-4">
                    Sign In
                </button>
            </form>

            <!-- Signup Link -->
            <p class="mt-8 text-center text-sm opacity-60">
                New to the platform? 
                <a href="signup.php" class="text-green-400 hover:underline font-semibold">Create account</a>
            </p>
        </div>

        <!-- Simple Footer -->
        <footer class="mt-12 text-center opacity-30 text-[10px] uppercase tracking-[0.2em]">
            Precision Agriculture Management &bull; Secured with Encryption
        </footer>
    </div>

</body>
</html>