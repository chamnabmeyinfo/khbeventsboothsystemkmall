import '../../css/client-dashboard.css';
import { createRoot } from 'react-dom/client';
import App from './App';
import { DEMO_PAYLOAD } from './demoPayload';

function readPayload() {
    const el = document.getElementById('client-profile-payload');
    if (el?.textContent?.trim()) {
        try {
            return JSON.parse(el.textContent);
        } catch (e) {
            console.error('Invalid client-profile-payload JSON', e);
        }
    }
    return DEMO_PAYLOAD;
}

const mountEl =
    document.getElementById('client-profile-root') ||
    document.getElementById('client-profile-dashboard-root');

if (mountEl) {
    createRoot(mountEl).render(<App initialData={readPayload()} />);
}
