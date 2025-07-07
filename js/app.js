// Page Navigation
function showPage(pageId) {
    document.querySelectorAll('.page-content').forEach(page => {
        page.classList.remove('active');
    });
    document.getElementById(pageId).classList.add('active');
}

// Dashboard Section Navigation
function showDashboardSection(sectionId) {
    document.querySelectorAll('.dashboard-section').forEach(section => {
        section.classList.remove('active');
    });
    document.getElementById(sectionId + '-section').classList.add('active');

    // Update sidebar active state
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    event.target.closest('.sidebar-link').classList.add('active');
}

// Toggle Sidebar (Mobile)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    sidebar.classList.toggle('show');
    overlay.classList.toggle('show');
    
    // Prevent body scroll when sidebar is open on mobile
    if (sidebar.classList.contains('show')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Handle Login
function handleLogin(event) {
    event.preventDefault();
    showPage('dashboard-page');
}

// Handle Signup
function handleSignup(event) {
    event.preventDefault();
    showPage('dashboard-page');
}

// Initialize Charts when dashboard loads
function initializeCharts() {
    // Call Volume Chart
    const callVolumeCtx = document.getElementById('callVolumeChart');
    if (callVolumeCtx) {
        new Chart(callVolumeCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Calls Made',
                    data: [245, 312, 289, 342, 365, 180, 120],
                    borderColor: '#5B5FDE',
                    backgroundColor: 'rgba(91, 95, 222, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Successful Connections',
                    data: [165, 210, 195, 232, 248, 122, 81],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Agent Status Chart
    const agentStatusCtx = document.getElementById('agentStatusChart');
    if (agentStatusCtx) {
        new Chart(agentStatusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Available', 'On Call', 'Break', 'Offline'],
                datasets: [{
                    data: [12, 8, 3, 2],
                    backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#6B7280']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Call Outcomes Chart (Analytics)
    const callOutcomesCtx = document.getElementById('callOutcomesChart');
    if (callOutcomesCtx) {
        new Chart(callOutcomesCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Connected', 'Voicemail', 'No Answer', 'Busy/Invalid'],
                datasets: [{
                    data: [68.5, 18.2, 8.7, 4.6],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#6B7280']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // AI Performance Chart (Analytics)
    const aiPerformanceCtx = document.getElementById('aiPerformanceChart');
    if (aiPerformanceCtx) {
        new Chart(aiPerformanceCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['AI Agent 1', 'AI Agent 2', 'AI Agent 3', 'AI Agent 4', 'AI Agent 5'],
                datasets: [{
                    label: 'Calls Handled',
                    data: [234, 198, 156, 142, 89],
                    backgroundColor: '#5B5FDE'
                }, {
                    label: 'Successful Transfers',
                    data: [89, 76, 58, 45, 32],
                    backgroundColor: '#10B981'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Dialer Mode Selection
document.addEventListener('click', function(e) {
    if (e.target.closest('.dialer-mode-card')) {
        document.querySelectorAll('.dialer-mode-card').forEach(card => {
            card.classList.remove('active');
        });
        e.target.closest('.dialer-mode-card').classList.add('active');
    }
});

// Power Dialer Variables
let powerDialerState = {
    isDialing: false,
    isPaused: false,
    currentCall: null,
    callList: [],
    currentIndex: 0,
    callTimer: null,
    callStartTime: null,
    connectedCount: 1224,
    failedCount: 0,
    remainingCount: 23
};

// Power Dialer Functions
function initializePowerDialer() {
    // Start Dialing Button
    document.getElementById('startDialingBtn')?.addEventListener('click', function() {
        if (powerDialerState.callList.length === 0) {
            alert('Please upload a contact list first');
            return;
        }
        startDialing();
    });

    // Pause Dialing Button
    document.getElementById('pauseDialingBtn')?.addEventListener('click', function() {
        pauseDialing();
    });

    // Stop Dialing Button
    document.getElementById('stopDialingBtn')?.addEventListener('click', function() {
        stopDialing();
    });

    // Skip Call Button
    document.getElementById('skipCallBtn')?.addEventListener('click', function() {
        skipCurrentCall();
    });

    // File Upload
    document.getElementById('fileUpload')?.addEventListener('change', function(e) {
        handleFileUpload(e.target.files[0]);
    });

    // Upload and Validate Button
    document.getElementById('uploadAndValidateBtn')?.addEventListener('click', function() {
        validateAndUpload();
    });
}

function startDialing() {
    powerDialerState.isDialing = true;
    powerDialerState.isPaused = false;
    
    document.getElementById('startDialingBtn').disabled = true;
    document.getElementById('pauseDialingBtn').disabled = false;
    document.getElementById('stopDialingBtn').disabled = false;
    document.getElementById('skipCallBtn').disabled = false;
    
    updateCallStatus('Starting dialer...', 'Initializing VoIP connection');
    
    // Simulate starting the first call
    setTimeout(() => {
        dialNextNumber();
    }, 2000);
}

function pauseDialing() {
    powerDialerState.isPaused = true;
    document.getElementById('pauseDialingBtn').disabled = true;
    document.getElementById('startDialingBtn').disabled = false;
    updateCallStatus('Dialer Paused', 'Click "Start Dialing" to resume');
}

function stopDialing() {
    powerDialerState.isDialing = false;
    powerDialerState.isPaused = false;
    powerDialerState.currentCall = null;
    
    document.getElementById('startDialingBtn').disabled = false;
    document.getElementById('pauseDialingBtn').disabled = true;
    document.getElementById('stopDialingBtn').disabled = true;
    document.getElementById('skipCallBtn').disabled = true;
    
    updateCallStatus('Dialer Stopped', 'Ready to start dialing');
    resetCallInterface();
}

function dialNextNumber() {
    if (!powerDialerState.isDialing || powerDialerState.isPaused) return;
    
    if (powerDialerState.currentIndex >= powerDialerState.callList.length) {
        // All numbers dialed
        stopDialing();
        updateCallStatus('Dialing Complete', 'All numbers have been called');
        return;
    }

    const contact = powerDialerState.callList[powerDialerState.currentIndex];
    powerDialerState.currentCall = contact;
    
    updateCallInterface(contact);
    updateCallStatus('Calling...', `Dialing ${contact.phone}`);
    
    // Simulate call connection
    setTimeout(() => {
        simulateCallConnection(contact);
    }, 3000);
}

function simulateCallConnection(contact) {
    const outcomes = ['connected', 'no-answer', 'busy', 'voicemail'];
    const outcome = outcomes[Math.floor(Math.random() * outcomes.length)];
    
    if (outcome === 'connected') {
        powerDialerState.connectedCount++;
        updateCallStatus('Connected', `Talking to ${contact.name}`);
        startCallTimer();
        enableCallControls();
        addCallLog(`Connected to ${contact.name} (${contact.phone})`);
    } else {
        powerDialerState.failedCount++;
        updateCallStatus('Call Ended', `${outcome.replace('-', ' ')}`);
        addCallLog(`Call ended - ${outcome.replace('-', ' ')} (0:00)`);
        
        // Move to next number after delay
        setTimeout(() => {
            powerDialerState.currentIndex++;
            dialNextNumber();
        }, 2000);
    }
    
    updateCounters();
}

function skipCurrentCall() {
    if (powerDialerState.currentCall) {
        addCallLog(`Skipped ${powerDialerState.currentCall.name} (${powerDialerState.currentCall.phone})`);
        powerDialerState.currentIndex++;
        resetCallInterface();
        setTimeout(() => {
            dialNextNumber();
        }, 1000);
    }
}

function startCallTimer() {
    powerDialerState.callStartTime = Date.now();
    powerDialerState.callTimer = setInterval(() => {
        const elapsed = Date.now() - powerDialerState.callStartTime;
        const minutes = Math.floor(elapsed / 60000);
        const seconds = Math.floor((elapsed % 60000) / 1000);
        document.getElementById('callTimer').textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }, 1000);
}

function stopCallTimer() {
    if (powerDialerState.callTimer) {
        clearInterval(powerDialerState.callTimer);
        powerDialerState.callTimer = null;
    }
}

function updateCallStatus(status, details) {
    document.getElementById('callStatus').textContent = status;
    document.getElementById('callStatusDetails').textContent = details;
}

function updateCallInterface(contact) {
    document.getElementById('contactName').textContent = contact.name;
    document.getElementById('contactCompany').textContent = contact.company || '';
    document.getElementById('contactPhone').textContent = contact.phone;
    document.getElementById('currentNumber').textContent = contact.phone;
    
    // Update avatar
    const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(contact.name)}&background=5B5FDE&color=fff&size=100`;
    document.getElementById('contactAvatar').src = avatarUrl;
}

function resetCallInterface() {
    document.getElementById('callTimer').textContent = '00:00';
    document.getElementById('contactName').textContent = '--';
    document.getElementById('contactCompany').textContent = '';
    document.getElementById('contactPhone').textContent = '--';
    document.getElementById('currentNumber').textContent = '--';
    disableCallControls();
    stopCallTimer();
}

function enableCallControls() {
    document.getElementById('answerBtn').disabled = false;
    document.getElementById('hangupBtn').disabled = false;
    document.getElementById('pauseBtn').disabled = false;
    document.getElementById('transferBtn').disabled = false;
    document.getElementById('muteBtn').disabled = false;
    document.getElementById('recordBtn').disabled = false;
    document.getElementById('keypadBtn').disabled = false;
    document.getElementById('speakerBtn').disabled = false;
    document.getElementById('saveNotesBtn').disabled = false;
    document.getElementById('markInterestedBtn').disabled = false;
}

function disableCallControls() {
    document.getElementById('answerBtn').disabled = true;
    document.getElementById('hangupBtn').disabled = true;
    document.getElementById('pauseBtn').disabled = true;
    document.getElementById('transferBtn').disabled = true;
    document.getElementById('muteBtn').disabled = true;
    document.getElementById('recordBtn').disabled = true;
    document.getElementById('keypadBtn').disabled = true;
    document.getElementById('speakerBtn').disabled = true;
    document.getElementById('saveNotesBtn').disabled = true;
    document.getElementById('markInterestedBtn').disabled = true;
}

function updateCounters() {
    document.getElementById('connectedCount').textContent = powerDialerState.connectedCount;
    document.getElementById('failedCount').textContent = powerDialerState.failedCount;
    document.getElementById('remainingCount').textContent = powerDialerState.remainingCount - powerDialerState.currentIndex;
}

function addCallLog(message) {
    const logContainer = document.getElementById('callLog');
    const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    const logEntry = document.createElement('div');
    logEntry.className = 'log-entry mb-2';
    logEntry.innerHTML = `
        <small class="text-secondary">${time}</small>
        <p class="mb-1">${message}</p>
    `;
    logContainer.insertBefore(logEntry, logContainer.firstChild);
    
    // Keep only last 10 entries
    while (logContainer.children.length > 10) {
        logContainer.removeChild(logContainer.lastChild);
    }
}

function handleFileUpload(file) {
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        // Simulate file processing
        setTimeout(() => {
            showValidationResults();
        }, 1000);
    };
    reader.readAsText(file);
}

function showValidationResults() {
    document.getElementById('validationResults').style.display = 'block';
    document.getElementById('validCount').textContent = '1245';
    document.getElementById('invalidCount').textContent = '2';
    document.getElementById('dncCount').textContent = '0';
    document.getElementById('totalCount').textContent = '1247';
}

function validateAndUpload() {
    // Simulate validation and upload
    const modal = bootstrap.Modal.getInstance(document.getElementById('uploadListModal'));
    modal.hide();
    
    // Populate call list with sample data
    powerDialerState.callList = [
        { name: 'John Smith', phone: '+1 (555) 123-4567', company: 'Acme Corporation' },
        { name: 'Sarah Wilson', phone: '+1 (555) 987-6543', company: 'Tech Solutions' },
        { name: 'Mike Johnson', phone: '+1 (555) 456-7890', company: 'Sales Pro' },
        { name: 'Lisa Brown', phone: '+1 (555) 321-6540', company: 'Marketing Inc' }
    ];
    
    powerDialerState.currentIndex = 0;
    powerDialerState.remainingCount = powerDialerState.callList.length;
    
    updateCounters();
    addCallLog('Contact list uploaded successfully (1247 numbers)');
}

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeCharts, 100);
    initializePowerDialer();
}); 