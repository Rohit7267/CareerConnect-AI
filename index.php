<?php
// CareerConnect AI - Home Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CareerConnect AI | Smart Career Platform</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #eef2ff 0%,
                #f8fafc 50%,
                #ecfeff 100%
            );
            color: #172033;
            line-height: 1.6;
        }

        a {
            text-decoration: none;
        }

        /* ================= NAVBAR ================= */

        nav {
            width: 100%;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            width: 90%;
            max-width: 1150px;
            height: 70px;
            margin: auto;

            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 21px;
            font-weight: 800;
            color: #1e293b;
        }

        .logo span {
            color: #4f46e5;
        }

        .nav-links {
            display: flex;
            gap: 28px;
        }

        .nav-links a {
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #4f46e5;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .login-btn,
        .register-btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .login-btn {
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            background: white;
        }

        .login-btn:hover {
            background: #eef2ff;
        }

        .register-btn {
            background: #4f46e5;
            color: white;
            box-shadow: 0 5px 15px rgba(79,70,229,0.20);
        }

        .register-btn:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }

        /* ================= HERO ================= */

        .hero-section {
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            width: 380px;
            height: 380px;
            background: #c7d2fe;
            opacity: 0.28;
            filter: blur(90px);
            border-radius: 50%;
            top: 80px;
            left: -120px;
            z-index: 0;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            background: #a5f3fc;
            opacity: 0.22;
            filter: blur(90px);
            border-radius: 50%;
            right: -100px;
            bottom: 30px;
            z-index: 0;
        }

        .hero {
            position: relative;
            z-index: 1;

            width: 90%;
            max-width: 1150px;
            margin: auto;

            min-height: 620px;

            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 50px;
            align-items: center;
        }

        .hero-content {
            animation: slideUp 0.7s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge {
            display: inline-block;
            padding: 7px 14px;
            background: rgba(238,242,255,0.9);
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .hero h1 {
            font-size: clamp(42px, 5vw, 62px);
            line-height: 1.1;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
            color: #111827;
        }

        .hero h1 span {
            color: #4f46e5;
        }

        .hero p {
            max-width: 600px;
            color: #64748b;
            font-size: 17px;
            margin-bottom: 28px;
        }

        .hero-buttons {
            display: flex;
            gap: 12px;
        }

        .primary-btn,
        .secondary-btn {
            padding: 13px 22px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 14px;
            transition: 0.3s;
        }

        .primary-btn {
            background: #4f46e5;
            color: white;
            box-shadow: 0 8px 20px rgba(79,70,229,0.22);
        }

        .primary-btn:hover {
            background: #4338ca;
            transform: translateY(-2px);
        }

        .secondary-btn {
            color: #334155;
            border: 1px solid #cbd5e1;
            background: rgba(255,255,255,0.85);
        }

        .secondary-btn:hover {
            background: white;
            transform: translateY(-2px);
        }

        /* ================= HERO CARD ================= */

        .hero-card {
            background: rgba(255,255,255,0.94);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 20px 45px rgba(15,23,42,0.10);

            animation: floating 4s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-7px);
            }
        }

        .card-header {
            margin-bottom: 20px;
        }

        .card-header small {
            color: #64748b;
        }

        .card-header h3 {
            margin-top: 5px;
            font-size: 22px;
            color: #1e293b;
        }

        .match {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            transition: 0.3s;
        }

        .match:hover {
            border-color: #c7d2fe;
            transform: translateX(3px);
        }

        .match-top {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .match-name {
            font-weight: 700;
            font-size: 14px;
        }

        .score {
            color: #4f46e5;
            font-weight: 800;
        }

        .bar {
            height: 7px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .bar div {
            height: 100%;
            background: linear-gradient(
                90deg,
                #4f46e5,
                #06b6d4
            );
            border-radius: 10px;
        }

        .skills {
            margin-top: 9px;
            color: #64748b;
            font-size: 12px;
        }

        /* ================= SECTION ================= */

        section {
            width: 90%;
            max-width: 1150px;
            margin: 0 auto 90px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title span {
            color: #4f46e5;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title h2 {
            font-size: 36px;
            color: #111827;
            margin: 8px 0;
        }

        .section-title p {
            color: #64748b;
            max-width: 600px;
            margin: auto;
        }

        /* ================= FEATURES ================= */

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature {
            background: rgba(255,255,255,0.90);
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            padding: 28px;
            transition: 0.3s;
        }

        .feature:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(15,23,42,0.08);
            border-color: #c7d2fe;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eef2ff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 22px;
            margin-bottom: 18px;
        }

        .feature h3 {
            font-size: 19px;
            margin-bottom: 8px;
        }

        .feature p {
            color: #64748b;
            font-size: 14px;
        }

        /* ================= ROLES ================= */

        #roles {
            width: 100%;
            max-width: none;
            padding: 80px 5%;
            background: rgba(224,231,255,0.45);
        }

        .roles-container {
            width: 100%;
            max-width: 1150px;
            margin: auto;
        }

        .roles {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .role {
            background: rgba(255,255,255,0.95);
            border-radius: 15px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
        }

        .role:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(15,23,42,0.08);
        }

        .role h3 {
            font-size: 21px;
            margin-bottom: 10px;
        }

        .role p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .role ul {
            list-style: none;
            color: #475569;
            font-size: 14px;
        }

        .role li {
            margin: 7px 0;
        }

        .role li::before {
            content: "✓";
            color: #4f46e5;
            font-weight: bold;
            margin-right: 8px;
        }

        /* ================= WORKFLOW ================= */

        .workflow {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .step {
            text-align: center;
            padding: 20px;
        }

        .step-number {
            width: 48px;
            height: 48px;
            background: linear-gradient(
                135deg,
                #4f46e5,
                #6366f1
            );
            color: white;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: auto auto 14px;
            font-weight: 800;
            box-shadow: 0 8px 18px rgba(79,70,229,0.20);
        }

        .step h4 {
            margin-bottom: 6px;
        }

        .step p {
            color: #64748b;
            font-size: 13px;
        }

        /* ================= CTA ================= */

        .cta {
            background: linear-gradient(
                135deg,
                #1e1b4b,
                #312e81
            );

            border-radius: 20px;
            padding: 55px 30px;
            text-align: center;
            color: white;

            box-shadow: 0 20px 40px rgba(30,27,75,0.18);
        }

        .cta h2 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .cta p {
            color: #cbd5e1;
            max-width: 550px;
            margin: 0 auto 25px;
        }

        .cta a {
            display: inline-block;
            background: white;
            color: #4338ca;
            padding: 13px 23px;
            border-radius: 9px;
            font-weight: 700;
            transition: 0.3s;
        }

        .cta a:hover {
            transform: translateY(-2px);
            background: #eef2ff;
        }

        /* ================= FOOTER ================= */

        footer {
            background: #172033;
            color: #94a3b8;
            padding: 28px 5%;
        }

        .footer-content {
            max-width: 1150px;
            margin: auto;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-content strong {
            color: white;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {

            .nav-links {
                display: none;
            }

            .hero {
                grid-template-columns: 1fr;
                padding: 80px 0;
                text-align: center;
            }

            .hero p {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .features,
            .roles {
                grid-template-columns: 1fr;
            }

            .workflow {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero-card {
                max-width: 550px;
                width: 100%;
                margin: auto;
            }
        }

        @media (max-width: 600px) {

            .nav-container {
                height: 62px;
            }

            .logo {
                font-size: 17px;
            }

            .nav-buttons .login-btn {
                display: none;
            }

            .register-btn {
                padding: 9px 13px;
                font-size: 12px;
            }

            .hero {
                min-height: auto;
                padding: 70px 0;
            }

            .hero h1 {
                font-size: 42px;
            }

            .hero p {
                font-size: 15px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .primary-btn,
            .secondary-btn {
                width: 100%;
            }

            .section-title h2 {
                font-size: 30px;
            }

            .workflow {
                grid-template-columns: 1fr 1fr;
            }

            .cta h2 {
                font-size: 30px;
            }

            .footer-content {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }
    </style>
</head>

<body>


<!-- ================= NAVBAR ================= -->

<nav>

    <div class="nav-container">

        <a href="#home" class="logo">
            🚀 Career<span>Connect AI</span>
        </a>

        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#features">Features</a>
            <a href="#roles">Solutions</a>
            <a href="#workflow">How It Works</a>
        </div>

        <div class="nav-buttons">
            <a href="login.php" class="login-btn">
                Login
            </a>

            <a href="register.php" class="register-btn">
                Register
            </a>
        </div>

    </div>

</nav>


<!-- ================= HERO ================= -->

<div id="home" class="hero-section">

    <div class="hero">

        <div class="hero-content">

            <div class="badge">
                ✨ AI-Powered Career Platform
            </div>

            <h1>
                Build Your Career
                With <span>AI</span>
            </h1>

            <p>
                CareerConnect AI connects students, companies and
                colleges through intelligent skill mapping,
                internship matching and placement insights.
            </p>

            <div class="hero-buttons">

                <a href="register.php" class="primary-btn">
                    🚀 Get Started
                </a>

                <a href="#features" class="secondary-btn">
                    Explore Features
                </a>

            </div>

        </div>


        <!-- AI MATCHING CARD -->

        <div class="hero-card">

            <div class="card-header">

                <small>
                    AI Skill Matching
                </small>

                <h3>
                    Recommended Opportunities
                </h3>

            </div>


            <div class="match">

                <div class="match-top">

                    <span class="match-name">
                        PHP Developer Intern
                    </span>

                    <span class="score">
                        92%
                    </span>

                </div>

                <div class="bar">
                    <div style="width:92%;"></div>
                </div>

                <div class="skills">
                    PHP • MySQL • HTML • CSS
                </div>

            </div>


            <div class="match">

                <div class="match-top">

                    <span class="match-name">
                        Web Developer
                    </span>

                    <span class="score">
                        86%
                    </span>

                </div>

                <div class="bar">
                    <div style="width:86%;"></div>
                </div>

                <div class="skills">
                    HTML • CSS • JavaScript
                </div>

            </div>


            <div class="match">

                <div class="match-top">

                    <span class="match-name">
                        Backend Developer
                    </span>

                    <span class="score">
                        78%
                    </span>

                </div>

                <div class="bar">
                    <div style="width:78%;"></div>
                </div>

                <div class="skills">
                    PHP • MySQL • REST API
                </div>

            </div>

        </div>

    </div>

</div>


<!-- ================= FEATURES ================= -->

<section id="features">

    <div class="section-title">

        <span>Our Features</span>

        <h2>
            Everything You Need
        </h2>

        <p>
            A smart ecosystem designed to bridge the gap between
            academic skills and industry opportunities.
        </p>

    </div>


    <div class="features">

        <div class="feature">

            <div class="feature-icon">
                🧠
            </div>

            <h3>
                AI Skill Mapping
            </h3>

            <p>
                Analyze your skills, identify strengths and discover
                suitable career paths based on your profile.
            </p>

        </div>


        <div class="feature">

            <div class="feature-icon">
                🎯
            </div>

            <h3>
                Smart Internship Matching
            </h3>

            <p>
                Find internships that match your skills and improve
                your chances of getting selected.
            </p>

        </div>


        <div class="feature">

            <div class="feature-icon">
                🏆
            </div>

            <h3>
                AI Candidate Ranking
            </h3>

            <p>
                Companies can identify suitable candidates using
                skill-based matching and intelligent ranking.
            </p>

        </div>

    </div>

</section>


<!-- ================= ROLES ================= -->

<section id="roles">

    <div class="roles-container">

        <div class="section-title">

            <span>One Platform</span>

            <h2>
                Built For Everyone
            </h2>

            <p>
                Connecting students, companies and colleges
                through one intelligent platform.
            </p>

        </div>


        <div class="roles">

            <div class="role">

                <h3>
                    🎓 Students
                </h3>

                <p>
                    Build your profile, improve your skills and
                    discover the right opportunities.
                </p>

                <ul>
                    <li>Create Skill Profile</li>
                    <li>AI Skill Analysis</li>
                    <li>Find Internships</li>
                    <li>Resume Analysis</li>
                </ul>

            </div>


            <div class="role">

                <h3>
                    🏢 Companies
                </h3>

                <p>
                    Find skilled candidates and simplify your
                    recruitment process.
                </p>

                <ul>
                    <li>Post Internships & Jobs</li>
                    <li>Candidate Ranking</li>
                    <li>Smart Shortlisting</li>
                    <li>Manage Applicants</li>
                </ul>

            </div>


            <div class="role">

                <h3>
                    🏫 Colleges
                </h3>

                <p>
                    Track student skills, applications and
                    placement performance.
                </p>

                <ul>
                    <li>Student Overview</li>
                    <li>Placement Analytics</li>
                    <li>Application Tracking</li>
                    <li>Industry Insights</li>
                </ul>

            </div>

        </div>

    </div>

</section>


<!-- ================= WORKFLOW ================= -->

<section id="workflow">

    <div class="section-title">

        <span>Simple Process</span>

        <h2>
            How CareerConnect Works
        </h2>

        <p>
            From creating a profile to discovering the right
            career opportunity.
        </p>

    </div>


    <div class="workflow">

        <div class="step">

            <div class="step-number">
                01
            </div>

            <h4>
                Create Profile
            </h4>

            <p>
                Add your education, skills and resume.
            </p>

        </div>


        <div class="step">

            <div class="step-number">
                02
            </div>

            <h4>
                AI Analysis
            </h4>

            <p>
                Analyze your skills and identify gaps.
            </p>

        </div>


        <div class="step">

            <div class="step-number">
                03
            </div>

            <h4>
                Get Matched
            </h4>

            <p>
                Discover suitable internships and jobs.
            </p>

        </div>


        <div class="step">

            <div class="step-number">
                04
            </div>

            <h4>
                Get Selected
            </h4>

            <p>
                Apply, get shortlisted and move forward.
            </p>

        </div>

    </div>

</section>


<!-- ================= CTA ================= -->

<section>

    <div class="cta">

        <h2>
            Ready To Build Your Future?
        </h2>

        <p>
            Join CareerConnect AI and connect your skills
            with the right opportunities.
        </p>

        <a href="register.php">
            🚀 Create Your Account
        </a>

    </div>

</section>


<!-- ================= FOOTER ================= -->

<footer>

    <div class="footer-content">

        <div>
            © 2026 <strong>CareerConnect AI</strong>
        </div>

        <div>
            Developed by <strong>ROHIT</strong>
            • Academia • Industry • AI
        </div>

    </div>

</footer>


</body>
</html>