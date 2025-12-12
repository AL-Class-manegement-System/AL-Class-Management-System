<?php 

include('../includes/connection.php'); 
include('../includes/header.php'); 
?>
    <title>Madushan Neranjan - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        /* General Reset and Base Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
            color: #333;
        }

        .profile-header {
            background-color: #fff;
            padding: 15px 5%;
            border-bottom: 1px solid #eee;
        }

        .back-link {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #6a1b9a; /* Using the profile's main color for hover */
        }

        /* Main Layout */
        .profile-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 15px;
        }

        /* Profile Card Styling (Detailed Version) */
        .profile-card.detailed {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            padding: 30px;
            margin-bottom: 30px;
            align-items: center;
        }

        .profile-photo-section {
            position: relative;
            margin-right: 40px;
            /* Ensure the image container does not shrink unnecessarily */
            flex-shrink: 0; 
        }

        .profile-image-large {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f4f6f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .subject-tag.large {
            position: absolute;
            bottom: 5px; /* Adjust to sit slightly lower for better visibility */
            left: 50%;
            transform: translateX(-50%);
            background-color: #6a1b9a; /* Purple color from the original card */
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            white-space: nowrap; /* Prevent tag from wrapping */
        }

        .profile-details-section h1 {
            margin: 0 0 5px 0;
            font-size: 2em;
            color: #1a1a1a;
        }

        .profile-details-section h2 {
            margin: 0 0 15px 0;
            font-size: 1.1em;
            color: #555;
            font-weight: 400;
            letter-spacing: 1px;
        }

        .qualification {
            font-size: 1.1em;
            color: #007bff; /* Highlight the University/Qualification */
            margin-bottom: 25px;
        }

        /* Contact Buttons */
        .contact-links a {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            margin-right: 15px;
            margin-top: 10px;
            display: inline-block;
            transition: background-color 0.3s, transform 0.1s;
        }
        
        .contact-links a:active {
            transform: scale(0.98);
        }

        .contact-btn i {
            margin-right: 8px;
        }

        .email {
            background-color: #f1f1f1;
            color: #333;
            border: 1px solid #ddd;
        }
        .email:hover { background-color: #e0e0e0; }

        .phone {
            background-color: #007bff;
            color: white;
        }
        .phone:hover { background-color: #0056b3; }

        .whatsapp {
            background-color: #25D366;
            color: white;
        }
        .whatsapp:hover { background-color: #1FAF59; }


        /* Profile Sections (Academic, Experience, etc.) */
        .profile-sections {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .section-block {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px dashed #eee;
        }
        .section-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-block h3 {
            color: #6a1b9a;
            font-size: 1.5em;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .section-block ul {
            list-style: none;
            padding-left: 0;
        }

        .section-block ul li {
            padding: 5px 0;
            line-height: 1.6;
        }

        .section-block p {
            line-height: 1.6;
            color: #555;
            margin-top: 0;
        }

        /* Rating Display */
        .rating-display {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .rating-number {
            font-size: 2em;
            font-weight: 700;
            color: #ff9800;
            margin-right: 10px;
        }

        .stars i {
            color: #ff9800;
            margin-right: 2px;
        }

        .review-count {
            color: #888;
            margin-left: 10px;
        }

        .review-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }
        .review-button:hover {
            background-color: #0056b3;
        }

        /* Footer Styling */
        .profile-footer {
            text-align: center;
            padding: 20px 0;
            margin-top: 20px;
            font-size: 0.9em;
            color: #888;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .profile-card.detailed {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .profile-photo-section {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .subject-tag.large {
                /* Adjust tag position for centered layout */
                top: auto;
                bottom: -10px; 
            }

            .contact-links {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
            }
            
            .contact-links a {
                margin: 5px 0;
                width: 80%; /* Make buttons full width */
                text-align: center;
            }
        }
    </style>
    </head>
<body>
    <header class="profile-header">
        <a href="#" class="back-link"><i class="fas fa-arrow-left"></i> Back to Courses</a>
    </header>
    
    <main class="profile-container">
        
        <div class="profile-card detailed">
            <div class="profile-photo-section">
                <img src="image_bcad92.png" alt="Madushan Neranjan" class="profile-image-large">
                <span class="subject-tag large">Combined Maths</span>
            </div>
            
            <div class="profile-details-section">
                <h1>Madushan Neranjan</h1>
                <h2>LECTURER</h2>
                
                <p class="qualification">
                   BSc (Hons) in T&LM Faculty of Engineering | University of Moratuwa
                </p>
                
                <div class="contact-links">
                    <a href="#" class="contact-btn email"><i class="fas fa-envelope"></i> Email</a>
                    <a href="#" class="contact-btn phone"><i class="fas fa-phone"></i> Call</a>
                    <a href="#" class="contact-btn whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </div>
            </div>
        </div>

        <section class="profile-sections">
            
            <div class="section-block">
                <h3><i class="fas fa-graduation-cap"></i> Academic Background</h3>
                <ul>
                    <li>**Degree:** BSc (Hons) in Transport & Logistics Management (T&LM)</li>
                    <li>**University:** University of Moratuwa, Sri Lanka</li>
                    <li>**Specialization:** Applied Mathematics, Fluid Dynamics, Operational Research</li>
                </ul>
            </div>
            
            <div class="section-block">
                <h3><i class="fas fa-chalkboard-user"></i> Teaching Experience</h3>
                <p>
                    Over 5 years of experience in teaching Combined Mathematics for Advanced Level students. Specializes in simplified conceptual delivery and effective problem-solving techniques for tough examination questions.
                </p>
                <br>
                <p>
                    **Focus Areas:** Pure Mathematics (Calculus, Trigonometry), Applied Mathematics (Mechanics, Statistics).
                </p>
            </div>

            <div class="section-block">
                <h3><i class="fas fa-star"></i> Student Feedback & Rating</h3>
                <div class="rating-display">
                    <span class="rating-number">4.8 / 5.0</span>
                    <span class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </span>
                    <span class="review-count">(250+ Reviews)</span>
                </div>
                <button class="review-button">Read All Reviews</button>
            </div>
            
        </section>

    </main>

    <?php include('../includes/footer.php'); ?>