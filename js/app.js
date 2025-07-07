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

// Show dashboard section logic
function showDashboardSection(section) {
    document.querySelectorAll('.dashboard-section').forEach(sec => sec.style.display = 'none');
    document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
    if (section === 'overview') {
        document.getElementById('overview-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('overview')\"]").classList.add('active');
    } else if (section === 'upload-contacts') {
        document.getElementById('upload-contacts-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('upload-contacts')\"]").classList.add('active');
    } else if (section === 'crm-connection') {
        document.getElementById('crm-connection-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('crm-connection')\"]").classList.add('active');
    } else if (section === 'manual-dial') {
        document.getElementById('manual-dial-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('manual-dial')\"]").classList.add('active');
    } else if (section === 'dialing-ratio') {
        document.getElementById('dialing-ratio-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('dialing-ratio')\"]").classList.add('active');
    } else if (section === 'caller-id') {
        document.getElementById('caller-id-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('caller-id')\"]").classList.add('active');
    } else if (section === 'call-queue') {
        document.getElementById('call-queue-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('call-queue')\"]").classList.add('active');
    } else if (section === 'call-recordings') {
        document.getElementById('call-recordings-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('call-recordings')\"]").classList.add('active');
    } else if (section === 'call-dispositions') {
        document.getElementById('call-dispositions-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('call-dispositions')\"]").classList.add('active');
    } else if (section === 'agent-assist') {
        document.getElementById('agent-assist-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('agent-assist')\"]").classList.add('active');
    } else if (section === 'ai-agents') {
        document.getElementById('ai-agents-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('ai-agents')\"]").classList.add('active');
    } else if (section === 'voice-selection') {
        document.getElementById('voice-selection-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('voice-selection')\"]").classList.add('active');
    } else if (section === 'conversation-designer') {
        document.getElementById('conversation-designer-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('conversation-designer')\"]").classList.add('active');
    } else if (section === 'reports-history') {
        document.getElementById('reports-history-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('reports-history')\"]").classList.add('active');
    } else if (section === 'call-summarization') {
        document.getElementById('call-summarization-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('call-summarization')\"]").classList.add('active');
    } else if (section === 'consent-capture') {
        document.getElementById('consent-capture-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('consent-capture')\"]").classList.add('active');
    } else if (section === 'dnc-management') {
        document.getElementById('dnc-management-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('dnc-management')\"]").classList.add('active');
    } else if (section === 'access-controls') {
        document.getElementById('access-controls-section').style.display = '';
        document.querySelector("[onclick=\"showDashboardSection('access-controls')\"]").classList.add('active');
    } else {
        const secId = section + '-section';
        const secEl = document.getElementById(secId);
        if (secEl) secEl.style.display = '';
        document.querySelector(`.sidebar-link[onclick="showDashboardSection('${section}')"]`).classList.add('active');
    }
}

// Upload Contacts Form Logic
const uploadForm = document.getElementById('uploadContactsForm');
if (uploadForm) {
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const fileInput = document.getElementById('contactsFile');
        const progress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('uploadProgressBar');
        const message = document.getElementById('uploadMessage');
        if (!fileInput.files.length) return;
        progress.classList.remove('d-none');
        progressBar.style.width = '0%';
        message.textContent = '';
        // Mock upload progress
        let percent = 0;
        const interval = setInterval(() => {
            percent += 10;
            progressBar.style.width = percent + '%';
            if (percent >= 100) {
                clearInterval(interval);
                message.innerHTML = '<span class="text-success">Contacts uploaded successfully!</span>';
                progress.classList.add('d-none');
                fileInput.value = '';
            }
        }, 150);
    });
}

// CRM Connection Form Logic
const crmForm = document.getElementById('crmConnectionForm');
if (crmForm) {
    crmForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const provider = document.getElementById('crmProvider').value;
        const apiKey = document.getElementById('crmApiKey').value;
        const status = document.getElementById('crmConnectionStatus');
        status.innerHTML = '';
        if (!provider || !apiKey) {
            status.innerHTML = '<span class="text-danger">Please select a provider and enter credentials.</span>';
            return;
        }
        status.innerHTML = '<span class="text-info">Connecting...</span>';
        setTimeout(() => {
            status.innerHTML = '<span class="text-success">Connected to ' + provider.charAt(0).toUpperCase() + provider.slice(1) + ' successfully!</span>';
        }, 1200);
    });
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

// Manual Dial Pad Logic
function renderManualDialPad() {
    const pad = document.getElementById('manualDialPad');
    if (!pad) return;
    const buttons = [
        '1','2','3',
        '4','5','6',
        '7','8','9',
        '*','0','#'
    ];
    pad.innerHTML = '';
    buttons.forEach(val => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-outline-secondary m-1 fs-4';
        btn.style.width = '60px';
        btn.style.height = '60px';
        btn.textContent = val;
        btn.title = 'Dial ' + val;
        btn.onclick = () => {
            const input = document.getElementById('manualDialInput');
            if (input.value.length < 15) input.value += val;
        };
        pad.appendChild(btn);
    });
    // Add backspace button
    const delBtn = document.createElement('button');
    delBtn.type = 'button';
    delBtn.className = 'btn btn-outline-danger m-1 fs-4';
    delBtn.style.width = '60px';
    delBtn.style.height = '60px';
    delBtn.innerHTML = '<i class="bi bi-backspace"></i>';
    delBtn.title = 'Backspace';
    delBtn.onclick = () => {
        const input = document.getElementById('manualDialInput');
        input.value = input.value.slice(0, -1);
    };
    pad.appendChild(delBtn);
}

function setupManualDial() {
    renderManualDialPad();
    const callBtn = document.getElementById('manualDialCallBtn');
    const input = document.getElementById('manualDialInput');
    const status = document.getElementById('manualDialStatus');
    if (callBtn && input && status) {
        callBtn.onclick = function() {
            const number = input.value;
            status.innerHTML = '';
            if (!number || number.length < 5) {
                status.innerHTML = '<span class="text-danger">Enter a valid number.</span>';
                return;
            }
            status.innerHTML = '<span class="text-info">Dialing ' + number + '...</span>';
            setTimeout(() => {
                status.innerHTML = '<span class="text-success">Call connected!</span>';
                input.value = '';
            }, 1200);
        };
    }
}

// Re-setup manual dial pad when section is shown
const observer = new MutationObserver(() => {
    const manualDialSection = document.getElementById('manual-dial-section');
    if (manualDialSection && manualDialSection.style.display !== 'none') {
        setupManualDial();
    }
});
observer.observe(document.body, { childList: true, subtree: true });

// Dialing Ratio Form Logic
const dialingRatioForm = document.getElementById('dialingRatioForm');
if (dialingRatioForm) {
    const status = document.getElementById('dialingRatioStatus');
    const select = document.getElementById('dialingRatioSelect');
    // Load current setting from localStorage if available
    if (localStorage.getItem('dialingRatio')) {
        select.value = localStorage.getItem('dialingRatio');
        status.innerHTML = `<span class='text-success'>Current dialing ratio: ${select.value}:1</span>`;
    }
    dialingRatioForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const ratio = select.value;
        localStorage.setItem('dialingRatio', ratio);
        status.innerHTML = `<span class='text-success'>Dialing ratio set to ${ratio}:1 successfully!</span>`;
    });
}

// Caller ID Management Logic
function renderCallerIdList() {
    const list = document.getElementById('callerIdList');
    if (!list) return;
    let ids = JSON.parse(localStorage.getItem('callerIds') || '[]');
    list.innerHTML = '';
    if (ids.length === 0) {
        list.innerHTML = '<li class="list-group-item text-muted">No Caller IDs added yet.</li>';
        return;
    }
    ids.forEach((item, idx) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `<span>${item.id}</span>`;
        const toggle = document.createElement('input');
        toggle.type = 'checkbox';
        toggle.className = 'form-check-input ms-2';
        toggle.checked = item.enabled;
        toggle.title = item.enabled ? 'Disable Caller ID' : 'Enable Caller ID';
        toggle.onchange = function() {
            ids[idx].enabled = toggle.checked;
            localStorage.setItem('callerIds', JSON.stringify(ids));
            renderCallerIdList();
        };
        li.appendChild(toggle);
        list.appendChild(li);
    });
}

function setupCallerIdManagement() {
    renderCallerIdList();
    const form = document.getElementById('addCallerIdForm');
    const input = document.getElementById('newCallerIdInput');
    const status = document.getElementById('callerIdStatus');
    if (form && input && status) {
        form.onsubmit = function(e) {
            e.preventDefault();
            let ids = JSON.parse(localStorage.getItem('callerIds') || '[]');
            const val = input.value.trim();
            if (!/^\+?\d{10,15}$/.test(val)) {
                status.innerHTML = '<span class="text-danger">Enter a valid Caller ID (e.g. +1XXXXXXXXXX).</span>';
                return;
            }
            if (ids.some(i => i.id === val)) {
                status.innerHTML = '<span class="text-warning">Caller ID already exists.</span>';
                return;
            }
            ids.push({ id: val, enabled: true });
            localStorage.setItem('callerIds', JSON.stringify(ids));
            input.value = '';
            status.innerHTML = '<span class="text-success">Caller ID added and enabled!</span>';
            renderCallerIdList();
        };
    }
}

// Re-setup caller ID management when section is shown
const callerIdObserver = new MutationObserver(() => {
    const section = document.getElementById('caller-id-section');
    if (section && section.style.display !== 'none') {
        setupCallerIdManagement();
    }
});
callerIdObserver.observe(document.body, { childList: true, subtree: true });

// Call Queue & Routing Logic
const mockAgents = [
    'Unassigned', 'Alice Smith', 'Bob Lee', 'Carlos Diaz', 'Dana White'
];
let mockQueue = [
    { number: '+12345678901', status: 'Waiting', wait: '00:01:23', agent: 'Unassigned' },
    { number: '+19876543210', status: 'Ringing', wait: '00:00:45', agent: 'Unassigned' },
    { number: '+11234567890', status: 'Queued', wait: '00:02:10', agent: 'Alice Smith' },
];
function renderCallQueueTable() {
    const tbody = document.getElementById('callQueueTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockQueue.forEach((call, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${call.number}</td>
            <td>${call.status}</td>
            <td>${call.wait}</td>
            <td>
                <select class='form-select form-select-sm' data-idx='${idx}'>
                    ${mockAgents.map(a => `<option${a===call.agent?' selected':''}>${a}</option>`).join('')}
                </select>
            </td>
            <td><button class='btn btn-sm btn-primary' data-assign='${idx}' title='Assign Agent'>Assign</button></td>
        `;
        tbody.appendChild(tr);
    });
    // Add event listeners for assignment
    tbody.querySelectorAll('button[data-assign]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-assign');
            const select = tbody.querySelector(`select[data-idx='${idx}']`);
            const agent = select.value;
            mockQueue[idx].agent = agent;
            document.getElementById('callQueueStatus').innerHTML = `<span class='text-success'>Call assigned to ${agent}.</span>`;
            renderCallQueueTable();
        };
    });
}
function setupCallQueue() {
    renderCallQueueTable();
}
// Re-setup call queue when section is shown
const callQueueObserver = new MutationObserver(() => {
    const section = document.getElementById('call-queue-section');
    if (section && section.style.display !== 'none') {
        setupCallQueue();
    }
});
callQueueObserver.observe(document.body, { childList: true, subtree: true });

// Call Recordings Logic
const mockRecordings = [
    { datetime: '2024-07-07 09:15', number: '+12345678901', agent: 'Alice Smith', duration: '02:15', url: 'sample1.mp3' },
    { datetime: '2024-07-07 09:10', number: '+19876543210', agent: 'Bob Lee', duration: '01:05', url: 'sample2.mp3' },
    { datetime: '2024-07-07 08:55', number: '+11234567890', agent: 'Carlos Diaz', duration: '03:22', url: 'sample3.mp3' },
];
function renderCallRecordingsTable() {
    const tbody = document.getElementById('callRecordingsTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockRecordings.forEach((rec, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${rec.datetime}</td>
            <td>${rec.number}</td>
            <td>${rec.agent}</td>
            <td>${rec.duration}</td>
            <td>
                <button class='btn btn-sm btn-outline-primary me-1' data-play='${idx}' title='Play Recording'><i class='bi bi-play-circle'></i></button>
                <a class='btn btn-sm btn-outline-secondary' href='${rec.url}' download title='Download Recording'><i class='bi bi-download'></i></a>
            </td>
        `;
        tbody.appendChild(tr);
    });
    // Play button logic (mock)
    tbody.querySelectorAll('button[data-play]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-play');
            document.getElementById('callRecordingsStatus').innerHTML = `<span class='text-info'>Playing recording for ${mockRecordings[idx].number}...</span>`;
            setTimeout(() => {
                document.getElementById('callRecordingsStatus').innerHTML = '';
            }, 2000);
        };
    });
}
function setupCallRecordings() {
    renderCallRecordingsTable();
}
// Re-setup call recordings when section is shown
const callRecordingsObserver = new MutationObserver(() => {
    const section = document.getElementById('call-recordings-section');
    if (section && section.style.display !== 'none') {
        setupCallRecordings();
    }
});
callRecordingsObserver.observe(document.body, { childList: true, subtree: true });

// Call Dispositions Logic
const mockDispositions = [
    'Interested', 'Not Interested', 'Follow-up', 'Voicemail', 'DNC', 'Callback', 'No Answer'
];
let mockCalls = [
    { number: '+12345678901', agent: 'Alice Smith', time: '09:15', disposition: '' },
    { number: '+19876543210', agent: 'Bob Lee', time: '09:10', disposition: '' },
    { number: '+11234567890', agent: 'Carlos Diaz', time: '08:55', disposition: '' },
];
function renderCallDispositionsTable() {
    const tbody = document.getElementById('callDispositionsTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockCalls.forEach((call, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${call.number}</td>
            <td>${call.agent}</td>
            <td>${call.time}</td>
            <td>
                <select class='form-select form-select-sm' data-idx='${idx}'>
                    <option value=''>Select...</option>
                    ${mockDispositions.map(d => `<option${d===call.disposition?' selected':''}>${d}</option>`).join('')}
                </select>
            </td>
            <td><button class='btn btn-sm btn-success' data-save='${idx}' title='Save Disposition'>Save</button></td>
        `;
        tbody.appendChild(tr);
    });
    // Save button logic
    tbody.querySelectorAll('button[data-save]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-save');
            const select = tbody.querySelector(`select[data-idx='${idx}']`);
            const val = select.value;
            if (!val) {
                document.getElementById('callDispositionsStatus').innerHTML = `<span class='text-danger'>Please select a disposition.</span>`;
                return;
            }
            mockCalls[idx].disposition = val;
            document.getElementById('callDispositionsStatus').innerHTML = `<span class='text-success'>Disposition saved for ${mockCalls[idx].number}.</span>`;
            renderCallDispositionsTable();
        };
    });
}
function setupCallDispositions() {
    renderCallDispositionsTable();
}
// Re-setup call dispositions when section is shown
const callDispositionsObserver = new MutationObserver(() => {
    const section = document.getElementById('call-dispositions-section');
    if (section && section.style.display !== 'none') {
        setupCallDispositions();
    }
});
callDispositionsObserver.observe(document.body, { childList: true, subtree: true });

// Agent Assist (Call Scripting) Logic
const mockScripts = {
    insurance: [
        'Greet the customer and introduce yourself as an insurance specialist.',
        'Ask: Are you looking for trucking insurance or want to renew your policy?',
        'Collect company name, DOT number, and current insurance provider.',
        'Explain coverage options and ask about their needs.',
        'Schedule a follow-up or transfer to a licensed agent.'
    ],
    realestate: [
        'Greet the customer and introduce yourself as a real estate advisor.',
        'Ask: Are you interested in buying, selling, or renting property?',
        'Collect property details and customer preferences.',
        'Explain available listings and next steps.',
        'Schedule a property tour or follow-up call.'
    ],
    collections: [
        'Greet the customer and state the purpose of your call (collections).',
        'Verify customer identity and outstanding balance.',
        'Discuss payment options and resolve objections.',
        'Confirm payment arrangement or escalate as needed.',
        'Thank the customer and provide contact info for support.'
    ]
};
let scriptState = { campaign: 'insurance', step: 0 };
function renderScriptPanel() {
    const panel = document.getElementById('scriptPanel');
    if (!panel) return;
    const steps = mockScripts[scriptState.campaign];
    panel.innerHTML = `<div class='p-3 bg-light rounded'><strong>Step ${scriptState.step+1} of ${steps.length}:</strong><br>${steps[scriptState.step]}</div>`;
    document.getElementById('scriptPrevBtn').disabled = scriptState.step === 0;
    document.getElementById('scriptNextBtn').disabled = scriptState.step === steps.length-1;
}
function setupAgentAssist() {
    const select = document.getElementById('scriptCampaignSelect');
    select.value = scriptState.campaign;
    renderScriptPanel();
    select.onchange = function() {
        scriptState.campaign = select.value;
        scriptState.step = 0;
        renderScriptPanel();
    };
    document.getElementById('scriptPrevBtn').onclick = function() {
        if (scriptState.step > 0) {
            scriptState.step--;
            renderScriptPanel();
        }
    };
    document.getElementById('scriptNextBtn').onclick = function() {
        const steps = mockScripts[scriptState.campaign];
        if (scriptState.step < steps.length-1) {
            scriptState.step++;
            renderScriptPanel();
        }
    };
}
// Re-setup agent assist when section is shown
const agentAssistObserver = new MutationObserver(() => {
    const section = document.getElementById('agent-assist-section');
    if (section && section.style.display !== 'none') {
        setupAgentAssist();
    }
});
agentAssistObserver.observe(document.body, { childList: true, subtree: true });

// AI Agent Training/Management Logic
function getAIAgents() {
    return JSON.parse(localStorage.getItem('aiAgents') || '[]');
}
function setAIAgents(agents) {
    localStorage.setItem('aiAgents', JSON.stringify(agents));
}
function renderAIAgentsTable() {
    const tbody = document.getElementById('aiAgentsTableBody');
    if (!tbody) return;
    const agents = getAIAgents();
    tbody.innerHTML = '';
    if (agents.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-muted text-center">No AI agents created yet.</td></tr>';
        return;
    }
    agents.forEach((agent, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${agent.name}</td>
            <td>${agent.language}</td>
            <td>${agent.voice}</td>
            <td>${agent.status || 'Active'}</td>
            <td>
                <button class='btn btn-sm btn-outline-primary me-1' data-edit='${idx}' title='Edit Agent'><i class='bi bi-pencil'></i></button>
                <button class='btn btn-sm btn-outline-danger' data-delete='${idx}' title='Delete Agent'><i class='bi bi-trash'></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    // Edit button logic
    tbody.querySelectorAll('button[data-edit]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-edit');
            showAIAgentModal('edit', idx);
        };
    });
    // Delete button logic
    tbody.querySelectorAll('button[data-delete]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-delete');
            let agents = getAIAgents();
            const name = agents[idx].name;
            agents.splice(idx, 1);
            setAIAgents(agents);
            document.getElementById('aiAgentsStatus').innerHTML = `<span class='text-danger'>Agent '${name}' deleted.</span>`;
            renderAIAgentsTable();
        };
    });
}
function showAIAgentModal(mode, idx) {
    const modal = document.getElementById('aiAgentModal');
    const form = document.getElementById('aiAgentForm');
    const title = document.getElementById('aiAgentModalTitle');
    modal.classList.remove('d-none');
    modal.style.display = 'block';
    let agents = getAIAgents();
    if (mode === 'edit') {
        title.textContent = 'Edit AI Agent';
        const agent = agents[idx];
        form.dataset.mode = 'edit';
        form.dataset.idx = idx;
        document.getElementById('aiAgentName').value = agent.name;
        document.getElementById('aiAgentLanguage').value = agent.language;
        document.getElementById('aiAgentVoice').value = agent.voice;
        document.getElementById('aiAgentSpeed').value = agent.speed || 1;
        document.getElementById('aiAgentPitch').value = agent.pitch || 1;
        document.getElementById('aiAgentTone').value = agent.tone || 'neutral';
    } else {
        title.textContent = 'Create AI Agent';
        form.dataset.mode = 'create';
        form.dataset.idx = '';
        form.reset();
        document.getElementById('aiAgentSpeed').value = 1;
        document.getElementById('aiAgentPitch').value = 1;
        document.getElementById('aiAgentTone').value = 'neutral';
    }
}
function hideAIAgentModal() {
    const modal = document.getElementById('aiAgentModal');
    modal.classList.add('d-none');
    modal.style.display = 'none';
}
function setupAIAgentManagement() {
    renderAIAgentsTable();
    document.getElementById('createAIAgentBtn').onclick = function() {
        showAIAgentModal('create');
    };
    document.getElementById('closeAIAgentModal').onclick = function() {
        hideAIAgentModal();
    };
    document.getElementById('aiAgentModal').onclick = function(e) {
        if (e.target === this) hideAIAgentModal();
    };
    document.getElementById('aiAgentForm').onsubmit = function(e) {
        e.preventDefault();
        let agents = getAIAgents();
        const name = document.getElementById('aiAgentName').value.trim();
        const language = document.getElementById('aiAgentLanguage').value;
        const voice = document.getElementById('aiAgentVoice').value;
        const speed = document.getElementById('aiAgentSpeed').value;
        const pitch = document.getElementById('aiAgentPitch').value;
        const tone = document.getElementById('aiAgentTone').value;
        if (!name) return;
        if (this.dataset.mode === 'edit') {
            const idx = +this.dataset.idx;
            agents[idx] = { name, language, voice, speed, pitch, tone, status: 'Active' };
            document.getElementById('aiAgentsStatus').innerHTML = `<span class='text-success'>Agent '${name}' updated.</span>`;
        } else {
            agents.push({ name, language, voice, speed, pitch, tone, status: 'Active' });
            document.getElementById('aiAgentsStatus').innerHTML = `<span class='text-success'>Agent '${name}' created.</span>`;
        }
        setAIAgents(agents);
        hideAIAgentModal();
        renderAIAgentsTable();
    };
}
// Re-setup AI agent management when section is shown
const aiAgentsObserver = new MutationObserver(() => {
    const section = document.getElementById('ai-agents-section');
    if (section && section.style.display !== 'none') {
        setupAIAgentManagement();
    }
});
aiAgentsObserver.observe(document.body, { childList: true, subtree: true });

// Voice Selection & Customization Logic
function setupVoiceSelection() {
    const form = document.getElementById('voiceSelectionForm');
    const status = document.getElementById('voiceSelectionStatus');
    const voiceSelect = document.getElementById('voiceSelect');
    const speed = document.getElementById('voiceSpeed');
    const pitch = document.getElementById('voicePitch');
    const tone = document.getElementById('voiceTone');
    // Load saved default
    const saved = JSON.parse(localStorage.getItem('defaultVoice') || '{}');
    if (saved.voice) voiceSelect.value = saved.voice;
    if (saved.speed) speed.value = saved.speed;
    if (saved.pitch) pitch.value = saved.pitch;
    if (saved.tone) tone.value = saved.tone;
    document.getElementById('previewVoiceBtn').onclick = function() {
        status.innerHTML = `<span class='text-info'>Previewing voice: ${voiceSelect.options[voiceSelect.selectedIndex].text}, Speed: ${speed.value}, Pitch: ${pitch.value}, Tone: ${tone.value} (mocked)</span>`;
        setTimeout(() => { status.innerHTML = ''; }, 2000);
    };
    form.onsubmit = function(e) {
        e.preventDefault();
        localStorage.setItem('defaultVoice', JSON.stringify({
            voice: voiceSelect.value,
            speed: speed.value,
            pitch: pitch.value,
            tone: tone.value
        }));
        status.innerHTML = `<span class='text-success'>Default voice settings saved!</span>`;
        setTimeout(() => { status.innerHTML = ''; }, 2000);
    };
}
// Re-setup voice selection when section is shown
const voiceSelectionObserver = new MutationObserver(() => {
    const section = document.getElementById('voice-selection-section');
    if (section && section.style.display !== 'none') {
        setupVoiceSelection();
    }
});
voiceSelectionObserver.observe(document.body, { childList: true, subtree: true });

// Conversation Designer Logic
function getNodes() {
    return JSON.parse(localStorage.getItem('conversationNodes') || '[]');
}
function setNodes(nodes) {
    localStorage.setItem('conversationNodes', JSON.stringify(nodes));
}
function renderConversationFlow() {
    const flow = document.getElementById('conversationFlow');
    if (!flow) return;
    const nodes = getNodes();
    if (nodes.length === 0) {
        flow.innerHTML = '<div class="text-muted text-center">No nodes in the conversation flow yet.</div>';
        return;
    }
    flow.innerHTML = '<div class="d-flex flex-wrap gap-3">' +
        nodes.map((node, idx) => `
            <div class='p-3 border rounded bg-light position-relative' style='min-width:220px;'>
                <div class='fw-bold mb-1'>${node.label}</div>
                <div class='small text-secondary mb-1'>${node.type}</div>
                <div class='mb-2'>${node.content}</div>
                <button class='btn btn-sm btn-outline-primary me-1' data-edit-node='${idx}' title='Edit Node'><i class='bi bi-pencil'></i></button>
                <button class='btn btn-sm btn-outline-danger' data-delete-node='${idx}' title='Delete Node'><i class='bi bi-trash'></i></button>
            </div>
        `).join('') + '</div>';
    // Edit button logic
    flow.querySelectorAll('button[data-edit-node]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-edit-node');
            showNodeModal('edit', idx);
        };
    });
    // Delete button logic
    flow.querySelectorAll('button[data-delete-node]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-delete-node');
            let nodes = getNodes();
            const label = nodes[idx].label;
            nodes.splice(idx, 1);
            setNodes(nodes);
            document.getElementById('conversationDesignerStatus').innerHTML = `<span class='text-danger'>Node '${label}' deleted.</span>`;
            renderConversationFlow();
        };
    });
}
function showNodeModal(mode, idx) {
    const modal = document.getElementById('nodeModal');
    const form = document.getElementById('nodeForm');
    const title = document.getElementById('nodeModalTitle');
    modal.classList.remove('d-none');
    modal.style.display = 'block';
    let nodes = getNodes();
    if (mode === 'edit') {
        title.textContent = 'Edit Node';
        const node = nodes[idx];
        form.dataset.mode = 'edit';
        form.dataset.idx = idx;
        document.getElementById('nodeLabel').value = node.label;
        document.getElementById('nodeType').value = node.type;
        document.getElementById('nodeContent').value = node.content;
    } else {
        title.textContent = 'Add Node';
        form.dataset.mode = 'add';
        form.dataset.idx = '';
        form.reset();
    }
}
function hideNodeModal() {
    const modal = document.getElementById('nodeModal');
    modal.classList.add('d-none');
    modal.style.display = 'none';
}
function setupConversationDesigner() {
    renderConversationFlow();
    document.getElementById('addNodeBtn').onclick = function() {
        showNodeModal('add');
    };
    document.getElementById('closeNodeModal').onclick = function() {
        hideNodeModal();
    };
    document.getElementById('nodeModal').onclick = function(e) {
        if (e.target === this) hideNodeModal();
    };
    document.getElementById('nodeForm').onsubmit = function(e) {
        e.preventDefault();
        let nodes = getNodes();
        const label = document.getElementById('nodeLabel').value.trim();
        const type = document.getElementById('nodeType').value;
        const content = document.getElementById('nodeContent').value.trim();
        if (!label || !type || !content) return;
        if (this.dataset.mode === 'edit') {
            const idx = +this.dataset.idx;
            nodes[idx] = { label, type, content };
            document.getElementById('conversationDesignerStatus').innerHTML = `<span class='text-success'>Node '${label}' updated.</span>`;
        } else {
            nodes.push({ label, type, content });
            document.getElementById('conversationDesignerStatus').innerHTML = `<span class='text-success'>Node '${label}' added.</span>`;
        }
        setNodes(nodes);
        hideNodeModal();
        renderConversationFlow();
    };
}
// Re-setup conversation designer when section is shown
const conversationDesignerObserver = new MutationObserver(() => {
    const section = document.getElementById('conversation-designer-section');
    if (section && section.style.display !== 'none') {
        setupConversationDesigner();
    }
});
conversationDesignerObserver.observe(document.body, { childList: true, subtree: true });

// Reports & History Logic
const mockCallReports = [
    { date: '2024-07-07', number: '+12345678901', agent: 'Alice Smith', duration: '02:15', outcome: 'Interested' },
    { date: '2024-07-07', number: '+19876543210', agent: 'Bob Lee', duration: '01:05', outcome: 'Not Interested' },
    { date: '2024-07-06', number: '+11234567890', agent: 'Carlos Diaz', duration: '03:22', outcome: 'Follow-up' },
];
const mockAgentReports = [
    { agent: 'Alice Smith', total: 120, connected: 90, avg: '1:45', rate: '75%' },
    { agent: 'Bob Lee', total: 100, connected: 70, avg: '1:30', rate: '70%' },
    { agent: 'Carlos Diaz', total: 80, connected: 60, avg: '2:00', rate: '75%' },
];
function renderCallReportTable() {
    const tbody = document.getElementById('callReportTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockCallReports.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.date}</td>
            <td>${row.number}</td>
            <td>${row.agent}</td>
            <td>${row.duration}</td>
            <td>${row.outcome}</td>
        `;
        tbody.appendChild(tr);
    });
}
function renderAgentReportTable() {
    const tbody = document.getElementById('agentReportTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockAgentReports.forEach(row => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${row.agent}</td>
            <td>${row.total}</td>
            <td>${row.connected}</td>
            <td>${row.avg}</td>
            <td>${row.rate}</td>
        `;
        tbody.appendChild(tr);
    });
}
function setupReportsHistory() {
    renderCallReportTable();
    renderAgentReportTable();
    document.getElementById('callReportTab').onclick = function() {
        document.getElementById('callReportTable').classList.add('show','active');
        document.getElementById('agentReportTable').classList.remove('show','active');
    };
    document.getElementById('agentReportTab').onclick = function() {
        document.getElementById('agentReportTable').classList.add('show','active');
        document.getElementById('callReportTable').classList.remove('show','active');
    };
    document.getElementById('exportCallReportBtn').onclick = function() {
        document.getElementById('reportsHistoryStatus').innerHTML = `<span class='text-info'>Exporting call report (mocked)...</span>`;
        setTimeout(() => { document.getElementById('reportsHistoryStatus').innerHTML = ''; }, 2000);
    };
    document.getElementById('exportAgentReportBtn').onclick = function() {
        document.getElementById('reportsHistoryStatus').innerHTML = `<span class='text-info'>Exporting agent report (mocked)...</span>`;
        setTimeout(() => { document.getElementById('reportsHistoryStatus').innerHTML = ''; }, 2000);
    };
}
// Re-setup reports & history when section is shown
const reportsHistoryObserver = new MutationObserver(() => {
    const section = document.getElementById('reports-history-section');
    if (section && section.style.display !== 'none') {
        setupReportsHistory();
    }
});
reportsHistoryObserver.observe(document.body, { childList: true, subtree: true });

// Call Summarization Logic
let mockSummarizationCalls = [
    { number: '+12345678901', agent: 'Alice Smith', time: '09:15', summary: '' },
    { number: '+19876543210', agent: 'Bob Lee', time: '09:10', summary: '' },
    { number: '+11234567890', agent: 'Carlos Diaz', time: '08:55', summary: '' },
];
function renderCallSummarizationTable() {
    const tbody = document.getElementById('callSummarizationTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockSummarizationCalls.forEach((call, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${call.number}</td>
            <td>${call.agent}</td>
            <td>${call.time}</td>
            <td>${call.summary ? call.summary : '<span class=\'text-muted\'>No summary yet</span>'}</td>
            <td><button class='btn btn-sm btn-primary' data-summarize='${idx}' title='Generate/View Summary'>Summarize</button></td>
        `;
        tbody.appendChild(tr);
    });
    // Summarize button logic
    tbody.querySelectorAll('button[data-summarize]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-summarize');
            document.getElementById('callSummarizationStatus').innerHTML = `<span class='text-info'>Generating summary for ${mockSummarizationCalls[idx].number}...</span>`;
            setTimeout(() => {
                mockSummarizationCalls[idx].summary = 'AI Summary: Customer was interested and requested a follow-up.';
                renderCallSummarizationTable();
                document.getElementById('callSummarizationStatus').innerHTML = `<span class='text-success'>Summary generated for ${mockSummarizationCalls[idx].number}.</span>`;
                setTimeout(() => { document.getElementById('callSummarizationStatus').innerHTML = ''; }, 2000);
            }, 1200);
        };
    });
}
function setupCallSummarization() {
    renderCallSummarizationTable();
}
// Re-setup call summarization when section is shown
const callSummarizationObserver = new MutationObserver(() => {
    const section = document.getElementById('call-summarization-section');
    if (section && section.style.display !== 'none') {
        setupCallSummarization();
    }
});
callSummarizationObserver.observe(document.body, { childList: true, subtree: true });

// Consent Capture Logic
let mockConsentCalls = [
    { number: '+12345678901', agent: 'Alice Smith', time: '09:15', consent: 'Pending' },
    { number: '+19876543210', agent: 'Bob Lee', time: '09:10', consent: 'Given' },
    { number: '+11234567890', agent: 'Carlos Diaz', time: '08:55', consent: 'Denied' },
];
function renderConsentCaptureTable() {
    const tbody = document.getElementById('consentCaptureTableBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    mockConsentCalls.forEach((call, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${call.number}</td>
            <td>${call.agent}</td>
            <td>${call.time}</td>
            <td>${call.consent}</td>
            <td><button class='btn btn-sm btn-success' data-consent='${idx}' title='Log/Update Consent'>Log Consent</button></td>
        `;
        tbody.appendChild(tr);
    });
    // Consent button logic
    tbody.querySelectorAll('button[data-consent]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-consent');
            const current = mockConsentCalls[idx].consent;
            let next;
            if (current === 'Pending') next = 'Given';
            else if (current === 'Given') next = 'Denied';
            else next = 'Pending';
            mockConsentCalls[idx].consent = next;
            renderConsentCaptureTable();
            document.getElementById('consentCaptureStatus').innerHTML = `<span class='text-success'>Consent status updated to '${next}' for ${mockConsentCalls[idx].number}.</span>`;
            setTimeout(() => { document.getElementById('consentCaptureStatus').innerHTML = ''; }, 2000);
        };
    });
}
function setupConsentCapture() {
    renderConsentCaptureTable();
}
// Re-setup consent capture when section is shown
const consentCaptureObserver = new MutationObserver(() => {
    const section = document.getElementById('consent-capture-section');
    if (section && section.style.display !== 'none') {
        setupConsentCapture();
    }
});
consentCaptureObserver.observe(document.body, { childList: true, subtree: true });

// DNC Management Logic
function getDncNumbers() {
    return JSON.parse(localStorage.getItem('dncNumbers') || '[]');
}
function setDncNumbers(numbers) {
    localStorage.setItem('dncNumbers', JSON.stringify(numbers));
}
function renderDncList() {
    const list = document.getElementById('dncList');
    if (!list) return;
    let numbers = getDncNumbers();
    list.innerHTML = '';
    if (numbers.length === 0) {
        list.innerHTML = '<li class="list-group-item text-muted">No numbers in DNC list.</li>';
        return;
    }
    numbers.forEach((num, idx) => {
        const li = document.createElement('li');
        li.className = 'list-group-item d-flex justify-content-between align-items-center';
        li.innerHTML = `<span>${num}</span>`;
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-outline-danger';
        removeBtn.title = 'Remove from DNC';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = function() {
            numbers.splice(idx, 1);
            setDncNumbers(numbers);
            renderDncList();
            document.getElementById('dncManagementStatus').innerHTML = `<span class='text-danger'>Number removed from DNC list.</span>`;
            setTimeout(() => { document.getElementById('dncManagementStatus').innerHTML = ''; }, 2000);
        };
        li.appendChild(removeBtn);
        list.appendChild(li);
    });
}
function setupDncManagement() {
    renderDncList();
    const form = document.getElementById('addDncForm');
    const input = document.getElementById('newDncInput');
    const status = document.getElementById('dncManagementStatus');
    if (form && input && status) {
        form.onsubmit = function(e) {
            e.preventDefault();
            let numbers = getDncNumbers();
            const val = input.value.trim();
            if (!/^\+?\d{10,15}$/.test(val)) {
                status.innerHTML = '<span class="text-danger">Enter a valid number (e.g. +1XXXXXXXXXX).</span>';
                return;
            }
            if (numbers.includes(val)) {
                status.innerHTML = '<span class="text-warning">Number already in DNC list.</span>';
                return;
            }
            numbers.push(val);
            setDncNumbers(numbers);
            input.value = '';
            status.innerHTML = '<span class="text-success">Number added to DNC list!</span>';
            renderDncList();
        };
    }
}
// Re-setup DNC management when section is shown
const dncObserver = new MutationObserver(() => {
    const section = document.getElementById('dnc-management-section');
    if (section && section.style.display !== 'none') {
        setupDncManagement();
    }
});
dncObserver.observe(document.body, { childList: true, subtree: true });

// Access Controls Logic
function getUsers() {
    return JSON.parse(localStorage.getItem('users') || '[{"name":"Alice Smith","email":"alice@example.com","role":"Admin","status":"Active"},{"name":"Bob Lee","email":"bob@example.com","role":"Manager","status":"Active"},{"name":"Carlos Diaz","email":"carlos@example.com","role":"Agent","status":"Inactive"}]');
}
function setUsers(users) {
    localStorage.setItem('users', JSON.stringify(users));
}
function renderAccessControlsTable() {
    const tbody = document.getElementById('accessControlsTableBody');
    if (!tbody) return;
    const users = getUsers();
    tbody.innerHTML = '';
    users.forEach((user, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>
                <select class='form-select form-select-sm' data-role='${idx}'>
                    <option${user.role==='Admin'?' selected':''}>Admin</option>
                    <option${user.role==='Manager'?' selected':''}>Manager</option>
                    <option${user.role==='Agent'?' selected':''}>Agent</option>
                </select>
            </td>
            <td>${user.status}</td>
            <td>
                <button class='btn btn-sm btn-primary me-1' data-update-role='${idx}' title='Update Role'>Update</button>
                <button class='btn btn-sm btn-${user.status==='Active'?'danger':'success'}' data-toggle-status='${idx}' title='${user.status==='Active'?'Deactivate':'Reactivate'} User'>${user.status==='Active'?'Deactivate':'Reactivate'}</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
    // Update role logic
    tbody.querySelectorAll('button[data-update-role]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-update-role');
            const select = tbody.querySelector(`select[data-role='${idx}']`);
            let users = getUsers();
            users[idx].role = select.value;
            setUsers(users);
            document.getElementById('accessControlsStatus').innerHTML = `<span class='text-success'>Role updated for ${users[idx].name}.</span>`;
            renderAccessControlsTable();
        };
    });
    // Toggle status logic
    tbody.querySelectorAll('button[data-toggle-status]').forEach(btn => {
        btn.onclick = function() {
            const idx = +btn.getAttribute('data-toggle-status');
            let users = getUsers();
            users[idx].status = users[idx].status === 'Active' ? 'Inactive' : 'Active';
            setUsers(users);
            document.getElementById('accessControlsStatus').innerHTML = `<span class='text-info'>Status updated for ${users[idx].name}.</span>`;
            renderAccessControlsTable();
        };
    });
}
function setupAccessControls() {
    renderAccessControlsTable();
}
// Re-setup access controls when section is shown
const accessControlsObserver = new MutationObserver(() => {
    const section = document.getElementById('access-controls-section');
    if (section && section.style.display !== 'none') {
        setupAccessControls();
    }
});
accessControlsObserver.observe(document.body, { childList: true, subtree: true });

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initializeCharts, 100);
    initializePowerDialer();
}); 