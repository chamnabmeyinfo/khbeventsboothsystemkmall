import MainArea from './MainArea';
import Sidebar from './Sidebar';

export default function App({ initialData }) {
    return (
        <div className="flex min-h-screen font-sans text-slate-800">
            <Sidebar nav={initialData.routes} user={initialData.user} />
            <MainArea data={initialData} />
        </div>
    );
}
