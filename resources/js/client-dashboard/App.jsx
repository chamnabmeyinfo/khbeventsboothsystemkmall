import MainArea from './MainArea';
import Sidebar from './Sidebar';

export default function App() {
    return (
        <div className="flex min-h-screen font-sans text-slate-800">
            <Sidebar />
            <MainArea />
        </div>
    );
}
