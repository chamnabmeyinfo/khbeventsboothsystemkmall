import MainArea from './MainArea';

/** Renders inside layouts.admin (default header + sidebar). No duplicate app sidebar. */
export default function App({ initialData }) {
    return (
        <div className="font-sans text-slate-800">
            <MainArea data={initialData} />
        </div>
    );
}
