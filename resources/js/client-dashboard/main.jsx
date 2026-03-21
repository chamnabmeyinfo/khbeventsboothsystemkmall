import '../../css/client-dashboard.css';
import { createRoot } from 'react-dom/client';
import App from './App';

const el = document.getElementById('client-profile-dashboard-root');
if (el) {
    createRoot(el).render(<App />);
}
