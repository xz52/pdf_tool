<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Professional exam portal for students to take exams and view results">
    <title>Exam Portal - Student Assessment System</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    <div id="app">
        <div class="layer"></div>
        <!-- Landing Section - Student Code Entry -->
        <section id="landing-section" class="section active">
            <div class="landing-container">

                <div class="landing-header">

                    <div class="logo-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="" srcset="">
                    </div>
                    <h1>Exam Portal</h1>
                    <p>Enter your student code to access your exams and results</p>
                </div>

                <div class="login-card">
                    <div class="card-header">
                        <h2>Student Login</h2>
                        <p>Enter the code provided by your institution</p>
                    </div>
                    <form id="login-form" class="login-form">
                        <div class="form-group">
                            <label for="student-code">Student Code</label>
                            <input type="text" id="student-code" name="studentCode" placeholder="e.g., STU001"
                                autocomplete="off" data-testid="input-student-code">
                        </div>
                        <div id="login-error" class="error-message hidden" data-testid="text-error-message"></div>
                        <button type="submit" class="btn btn-primary btn-full" id="login-btn"
                            data-testid="button-login">
                            <span class="btn-text">Access Portal</span>
                            <span class="material-icons-round">arrow_forward</span>
                            <span class="loader hidden"></span>
                        </button>
                    </form>
                </div>

                <p class="help-text">Contact your institution if you don't have a student code</p>
            </div>
        </section>

        <!-- Dashboard Section -->
        <section id="dashboard-section" class="section hidden">
            <!-- Header -->
            <header class="dashboard-header">
                <div class="header-content">
                    <div class="header-left">
                        <span class="material-icons-round header-logo">menu_book</span>
                        <span class="header-title">Exam Portal</span>
                    </div>
                    <div class="header-right">
                        <button class="btn-icon" id="theme-toggle" data-testid="button-theme-toggle"
                            aria-label="Toggle theme">
                            <span class="material-icons-round" id="theme-icon">dark_mode</span>
                        </button>
                        <div class="user-info">
                            <div class="avatar" id="user-avatar" data-testid="text-user-avatar"></div>
                            <span class="user-name" id="user-name" data-testid="text-student-name"></span>
                        </div>
                        <button class="btn-icon" id="logout-btn" data-testid="button-logout" aria-label="Logout">
                            <span class="material-icons-round">logout</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Tab Navigation -->
            <nav class="tab-nav">
                <div class="tab-list">
                    <button class="tab-btn active" data-tab="exams" data-testid="tab-exams">
                        <span class="material-icons-round">assignment</span>
                        <span class="tab-text">Exams</span>
                    </button>
                    <button class="tab-btn" data-tab="results" data-testid="tab-results">
                        <span class="material-icons-round">assessment</span>
                        <span class="tab-text">Results</span>
                    </button>
                    <button class="tab-btn" data-tab="profile" data-testid="tab-profile">
                        <span class="material-icons-round">person</span>
                        <span class="tab-text">Profile</span>
                    </button>
                </div>
            </nav>

            <!-- Tab Content -->
            <main class="dashboard-main">
                <!-- Exams Tab -->
                <div id="exams-tab" class="tab-content active" data-testid="section-exams">
                    <div id="active-exams-section" class="exams-section hidden">
                        <div class="section-header">
                            <span class="material-icons-round section-icon">priority_high</span>
                            <h2>Active Exams</h2>
                        </div>
                        <div id="active-exams-grid" class="exams-grid"></div>
                    </div>

                    <div id="available-exams-section" class="exams-section">
                        <div class="section-header">
                            <h2>Available Exams</h2>
                        </div>
                        <div id="available-exams-grid" class="exams-grid"></div>
                    </div>

                    <div id="no-exams" class="empty-state hidden" data-testid="empty-exams">
                        <span class="material-icons-round empty-icon">description</span>
                        <h3>No Available Exams</h3>
                        <p>There are no exams available for you at the moment. Check back later or contact your
                            institution.</p>
                    </div>
                </div>

                <!-- Results Tab -->
                <div id="results-tab" class="tab-content hidden" data-testid="section-results">
                    <div id="results-list" class="results-list"></div>
                    <div id="no-results" class="empty-state hidden" data-testid="empty-results">
                        <span class="material-icons-round empty-icon">quiz</span>
                        <h3>No Results Yet</h3>
                        <p>You haven't completed any exams yet. Take an exam to see your results here.</p>
                    </div>
                </div>

                <!-- Profile Tab -->
                <div id="profile-tab" class="tab-content hidden" data-testid="section-profile">
                    <div class="profile-card">
                        <div class="profile-header">
                            <div class="profile-avatar" id="profile-avatar"></div>
                            <h2 id="profile-name" data-testid="text-profile-name"></h2>
                        </div>
                        <div class="profile-info">
                            <div class="info-item">
                                <span class="material-icons-round">badge</span>
                                <div class="info-content">
                                    <span class="info-label">Student Code</span>
                                    <span class="info-value" id="profile-code"
                                        data-testid="text-profile-code"></span>
                                </div>
                            </div>
                            <div class="info-item">
                                <span class="material-icons-round">groups</span>
                                <div class="info-content">
                                    <span class="info-label">Batch</span>
                                    <span class="info-value" id="profile-batch"
                                        data-testid="text-profile-batch"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </section>

        <!-- Exam Taking Section -->
        <section id="exam-section" class="section hidden">
            <!-- Exam Header -->
            <header class="exam-header">
                <div class="exam-header-content">
                    <div class="exam-progress-info">
                        <span id="question-counter" data-testid="text-question-counter">Question 1 of 10</span>
                    </div>
                    <div class="exam-header-right">
                        <div class="timer-badge" id="timer-badge" data-testid="badge-timer">
                            <span class="material-icons-round">schedule</span>
                            <span id="timer-display">30:00</span>
                        </div>
                        <button class="btn-icon" id="exit-exam-btn" data-testid="button-exit-exam"
                            aria-label="Exit exam">
                            <span class="material-icons-round">close</span>
                        </button>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" id="exam-progress-bar"></div>
                </div>
                <p class="progress-text" id="progress-text">0 of 10 answered</p>
            </header>

            <!-- Question Area -->
            <main class="exam-main">
                <div class="question-card">
                    <h2 id="question-text" class="question-text" data-testid="text-question"></h2>
                    <div id="options-container" class="options-container"></div>
                </div>

                <!-- Question Navigation Grid -->
                <div class="question-nav-grid" id="question-nav-grid"></div>
            </main>

            <!-- Exam Footer -->
            <footer class="exam-footer">
                <div class="exam-footer-content">
                    <button class="btn btn-outline" id="prev-question-btn" data-testid="button-prev-question">
                        <span class="material-icons-round">chevron_left</span>
                        <span class="btn-text-desktop">Previous</span>
                    </button>
                    <button class="btn btn-primary" id="submit-exam-btn" data-testid="button-submit-exam">
                        <span class="material-icons-round">send</span>
                        <span>Submit Exam</span>
                    </button>
                    <button class="btn btn-outline" id="next-question-btn" data-testid="button-next-question">
                        <span class="btn-text-desktop">Next</span>
                        <span class="material-icons-round">chevron_right</span>
                    </button>
                </div>
            </footer>
        </section>
    </div>

    <!-- Modal Overlay -->
    <div id="modal-overlay" class="modal-overlay hidden">
        <div class="modal" id="modal">
            <div class="modal-header">
                <span class="material-icons-round modal-icon" id="modal-icon"></span>
                <h3 id="modal-title"></h3>
            </div>
            <p id="modal-message"></p>
            <div class="modal-actions">
                <button class="btn btn-outline" id="modal-cancel" data-testid="button-modal-cancel">Cancel</button>
                <button class="btn" id="modal-confirm" data-testid="button-modal-confirm">Confirm</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
