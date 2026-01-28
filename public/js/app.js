/**
 * ============================================
 * EXAM PORTAL - VANILLA JAVASCRIPT
 * Professional exam system application
 * ============================================
 * 
 * HOW TO SWITCH BETWEEN FAKE API AND REAL API:
 * --------------------------------------------
 * Set USE_FAKE_API = true for mock data (no backend required)
 * Set USE_FAKE_API = false to use real API endpoints
 * 
 * REAL API ENDPOINTS:
 * - GET /api/student/{code} - Get student info
 * - GET /api/student/{code}/exams - Get student's exams
 * - GET /api/exam/{examId} - Get exam questions
 * - POST /api/exam/{examId}/submit - Submit exam answers
 */

// ============================================
// API CONFIGURATION
// ============================================
const USE_FAKE_API = false; // Toggle this to switch between fake and real API

// ============================================
// FAKE DATA MODELS
// ============================================

/**
 * STUDENT DATA MODEL
 * {
 *   code: string,    // Unique student identifier
 *   name: string,    // Full name
 *   batch: string    // Batch/group name
 * }
 */
const fakeStudents = {
  'STU001': { code: 'STU001', name: 'John Smith', batch: 'Computer Science 2024' },
  'STU002': { code: 'STU002', name: 'Emily Johnson', batch: 'Mathematics 2024' },
  'STU003': { code: 'STU003', name: 'Michael Brown', batch: 'Physics 2023' },
  'STU004': { code: 'STU004', name: 'Sarah Davis', batch: 'Computer Science 2024' }
};

/**
 * EXAM DATA MODEL
 * {
 *   id: string,
 *   subject: string,
 *   duration: number (minutes),
 *   totalQuestions: number,
 *   isActive: boolean,
 *   description: string
 * }
 */
const fakeExams = [
  {
    id: 'EXAM001',
    subject: 'Introduction to Programming',
    duration: 30,
    totalQuestions: 5,
    isActive: true,
    description: 'Basic programming concepts including variables, loops, and functions.'
  },
  {
    id: 'EXAM002',
    subject: 'Data Structures',
    duration: 45,
    totalQuestions: 8,
    isActive: false,
    description: 'Arrays, linked lists, stacks, queues, and trees.'
  },
  {
    id: 'EXAM003',
    subject: 'Web Development Fundamentals',
    duration: 25,
    totalQuestions: 6,
    isActive: false,
    description: 'HTML, CSS, and JavaScript basics for web development.'
  },
  {
    id: 'EXAM004',
    subject: 'Database Management',
    duration: 40,
    totalQuestions: 10,
    isActive: false,
    description: 'SQL queries, normalization, and database design principles.'
  }
];

/**
 * QUESTION DATA MODEL
 * {
 *   id: string,
 *   examId: string,
 *   questionText: string,
 *   options: string[],
 *   correctOptionIndex: number,
 *   explanation: string
 * }
 */
const fakeQuestions = {
  'EXAM001': [
    {
      id: 'Q001',
      examId: 'EXAM001',
      questionText: 'What is a variable in programming?',
      options: [
        'A container that stores data values',
        'A type of loop',
        'A function that returns nothing',
        'A comment in code'
      ],
      correctOptionIndex: 0,
      explanation: 'A variable is a named container used to store data values that can be changed during program execution.'
    },
    {
      id: 'Q002',
      examId: 'EXAM001',
      questionText: 'Which of the following is a loop structure?',
      options: [
        'if-else',
        'switch',
        'for',
        'try-catch'
      ],
      correctOptionIndex: 2,
      explanation: 'A "for" loop is a control flow statement that allows code to be executed repeatedly.'
    },
    {
      id: 'Q003',
      examId: 'EXAM001',
      questionText: 'What does a function do in programming?',
      options: [
        'Stores data permanently',
        'Performs a specific task and can be reused',
        'Connects to the internet',
        'Displays graphics'
      ],
      correctOptionIndex: 1,
      explanation: 'Functions are reusable blocks of code that perform specific tasks and can be called multiple times.'
    },
    {
      id: 'Q004',
      examId: 'EXAM001',
      questionText: 'What is the correct syntax for a single-line comment in JavaScript?',
      options: [
        '/* comment */',
        '# comment',
        '// comment',
        '<!-- comment -->'
      ],
      correctOptionIndex: 2,
      explanation: 'In JavaScript, single-line comments start with // and continue to the end of the line.'
    },
    {
      id: 'Q005',
      examId: 'EXAM001',
      questionText: 'Which data type is used to store true or false values?',
      options: [
        'String',
        'Number',
        'Boolean',
        'Array'
      ],
      correctOptionIndex: 2,
      explanation: 'Boolean is a data type that can only hold two values: true or false.'
    }
  ],
  'EXAM002': [
    {
      id: 'Q101',
      examId: 'EXAM002',
      questionText: 'What is the time complexity of accessing an element in an array by index?',
      options: ['O(1)', 'O(n)', 'O(log n)', 'O(n^2)'],
      correctOptionIndex: 0,
      explanation: 'Array access by index is O(1) because we can directly compute the memory address.'
    },
    {
      id: 'Q102',
      examId: 'EXAM002',
      questionText: 'Which data structure follows LIFO (Last In First Out) principle?',
      options: ['Queue', 'Stack', 'Array', 'Linked List'],
      correctOptionIndex: 1,
      explanation: 'Stack follows LIFO - the last element added is the first one to be removed.'
    },
    {
      id: 'Q103',
      examId: 'EXAM002',
      questionText: 'What is a linked list?',
      options: [
        'A collection of elements stored at contiguous memory locations',
        'A linear data structure where elements are connected via pointers',
        'A tree-like data structure',
        'A hash-based data structure'
      ],
      correctOptionIndex: 1,
      explanation: 'A linked list consists of nodes where each node contains data and a reference to the next node.'
    },
    {
      id: 'Q104',
      examId: 'EXAM002',
      questionText: 'Which data structure follows FIFO (First In First Out) principle?',
      options: ['Stack', 'Queue', 'Tree', 'Graph'],
      correctOptionIndex: 1,
      explanation: 'Queue follows FIFO - the first element added is the first one to be removed.'
    },
    {
      id: 'Q105',
      examId: 'EXAM002',
      questionText: 'What is the maximum number of children a binary tree node can have?',
      options: ['1', '2', '3', 'Unlimited'],
      correctOptionIndex: 1,
      explanation: 'In a binary tree, each node can have at most two children (left and right).'
    },
    {
      id: 'Q106',
      examId: 'EXAM002',
      questionText: 'Which operation adds an element to a stack?',
      options: ['Enqueue', 'Dequeue', 'Push', 'Pop'],
      correctOptionIndex: 2,
      explanation: 'Push adds an element to the top of a stack.'
    },
    {
      id: 'Q107',
      examId: 'EXAM002',
      questionText: 'What is the time complexity of inserting at the beginning of a linked list?',
      options: ['O(1)', 'O(n)', 'O(log n)', 'O(n^2)'],
      correctOptionIndex: 0,
      explanation: 'Inserting at the beginning of a linked list is O(1) as we only need to update the head pointer.'
    },
    {
      id: 'Q108',
      examId: 'EXAM002',
      questionText: 'What type of tree has all leaves at the same level?',
      options: ['Binary Tree', 'Complete Binary Tree', 'Perfect Binary Tree', 'Skewed Tree'],
      correctOptionIndex: 2,
      explanation: 'A perfect binary tree has all leaves at the same level and all internal nodes have two children.'
    }
  ],
  'EXAM003': [
    {
      id: 'Q201',
      examId: 'EXAM003',
      questionText: 'What does HTML stand for?',
      options: [
        'Hyper Text Markup Language',
        'High Tech Modern Language',
        'Home Tool Markup Language',
        'Hyperlink Text Making Language'
      ],
      correctOptionIndex: 0,
      explanation: 'HTML stands for Hyper Text Markup Language, the standard markup language for creating web pages.'
    },
    {
      id: 'Q202',
      examId: 'EXAM003',
      questionText: 'Which CSS property changes text color?',
      options: ['text-color', 'font-color', 'color', 'text-style'],
      correctOptionIndex: 2,
      explanation: 'The "color" property in CSS is used to set the text color of an element.'
    },
    {
      id: 'Q203',
      examId: 'EXAM003',
      questionText: 'What is the correct HTML tag for the largest heading?',
      options: ['<heading>', '<h6>', '<head>', '<h1>'],
      correctOptionIndex: 3,
      explanation: '<h1> defines the most important heading. <h6> defines the least important heading.'
    },
    {
      id: 'Q204',
      examId: 'EXAM003',
      questionText: 'Which JavaScript method adds an element to the end of an array?',
      options: ['push()', 'pop()', 'shift()', 'unshift()'],
      correctOptionIndex: 0,
      explanation: 'The push() method adds one or more elements to the end of an array.'
    },
    {
      id: 'Q205',
      examId: 'EXAM003',
      questionText: 'What does CSS stand for?',
      options: [
        'Creative Style Sheets',
        'Cascading Style Sheets',
        'Computer Style Sheets',
        'Colorful Style Sheets'
      ],
      correctOptionIndex: 1,
      explanation: 'CSS stands for Cascading Style Sheets, used to style and layout web pages.'
    },
    {
      id: 'Q206',
      examId: 'EXAM003',
      questionText: 'Which HTML attribute specifies an alternate text for an image?',
      options: ['title', 'src', 'alt', 'longdesc'],
      correctOptionIndex: 2,
      explanation: 'The alt attribute provides alternative text for an image if it cannot be displayed.'
    }
  ],
  'EXAM004': [
    {
      id: 'Q301',
      examId: 'EXAM004',
      questionText: 'What does SQL stand for?',
      options: [
        'Structured Query Language',
        'Simple Question Language',
        'Standard Query Logic',
        'Sequential Query List'
      ],
      correctOptionIndex: 0,
      explanation: 'SQL stands for Structured Query Language, used for managing relational databases.'
    },
    {
      id: 'Q302',
      examId: 'EXAM004',
      questionText: 'Which SQL command is used to retrieve data from a database?',
      options: ['GET', 'FETCH', 'SELECT', 'RETRIEVE'],
      correctOptionIndex: 2,
      explanation: 'SELECT is used to retrieve data from one or more tables in a database.'
    },
    {
      id: 'Q303',
      examId: 'EXAM004',
      questionText: 'What is a primary key?',
      options: [
        'A key that opens the database',
        'A unique identifier for each record in a table',
        'The first column in a table',
        'A foreign reference'
      ],
      correctOptionIndex: 1,
      explanation: 'A primary key uniquely identifies each record in a database table.'
    },
    {
      id: 'Q304',
      examId: 'EXAM004',
      questionText: 'Which SQL clause is used to filter records?',
      options: ['FILTER', 'WHERE', 'HAVING', 'SELECT'],
      correctOptionIndex: 1,
      explanation: 'WHERE clause is used to filter records based on specified conditions.'
    },
    {
      id: 'Q305',
      examId: 'EXAM004',
      questionText: 'What is normalization in databases?',
      options: [
        'Making data smaller',
        'Organizing data to reduce redundancy',
        'Encrypting data',
        'Backing up data'
      ],
      correctOptionIndex: 1,
      explanation: 'Normalization is the process of organizing data to minimize redundancy and dependency.'
    },
    {
      id: 'Q306',
      examId: 'EXAM004',
      questionText: 'Which SQL command is used to insert new data?',
      options: ['ADD', 'INSERT INTO', 'APPEND', 'CREATE'],
      correctOptionIndex: 1,
      explanation: 'INSERT INTO is used to add new records to a database table.'
    },
    {
      id: 'Q307',
      examId: 'EXAM004',
      questionText: 'What is a foreign key?',
      options: [
        'A key from another country',
        'A key that references the primary key of another table',
        'An encrypted key',
        'A temporary key'
      ],
      correctOptionIndex: 1,
      explanation: 'A foreign key is a field that creates a link between two tables by referencing the primary key of another table.'
    },
    {
      id: 'Q308',
      examId: 'EXAM004',
      questionText: 'Which SQL command removes all records from a table?',
      options: ['DELETE', 'REMOVE', 'TRUNCATE', 'DROP'],
      correctOptionIndex: 2,
      explanation: 'TRUNCATE removes all rows from a table but keeps the table structure.'
    },
    {
      id: 'Q309',
      examId: 'EXAM004',
      questionText: 'What does JOIN do in SQL?',
      options: [
        'Combines rows from two or more tables',
        'Merges databases',
        'Connects to a server',
        'Creates a new table'
      ],
      correctOptionIndex: 0,
      explanation: 'JOIN clause is used to combine rows from two or more tables based on a related column.'
    },
    {
      id: 'Q310',
      examId: 'EXAM004',
      questionText: 'Which SQL command is used to modify existing data?',
      options: ['MODIFY', 'CHANGE', 'UPDATE', 'ALTER'],
      correctOptionIndex: 2,
      explanation: 'UPDATE is used to modify existing records in a table.'
    }
  ]
};

/**
 * EXAM RESULT DATA MODEL
 * {
 *   id: string,
 *   studentCode: string,
 *   examId: string,
 *   examSubject: string,
 *   score: number,
 *   totalQuestions: number,
 *   answers: AnswerRecord[],
 *   submittedAt: string (ISO date)
 * }
 * 
 * ANSWER RECORD:
 * {
 *   questionId: string,
 *   questionText: string,
 *   options: string[],
 *   selectedOptionIndex: number,
 *   correctOptionIndex: number,
 *   isCorrect: boolean,
 *   explanation: string
 * }
 */
let fakeResults = {
  'STU001': [
    {
      id: 'RES001',
      studentCode: 'STU001',
      examId: 'EXAM002',
      examSubject: 'Data Structures',
      score: 6,
      totalQuestions: 8,
      submittedAt: '2024-11-15T10:30:00Z',
      answers: [
        { questionId: 'Q101', questionText: 'What is the time complexity of accessing an element in an array by index?', options: ['O(1)', 'O(n)', 'O(log n)', 'O(n^2)'], selectedOptionIndex: 0, correctOptionIndex: 0, isCorrect: true },
        { questionId: 'Q102', questionText: 'Which data structure follows LIFO (Last In First Out) principle?', options: ['Queue', 'Stack', 'Array', 'Linked List'], selectedOptionIndex: 1, correctOptionIndex: 1, isCorrect: true },
        { questionId: 'Q103', questionText: 'What is a linked list?', options: ['A collection of elements stored at contiguous memory locations', 'A linear data structure where elements are connected via pointers', 'A tree-like data structure', 'A hash-based data structure'], selectedOptionIndex: 1, correctOptionIndex: 1, isCorrect: true },
        { questionId: 'Q104', questionText: 'Which data structure follows FIFO (First In First Out) principle?', options: ['Stack', 'Queue', 'Tree', 'Graph'], selectedOptionIndex: 0, correctOptionIndex: 1, isCorrect: false },
        { questionId: 'Q105', questionText: 'What is the maximum number of children a binary tree node can have?', options: ['1', '2', '3', 'Unlimited'], selectedOptionIndex: 1, correctOptionIndex: 1, isCorrect: true },
        { questionId: 'Q106', questionText: 'Which operation adds an element to a stack?', options: ['Enqueue', 'Dequeue', 'Push', 'Pop'], selectedOptionIndex: 2, correctOptionIndex: 2, isCorrect: true },
        { questionId: 'Q107', questionText: 'What is the time complexity of inserting at the beginning of a linked list?', options: ['O(1)', 'O(n)', 'O(log n)', 'O(n^2)'], selectedOptionIndex: 1, correctOptionIndex: 0, isCorrect: false },
        { questionId: 'Q108', questionText: 'What type of tree has all leaves at the same level?', options: ['Binary Tree', 'Complete Binary Tree', 'Perfect Binary Tree', 'Skewed Tree'], selectedOptionIndex: 2, correctOptionIndex: 2, isCorrect: true }
      ]
    },
    {
      id: 'RES002',
      studentCode: 'STU001',
      examId: 'EXAM003',
      examSubject: 'Web Development Fundamentals',
      score: 5,
      totalQuestions: 6,
      submittedAt: '2024-11-10T14:15:00Z',
      answers: [
        { questionId: 'Q201', questionText: 'What does HTML stand for?', options: ['Hyper Text Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlink Text Making Language'], selectedOptionIndex: 0, correctOptionIndex: 0, isCorrect: true },
        { questionId: 'Q202', questionText: 'Which CSS property changes text color?', options: ['text-color', 'font-color', 'color', 'text-style'], selectedOptionIndex: 2, correctOptionIndex: 2, isCorrect: true },
        { questionId: 'Q203', questionText: 'What is the correct HTML tag for the largest heading?', options: ['<heading>', '<h6>', '<head>', '<h1>'], selectedOptionIndex: 3, correctOptionIndex: 3, isCorrect: true },
        { questionId: 'Q204', questionText: 'Which JavaScript method adds an element to the end of an array?', options: ['push()', 'pop()', 'shift()', 'unshift()'], selectedOptionIndex: 1, correctOptionIndex: 0, isCorrect: false },
        { questionId: 'Q205', questionText: 'What does CSS stand for?', options: ['Creative Style Sheets', 'Cascading Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets'], selectedOptionIndex: 1, correctOptionIndex: 1, isCorrect: true },
        { questionId: 'Q206', questionText: 'Which HTML attribute specifies an alternate text for an image?', options: ['title', 'src', 'alt', 'longdesc'], selectedOptionIndex: 2, correctOptionIndex: 2, isCorrect: true }
      ]
    }
  ],
  'STU002': [],
  'STU003': [],
  'STU004': []
};

// ============================================
// FAKE API LAYER
// ============================================

/**
 * Simulates API call delay for realistic UX
 */
function simulateDelay(ms = 800) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Fake API object with methods that return Promises
 * simulating real API calls
 */
const fakeApi = {
  /**
   * Get student by code
   * @param {string} code - Student code
   * @returns {Promise<{success: boolean, data?: object, error?: string}>}
   */
  async getStudent(code) {
    await simulateDelay();
    const student = fakeStudents[code.toUpperCase()];
    if (student) {
      return { success: true, data: student };
    }
    return { success: false, error: 'Student not found. Please check your code and try again.' };
  },

  /**
   * Get student's available and taken exams
   * @param {string} code - Student code
   * @returns {Promise<{success: boolean, data?: object}>}
   */
  async getStudentExams(code) {
    await simulateDelay(500);
    
    const takenExamIds = (fakeResults[code.toUpperCase()] || []).map(r => r.examId);
    const available = fakeExams.filter(e => !takenExamIds.includes(e.id));
    const taken = fakeResults[code.toUpperCase()] || [];
    
    return { success: true, data: { available, taken } };
  },

  /**
   * Get exam questions
   * @param {string} examId - Exam ID
   * @returns {Promise<{success: boolean, data?: object[], error?: string}>}
   */
  async getExamQuestions(examId) {
    await simulateDelay(600);
    const questions = fakeQuestions[examId];
    if (questions) {
      // Return questions without correct answers
      const safeQuestions = questions.map(q => ({
        id: q.id,
        examId: q.examId,
        questionText: q.questionText,
        options: q.options
      }));
      return { success: true, data: safeQuestions };
    }
    return { success: false, error: 'Exam not found.' };
  },

  /**
   * Submit exam answers
   * @param {string} examId - Exam ID
   * @param {string} studentCode - Student code
   * @param {Array} answers - Array of {questionId, selectedOptionIndex}
   * @returns {Promise<{success: boolean, data?: object}>}
   */
  async submitExam(examId, studentCode, answers) {
    await simulateDelay(1000);
    const exam = fakeExams.find(e => e.id === examId);
    const questions = fakeQuestions[examId];
    
    if (!exam || !questions) {
      return { success: false, error: 'Exam not found.' };
    }

    // Calculate score and build answer records
    const answerRecords = questions.map(q => {
      const userAnswer = answers.find(a => a.questionId === q.id);
      const selectedIndex = userAnswer ? userAnswer.selectedOptionIndex : -1;
      return {
        questionId: q.id,
        questionText: q.questionText,
        options: q.options,
        selectedOptionIndex: selectedIndex,
        correctOptionIndex: q.correctOptionIndex,
        isCorrect: selectedIndex === q.correctOptionIndex,
        explanation: q.explanation
      };
    });

    const score = answerRecords.filter(a => a.isCorrect).length;

    const result = {
      id: 'RES' + Date.now(),
      studentCode: studentCode.toUpperCase(),
      examId,
      examSubject: exam.subject,
      score,
      totalQuestions: questions.length,
      answers: answerRecords,
      submittedAt: new Date().toISOString()
    };

    // Store result
    if (!fakeResults[studentCode.toUpperCase()]) {
      fakeResults[studentCode.toUpperCase()] = [];
    }
    fakeResults[studentCode.toUpperCase()].unshift(result);

    return { success: true, data: result };
  }
};

// ============================================
// REAL API LAYER
// Replace these endpoints with your actual backend URLs
// ============================================
const realApi = {
  async getStudent(code) {
    try {
      const response = await fetch(`/api/student/${code}`);
      const data = await response.json();
      return data;
    } catch (error) {
      return { success: false, error: 'Failed to connect to server.' };
    }
  },

  async getStudentExams(code) {
    try {
      const response = await fetch(`/api/student/${code}/exams`);
      const data = await response.json();
      return data;
    } catch (error) {
      return { success: false, error: 'Failed to fetch exams.' };
    }
  },

  async getExamQuestions(examId) {
    try {
      const response = await fetch(`/api/exam/${examId}`);
      const data = await response.json();
      return data;
    } catch (error) {
      return { success: false, error: 'Failed to fetch exam questions.' };
    }
  },

  async submitExam(examId, studentCode, answers) {
    try {
      const response = await fetch(`/api/exam/${examId}/submit`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ studentCode, answers })
      });
      const data = await response.json();
      return data;
    } catch (error) {
      return { success: false, error: 'Failed to submit exam.' };
    }
  }
};

// Select API based on configuration
const api = USE_FAKE_API ? fakeApi : realApi;

// ============================================
// APPLICATION STATE
// ============================================
let appState = {
  currentStudent: null,
  studentExams: null,
  currentExam: null,
  currentQuestions: [],
  currentQuestionIndex: 0,
  answers: new Map(),
  timerInterval: null,
  timeRemaining: 0,
  theme: localStorage.getItem('theme') || 'light'
};

// ============================================
// DOM ELEMENTS
// ============================================
const elements = {
  // Sections
  landingSection: document.getElementById('landing-section'),
  dashboardSection: document.getElementById('dashboard-section'),
  examSection: document.getElementById('exam-section'),
  
  // Login
  loginForm: document.getElementById('login-form'),
  studentCodeInput: document.getElementById('student-code'),
  loginBtn: document.getElementById('login-btn'),
  loginError: document.getElementById('login-error'),
  
  // Dashboard
  userName: document.getElementById('user-name'),
  userAvatar: document.getElementById('user-avatar'),
  logoutBtn: document.getElementById('logout-btn'),
  themeToggle: document.getElementById('theme-toggle'),
  themeIcon: document.getElementById('theme-icon'),
  
  // Tabs
  tabBtns: document.querySelectorAll('.tab-btn'),
  examsTab: document.getElementById('exams-tab'),
  resultsTab: document.getElementById('results-tab'),
  profileTab: document.getElementById('profile-tab'),
  
  // Exams
  activeExamsSection: document.getElementById('active-exams-section'),
  activeExamsGrid: document.getElementById('active-exams-grid'),
  availableExamsSection: document.getElementById('available-exams-section'),
  availableExamsGrid: document.getElementById('available-exams-grid'),
  noExams: document.getElementById('no-exams'),
  
  // Results
  resultsList: document.getElementById('results-list'),
  noResults: document.getElementById('no-results'),
  
  // Profile
  profileName: document.getElementById('profile-name'),
  profileCode: document.getElementById('profile-code'),
  profileBatch: document.getElementById('profile-batch'),
  profileAvatar: document.getElementById('profile-avatar'),
  
  // Exam Taking
  questionCounter: document.getElementById('question-counter'),
  timerBadge: document.getElementById('timer-badge'),
  timerDisplay: document.getElementById('timer-display'),
  examProgressBar: document.getElementById('exam-progress-bar'),
  progressText: document.getElementById('progress-text'),
  questionText: document.getElementById('question-text'),
  optionsContainer: document.getElementById('options-container'),
  questionNavGrid: document.getElementById('question-nav-grid'),
  prevQuestionBtn: document.getElementById('prev-question-btn'),
  nextQuestionBtn: document.getElementById('next-question-btn'),
  submitExamBtn: document.getElementById('submit-exam-btn'),
  exitExamBtn: document.getElementById('exit-exam-btn'),
  
  // Modal
  modalOverlay: document.getElementById('modal-overlay'),
  modal: document.getElementById('modal'),
  modalIcon: document.getElementById('modal-icon'),
  modalTitle: document.getElementById('modal-title'),
  modalMessage: document.getElementById('modal-message'),
  modalCancel: document.getElementById('modal-cancel'),
  modalConfirm: document.getElementById('modal-confirm')
};

// ============================================
// UTILITY FUNCTIONS
// ============================================
function getInitials(name) {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function formatTime(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function showSection(section) {
  [elements.landingSection, elements.dashboardSection, elements.examSection].forEach(s => {
    s.classList.remove('active');
    s.classList.add('hidden');
  });
  section.classList.remove('hidden');
  section.classList.add('active');
}

function setLoading(button, loading) {
  const btnText = button.querySelector('.btn-text');
  const icon = button.querySelector('.material-icons-round');
  const loader = button.querySelector('.loader');
  
  if (loading) {
    button.disabled = true;
    if (btnText) btnText.classList.add('hidden');
    if (icon) icon.classList.add('hidden');
    if (loader) loader.classList.remove('hidden');
  } else {
    button.disabled = false;
    if (btnText) btnText.classList.remove('hidden');
    if (icon) icon.classList.remove('hidden');
    if (loader) loader.classList.add('hidden');
  }
}

function showError(element, message) {
  element.textContent = message;
  element.classList.remove('hidden');
}

function hideError(element) {
  element.classList.add('hidden');
}

// ============================================
// THEME MANAGEMENT
// ============================================
function initTheme() {
  if (appState.theme === 'dark') {
    document.body.classList.add('dark');
    elements.themeIcon.textContent = 'light_mode';
  } else {
    document.body.classList.remove('dark');
    elements.themeIcon.textContent = 'dark_mode';
  }
}

function toggleTheme() {
  appState.theme = appState.theme === 'light' ? 'dark' : 'light';
  localStorage.setItem('theme', appState.theme);
  initTheme();
}

// ============================================
// MODAL FUNCTIONS
// ============================================
function showModal(options) {
  return new Promise((resolve) => {
    elements.modalIcon.textContent = options.icon || 'info';
    elements.modalIcon.className = `material-icons-round modal-icon ${options.iconClass || 'info'}`;
    elements.modalTitle.textContent = options.title;
    elements.modalMessage.innerHTML = options.message;
    elements.modalCancel.textContent = options.cancelText || 'Cancel';
    elements.modalConfirm.textContent = options.confirmText || 'Confirm';
    elements.modalConfirm.className = `btn ${options.confirmClass || 'btn-primary'}`;
    
    elements.modalOverlay.classList.remove('hidden');
    
    const handleConfirm = () => {
      elements.modalOverlay.classList.add('hidden');
      cleanup();
      resolve(true);
    };
    
    const handleCancel = () => {
      elements.modalOverlay.classList.add('hidden');
      cleanup();
      resolve(false);
    };
    
    const cleanup = () => {
      elements.modalConfirm.removeEventListener('click', handleConfirm);
      elements.modalCancel.removeEventListener('click', handleCancel);
    };
    
    elements.modalConfirm.addEventListener('click', handleConfirm);
    elements.modalCancel.addEventListener('click', handleCancel);
  });
}

// ============================================
// LOGIN FUNCTIONS
// ============================================
async function handleLogin(e) {
  e.preventDefault();
  hideError(elements.loginError);
  
  const code = elements.studentCodeInput.value.trim();
  
  if (!code) {
    showError(elements.loginError, 'Please enter your student code');
    return;
  }
  
  if (code.length < 3) {
    showError(elements.loginError, 'Student code must be at least 3 characters');
    return;
  }
  
  setLoading(elements.loginBtn, true);
  
  const result = await api.getStudent(code);
  
  if (result.success) {
    
    appState.currentStudent = result.data;
    await loadStudentExams();
    renderDashboard();
    showSection(elements.dashboardSection);
    elements.studentCodeInput.value = '';
  } else {
    showError(elements.loginError, result.error);
  }
  
  setLoading(elements.loginBtn, false);
}

function handleLogout() {
  appState.currentStudent = null;
  appState.studentExams = null;
  appState.currentExam = null;
  appState.currentQuestions = [];
  appState.answers.clear();
  clearInterval(appState.timerInterval);
  showSection(elements.landingSection);
}

// ============================================
// DASHBOARD FUNCTIONS
// ============================================
async function loadStudentExams() {
  const result = await api.getStudentExams(appState.currentStudent.code);
  if (result.success) {    
    appState.studentExams = result.data;
  }
}

function renderDashboard() {
  // Update header
  elements.userName.textContent = appState.currentStudent.name;
  elements.userAvatar.textContent = getInitials(appState.currentStudent.name);
  
  // Render all tabs
  renderExamsTab();
  renderResultsTab();
  renderProfileTab();
}

function renderExamsTab() {
  const { available } = appState.studentExams;
  
  const activeExams = available.filter(e => e.isActive);
  const regularExams = available.filter(e => !e.isActive);
  
  if (available.length === 0) {
    elements.activeExamsSection.classList.add('hidden');
    elements.availableExamsSection.classList.add('hidden');
    elements.noExams.classList.remove('hidden');
    return;
  }
  
  elements.noExams.classList.add('hidden');
  
  // Active exams
  if (activeExams.length > 0) {
    elements.activeExamsSection.classList.remove('hidden');
    elements.activeExamsGrid.innerHTML = activeExams.map(exam => createExamCard(exam, true)).join('');
  } else {
    elements.activeExamsSection.classList.add('hidden');
  }
  
  // Regular exams
  if (regularExams.length > 0) {
    elements.availableExamsSection.classList.remove('hidden');
    elements.availableExamsGrid.innerHTML = regularExams.map(exam => createExamCard(exam, false)).join('');
  } else {
    elements.availableExamsSection.classList.add('hidden');
  }
  
  // Add event listeners
  document.querySelectorAll('.start-exam-btn').forEach(btn => {
    btn.addEventListener('click', () => startExam(btn.dataset.examId));
  });
}

function createExamCard(exam, isActive) {
  return `
    <div class="exam-card ${isActive ? 'active' : ''}" data-testid="card-exam-${exam.id}">
      <div class="exam-card-header">
        <h3 class="exam-card-title">${exam.subject}</h3>
        ${isActive ? '<span class="badge badge-primary">Active</span>' : ''}
      </div>
      ${exam.description ? `<p class="exam-card-description">${exam.description}</p>` : ''}
      <div class="exam-card-meta">
        <div class="meta-item">
          <span class="material-icons-round">schedule</span>
          <span>${exam.duration} min</span>
        </div>
        <div class="meta-item">
          <span class="material-icons-round">quiz</span>
          <span>${exam.totalQuestions} questions</span>
        </div>
      </div>
      <div class="exam-card-footer">
        <button class="btn ${isActive ? 'btn-primary' : 'btn-secondary'} btn-full start-exam-btn" 
                data-exam-id="${exam.id}"
                data-testid="button-start-exam-${exam.id}">
          <span class="material-icons-round">play_arrow</span>
          <span>${isActive ? 'Take Exam Now' : 'Start Exam'}</span>
        </button>
      </div>
    </div>
  `;
}

function renderResultsTab() {
  const { taken } = appState.studentExams;
  
  if (taken.length === 0) {
    elements.resultsList.classList.add('hidden');
    elements.noResults.classList.remove('hidden');
    return;
  }
  
  elements.noResults.classList.add('hidden');
  elements.resultsList.classList.remove('hidden');
  elements.resultsList.innerHTML = taken.map(result => createResultCard(result)).join('');
  
  // Add event listeners for collapsible
  document.querySelectorAll('.result-header').forEach(header => {
    header.addEventListener('click', () => {
      const card = header.closest('.result-card');
      card.classList.toggle('expanded');
    });
  });
}

function createResultCard(result) {
  const percentage = Math.round((result.score / result.totalQuestions) * 100);
  const passed = percentage >= 50;
  
  return `
    <div class="result-card" data-testid="card-result-${result.id}">
      <div class="result-header">
        <div class="result-info">
          <h3 class="result-subject">${result.examSubject}</h3>
          <p class="result-date">${formatDate(result.submittedAt)}</p>
        </div>
        <div class="result-score">
          <div class="score-value ${passed ? 'pass' : 'fail'}">${result.score}/${result.totalQuestions}</div>
          <div class="score-label">${percentage}%</div>
        </div>
        <div class="result-toggle">
          <span class="material-icons-round">expand_more</span>
        </div>
      </div>
      <div class="result-details">
        <div class="question-breakdown">
          ${result.answers.map((answer, index) => createBreakdownItem(answer, index + 1)).join('')}
        </div>
      </div>
    </div>
  `;
}

function createBreakdownItem(answer, number) {
  const correctAnswer = answer.options[answer.correctOptionIndex];
  const studentAnswer = answer.selectedOptionIndex >= 0 ? answer.options[answer.selectedOptionIndex] : 'Not answered';
  
  return `
    <div class="breakdown-item ${answer.isCorrect ? 'correct' : 'wrong'}">
      <div class="breakdown-question">
        <span class="question-number">${number}</span>
        <span>${answer.questionText}</span>
      </div>
      <div class="breakdown-answers">
        <div class="answer-row">
          <span class="answer-label">Your answer:</span>
          <span class="answer-value ${answer.isCorrect ? 'correct' : 'wrong'}">${studentAnswer}</span>
        </div>
        ${!answer.isCorrect ? `
          <div class="answer-row">
            <span class="answer-label">Correct answer:</span>
            <span class="answer-value correct">${correctAnswer}</span>
          </div>
        ` : ''}
      </div>
    </div>
  `;
}

function renderProfileTab() {
  const student = appState.currentStudent;
  elements.profileName.textContent = student.name;
  elements.profileCode.textContent = student.code;
  elements.profileBatch.textContent = student.batch;
  elements.profileAvatar.textContent = getInitials(student.name);
}

// Tab switching
function switchTab(tabName) {
  elements.tabBtns.forEach(btn => {
    btn.classList.toggle('active', btn.dataset.tab === tabName);
  });
  
  [elements.examsTab, elements.resultsTab, elements.profileTab].forEach(tab => {
    tab.classList.remove('active');
    tab.classList.add('hidden');
  });
  
  document.getElementById(`${tabName}-tab`).classList.add('active');
  document.getElementById(`${tabName}-tab`).classList.remove('hidden');
}

// ============================================
// EXAM TAKING FUNCTIONS
// ============================================
async function startExam(examId) {
  const exam = appState.studentExams.available.find(e => e.id == examId);
  
  if (!exam) return;
  
  // Find the button and show loading
  const btn = document.querySelector(`[data-exam-id="${examId}"]`);
  if (btn) btn.disabled = true;
  
  const result = await api.getExamQuestions(examId);
  
  if (btn) btn.disabled = false;
  
  if (result.success) {
    appState.currentExam = exam;
    appState.currentQuestions = result.data;
    appState.currentQuestionIndex = 0;
    appState.answers.clear();
    appState.timeRemaining = exam.duration * 60;
    
    showSection(elements.examSection);
    startTimer();
    renderQuestion();
    renderQuestionNav();
    updateProgress();
  }
}

function startTimer() {
  clearInterval(appState.timerInterval);
  
  appState.timerInterval = setInterval(() => {
    appState.timeRemaining--;
    
    if (appState.timeRemaining <= 60) {
      elements.timerBadge.classList.add('warning');
    }
    
    elements.timerDisplay.textContent = formatTime(appState.timeRemaining);
    
    if (appState.timeRemaining <= 0) {
      clearInterval(appState.timerInterval);
      submitExam(true);
    }
  }, 1000);
}

function renderQuestion() {
  const question = appState.currentQuestions[appState.currentQuestionIndex];
  const selectedAnswer = appState.answers.get(question.id);
  
  elements.questionCounter.textContent = `Question ${appState.currentQuestionIndex + 1} of ${appState.currentQuestions.length}`;
  elements.questionText.textContent = question.questionText;
    
  elements.optionsContainer.innerHTML = question.options.map((option, index) => `
    <button class="option-btn ${selectedAnswer === index ? 'selected' : ''}" 
            data-index="${index}"
            data-testid="button-option-${index}">
      <span class="option-letter">${String.fromCharCode(65 + index)}</span>
      <span class="option-text">${option}</span>
    </button>
  `).join('');
  
  // Add event listeners
  document.querySelectorAll('.option-btn').forEach(btn => {
    btn.addEventListener('click', () => selectAnswer(parseInt(btn.dataset.index)));
  });
  
  // Update navigation buttons
  elements.prevQuestionBtn.disabled = appState.currentQuestionIndex === 0;
  elements.nextQuestionBtn.disabled = appState.currentQuestionIndex === appState.currentQuestions.length - 1;
}

function selectAnswer(optionIndex) {
  const question = appState.currentQuestions[appState.currentQuestionIndex];
  appState.answers.set(question.id, optionIndex);
  renderQuestion();
  renderQuestionNav();
  updateProgress();
}

function renderQuestionNav() {
  elements.questionNavGrid.innerHTML = appState.currentQuestions.map((q, index) => {
    const isAnswered = appState.answers.has(q.id);
    const isCurrent = index === appState.currentQuestionIndex;
    
    return `
      <button class="question-nav-btn ${isCurrent ? 'current' : ''} ${isAnswered ? 'answered' : ''}"
              data-index="${index}"
              data-testid="button-question-nav-${index}">
        ${index + 1}
      </button>
    `;
  }).join('');
  
  document.querySelectorAll('.question-nav-btn').forEach(btn => {
    btn.addEventListener('click', () => goToQuestion(parseInt(btn.dataset.index)));
  });
}

function goToQuestion(index) {
  if (index >= 0 && index < appState.currentQuestions.length) {
    appState.currentQuestionIndex = index;
    renderQuestion();
    renderQuestionNav();
  }
}

function updateProgress() {
  const answered = appState.answers.size;
  const total = appState.currentQuestions.length;
  const percent = (answered / total) * 100;
  
  elements.examProgressBar.style.width = `${percent}%`;
  elements.progressText.textContent = `${answered} of ${total} answered`;
}

async function confirmExitExam() {
  const confirmed = await showModal({
    icon: 'warning',
    iconClass: 'warning',
    title: 'Exit Exam?',
    message: 'Are you sure you want to exit? Your progress will be lost and this action cannot be undone.',
    cancelText: 'Continue Exam',
    confirmText: 'Exit Exam',
    confirmClass: 'btn-danger'
  });
  
  if (confirmed) {
    exitExam();
  }
}

function exitExam() {
  clearInterval(appState.timerInterval);
  appState.currentExam = null;
  appState.currentQuestions = [];
  appState.answers.clear();
  elements.timerBadge.classList.remove('warning');
  showSection(elements.dashboardSection);
}

async function confirmSubmitExam() {
  const answered = appState.answers.size;
  const total = appState.currentQuestions.length;
  const unanswered = total - answered;
  
  let message = `You have answered ${answered} out of ${total} questions.`;
  if (unanswered > 0) {
    message += `<br><br><strong style="color: var(--color-danger);">Warning: You have ${unanswered} unanswered question(s).</strong>`;
  }
  
  const confirmed = await showModal({
    icon: 'check_circle',
    iconClass: 'success',
    title: 'Submit Exam?',
    message,
    cancelText: 'Review Answers',
    confirmText: 'Submit Exam',
    confirmClass: 'btn-primary'
  });
  
  if (confirmed) {
    submitExam(false);
  }
}

async function submitExam(autoSubmit = false) {
  clearInterval(appState.timerInterval);
  
  const answers = appState.currentQuestions.map(q => ({
    questionId: q.id,
    selectedOptionIndex: appState.answers.get(q.id) ?? -1
  }));
  
  elements.submitExamBtn.disabled = true;
  elements.submitExamBtn.innerHTML = '<span class="loader"></span>';
  
  const result = await api.submitExam(
    appState.currentExam.id,
    appState.currentStudent.code,
    answers
  );
  
  if (result.success) {
    // Refresh exams list
    await loadStudentExams();
    
    // Reset state
    appState.currentExam = null;
    appState.currentQuestions = [];
    appState.answers.clear();
    elements.timerBadge.classList.remove('warning');
    
    // Go back to dashboard and show results
    renderDashboard();
    switchTab('results');
    showSection(elements.dashboardSection);
  }
  
  elements.submitExamBtn.disabled = false;
  elements.submitExamBtn.innerHTML = '<span class="material-icons-round">send</span><span>Submit Exam</span>';
}

// ============================================
// EVENT LISTENERS
// ============================================
function initEventListeners() {
  // Login form
  elements.loginForm.addEventListener('submit', handleLogin);
  
  // Logout
  elements.logoutBtn.addEventListener('click', handleLogout);
  
  // Theme toggle
  elements.themeToggle.addEventListener('click', toggleTheme);
  
  // Tab switching
  elements.tabBtns.forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.dataset.tab));
  });
  
  // Exam navigation
  elements.prevQuestionBtn.addEventListener('click', () => {
    goToQuestion(appState.currentQuestionIndex - 1);
  });
  
  elements.nextQuestionBtn.addEventListener('click', () => {
    goToQuestion(appState.currentQuestionIndex + 1);
  });
  
  // Exam actions
  elements.exitExamBtn.addEventListener('click', confirmExitExam);
  elements.submitExamBtn.addEventListener('click', confirmSubmitExam);
}

// ============================================
// INITIALIZATION
// ============================================
function init() {
  initTheme();
  initEventListeners();
  showSection(elements.landingSection);
  
  // Focus on student code input
  elements.studentCodeInput.focus();
  

}

// Start the application
document.addEventListener('DOMContentLoaded', init);
