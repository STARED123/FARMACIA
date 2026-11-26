const pwaScript = document.currentScript;
const serviceWorkerUrl = pwaScript?.dataset.serviceWorkerUrl;

if ('serviceWorker' in navigator && serviceWorkerUrl) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register(serviceWorkerUrl).catch((error) => {
            console.error('No se pudo registrar el service worker de Farmacia San Juan.', error);
        });
    });
}

let deferredInstallPrompt = null;
const installButtons = document.querySelectorAll('[data-pwa-install]');

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
    installButtons.forEach((button) => {
        button.hidden = false;
    });
});

installButtons.forEach((button) => {
    button.addEventListener('click', async () => {
        if (!deferredInstallPrompt) {
            return;
        }

        try {
            await deferredInstallPrompt.prompt();
            await deferredInstallPrompt.userChoice;
        } catch (error) {
            console.error('No se pudo iniciar la instalación de la PWA.', error);
        } finally {
            deferredInstallPrompt = null;
            installButtons.forEach((installButton) => {
                installButton.hidden = true;
            });
        }
    });
});

window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    installButtons.forEach((button) => {
        button.hidden = true;
    });
});
