const { ipcRenderer } = require('electron');

// ── Palette for account avatars ──────────────────────────
const COLORS = [
  ['#25D366', '#00a884'],
  ['#6366f1', '#8b5cf6'],
  ['#f59e0b', '#ef4444'],
  ['#ec4899', '#8b5cf6'],
  ['#06b6d4', '#3b82f6'],
  ['#f97316', '#ef4444'],
  ['#10b981', '#06b6d4'],
  ['#a855f7', '#ec4899'],
];

function getGradient(index) {
  const [a, b] = COLORS[index % COLORS.length];
  return `linear-gradient(135deg, ${a}, ${b})`;
}

// ── State ────────────────────────────────────────────────
let accounts = JSON.parse(localStorage.getItem('wa_accounts') || '[]');
let activeAccountId = null;
let contextTargetId = null;

// ── DOM ──────────────────────────────────────────────────
const accountList   = document.getElementById('account-list');
const mainContent   = document.getElementById('main-content');
const emptyState    = document.getElementById('empty-state');
const addBtn        = document.getElementById('add-account-btn');
const emptyAddBtn   = document.getElementById('empty-add-btn');
const modal         = document.getElementById('add-modal');
const nameInput     = document.getElementById('account-name-input');
const cancelBtn     = document.getElementById('cancel-add-btn');
const confirmBtn    = document.getElementById('confirm-add-btn');
const contextMenu   = document.getElementById('context-menu');
const ctxRemoveBtn  = document.getElementById('ctx-remove-btn');

const updateNotif   = document.getElementById('update-notification');
const updateMsg     = document.getElementById('update-message');
const updateAction  = document.getElementById('update-action-btn');
const manualUpdateBtn = document.getElementById('manual-update-btn');

// ── Init ─────────────────────────────────────────────────
function init() {
  accounts.forEach((acc, i) => {
    createIcon(acc, i);
    createWebview(acc);
  });
  if (accounts.length > 0) {
    switchTo(accounts[0].id);
  }
}

// ── Helpers ──────────────────────────────────────────────
function uid() {
  return 'wa_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7);
}

function getInitials(name) {
  return name.trim().split(/\s+/).map(w => w[0]).join('').slice(0, 2).toUpperCase();
}

function save() {
  localStorage.setItem('wa_accounts', JSON.stringify(accounts));
}

// ── Create icon in sidebar ───────────────────────────────
function createIcon(acc, colorIndex) {
  const el = document.createElement('div');
  el.className = 'account-item pop-in';
  el.id = `icon-${acc.id}`;
  el.style.background = getGradient(colorIndex !== undefined ? colorIndex : accounts.indexOf(acc));

  const initials = document.createTextNode(getInitials(acc.name));
  el.appendChild(initials);

  const tip = document.createElement('div');
  tip.className = 'tooltip';
  tip.textContent = acc.name;
  el.appendChild(tip);

  el.addEventListener('click', () => switchTo(acc.id));

  // Right click = context menu
  el.addEventListener('contextmenu', (e) => {
    e.preventDefault();
    contextTargetId = acc.id;
    showContextMenu(e.clientX, e.clientY);
  });

  accountList.appendChild(el);

  // Remove animation class after it plays
  setTimeout(() => el.classList.remove('pop-in'), 400);
}

// ── Create webview ───────────────────────────────────────
function createWebview(acc) {
  const wv = document.createElement('webview');
  wv.id = `wv-${acc.id}`;
  wv.src = 'https://web.whatsapp.com/';
  wv.partition = acc.partition;
  wv.setAttribute('allowpopups', 'true');
  wv.setAttribute('useragent',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36'
  );
  mainContent.appendChild(wv);
}

// ── Switch account ───────────────────────────────────────
function switchTo(id) {
  if (activeAccountId === id) return;

  document.querySelectorAll('.account-item').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('webview').forEach(wv => wv.classList.remove('active'));

  const icon = document.getElementById(`icon-${id}`);
  const wv   = document.getElementById(`wv-${id}`);

  if (icon) icon.classList.add('active');
  if (wv)   wv.classList.add('active');

  emptyState.style.display = 'none';
  activeAccountId = id;
}

// ── Add account ──────────────────────────────────────────
function addAccount(name) {
  const acc = {
    id:        uid(),
    name:      name,
    partition: 'persist:' + uid(),
  };
  const colorIndex = accounts.length;
  accounts.push(acc);
  save();
  createIcon(acc, colorIndex);
  createWebview(acc);
  switchTo(acc.id);
}

// ── Remove account ───────────────────────────────────────
function removeAccount(id) {
  const icon = document.getElementById(`icon-${id}`);
  const wv   = document.getElementById(`wv-${id}`);

  if (icon) {
    icon.classList.add('removing');
    setTimeout(() => icon.remove(), 280);
  }
  if (wv) wv.remove();

  accounts = accounts.filter(a => a.id !== id);
  save();

  if (activeAccountId === id) {
    activeAccountId = null;
    if (accounts.length > 0) {
      switchTo(accounts[0].id);
    } else {
      emptyState.style.display = 'flex';
    }
  }
}

// ── Modal ────────────────────────────────────────────────
function openModal() {
  modal.classList.add('show');
  setTimeout(() => nameInput.focus(), 100);
}

function closeModal() {
  modal.classList.remove('show');
  nameInput.value = '';
}

// ── Context menu ─────────────────────────────────────────
function showContextMenu(x, y) {
  contextMenu.style.left = x + 'px';
  contextMenu.style.top  = y + 'px';
  contextMenu.classList.add('show');
}

function hideContextMenu() {
  contextMenu.classList.remove('show');
  contextTargetId = null;
}

// ── Event listeners ──────────────────────────────────────
addBtn.addEventListener('click', openModal);
emptyAddBtn.addEventListener('click', openModal);
cancelBtn.addEventListener('click', closeModal);

confirmBtn.addEventListener('click', () => {
  const name = nameInput.value.trim();
  if (name) {
    addAccount(name);
    closeModal();
  } else {
    nameInput.style.borderColor = '#f85149';
    nameInput.style.boxShadow = '0 0 0 3px rgba(248,81,73,0.2)';
    setTimeout(() => {
      nameInput.style.borderColor = '';
      nameInput.style.boxShadow = '';
    }, 800);
  }
});

nameInput.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') confirmBtn.click();
  if (e.key === 'Escape') closeModal();
});

// Close modal on overlay click
modal.addEventListener('click', (e) => {
  if (e.target === modal) closeModal();
});

// Context menu remove
ctxRemoveBtn.addEventListener('click', () => {
  if (contextTargetId) removeAccount(contextTargetId);
  hideContextMenu();
});

// Close context menu on outside click
document.addEventListener('click', (e) => {
  if (!contextMenu.contains(e.target)) hideContextMenu();
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeModal();
    hideContextMenu();
  }
});

// ── Auto Update ──────────────────────────────────────────
ipcRenderer.on('app_version', (_, version) => {
  document.getElementById('app-version').textContent = 'v' + version;
});

manualUpdateBtn.addEventListener('click', () => {
  ipcRenderer.send('check_update');
});

ipcRenderer.on('checking_update', () => {
  updateMsg.innerText = 'Checking for updates...';
  updateAction.classList.add('hidden');
  updateNotif.classList.remove('hidden');
});

ipcRenderer.on('update_not_available', () => {
  updateMsg.innerText = 'You are on the latest version.';
  setTimeout(() => updateNotif.classList.add('hidden'), 3000);
});

ipcRenderer.on('update_error', (_, err) => {
  updateMsg.innerText = 'Update error: ' + err;
});

ipcRenderer.on('update_available', () => {
  updateMsg.innerText = 'A new update is available. Downloading...';
  updateNotif.classList.remove('hidden');
});

ipcRenderer.on('update_downloaded', () => {
  updateMsg.innerText = 'Update downloaded and ready to install.';
  updateAction.classList.remove('hidden');
});

updateAction.addEventListener('click', () => {
  ipcRenderer.send('restart_app');
});

// ── Start ────────────────────────────────────────────────
init();
