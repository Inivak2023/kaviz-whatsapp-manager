const { app, BrowserWindow, nativeImage, ipcMain } = require('electron');
const path = require('path');
const { autoUpdater } = require('electron-updater');

let mainWindow;

function createWindow() {
  const iconPath = path.join(__dirname, 'logo.png');
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 800,
    icon: iconPath,
    webPreferences: {
      webviewTag: true, // Enable <webview> tag
      nodeIntegration: true,
      contextIsolation: false
    },
    autoHideMenuBar: true,
    titleBarStyle: 'hidden',
    titleBarOverlay: {
      color: '#0d1117',
      symbolColor: '#e6edf3',
      height: 38
    }
  });

  mainWindow.removeMenu();
  mainWindow.loadFile('index.html');

  // Send app version to renderer once ready
  mainWindow.webContents.on('did-finish-load', () => {
    mainWindow.webContents.send('app_version', app.getVersion());
  });

  // Auto-updater events
  autoUpdater.on('update-available', () => {
    mainWindow.webContents.send('update_available');
  });

  autoUpdater.on('update-downloaded', () => {
    mainWindow.webContents.send('update_downloaded');
  });

  autoUpdater.on('error', (err) => {
    mainWindow.webContents.send('update_error', err.toString());
  });

  autoUpdater.on('checking-for-update', () => {
    mainWindow.webContents.send('checking_update');
  });

  autoUpdater.on('update-not-available', () => {
    mainWindow.webContents.send('update_not_available');
  });

  // Check for updates shortly after startup
  setTimeout(() => {
    autoUpdater.checkForUpdatesAndNotify();
  }, 3000);
}

app.whenReady().then(() => {
  createWindow();

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createWindow();
    }
  });
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

// IPC listeners for manual update checks
ipcMain.on('check_update', () => {
  autoUpdater.checkForUpdatesAndNotify();
});

ipcMain.on('restart_app', () => {
  autoUpdater.quitAndInstall();
});
