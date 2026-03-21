import { RingProgress, Sparkline } from './charts';
import {
    IconCalendar,
    IconCamera,
    IconCheck,
    IconClock,
    IconDocument,
    IconDollar,
    IconList,
    IconPencil,
    IconStore,
    IconTrash,
    IconUsers,
    IconX,
} from './icons';

const AVATAR =
    'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&q=80';

function KpiCard({ icon: Icon, iconWrapClass, label, value, chart }) {
    return (
        <div className="flex h-full min-h-[140px] flex-col rounded-xl bg-white p-5 shadow-card">
            <div className="flex items-start justify-between gap-3">
                <div
                    className={`flex h-11 w-11 items-center justify-center rounded-xl ${iconWrapClass}`}
                >
                    <Icon className="h-5 w-5" />
                </div>
                {chart}
            </div>
            <p className="mt-4 text-2xl font-bold tracking-tight text-slate-900">{value}</p>
            <p className="mt-1 text-sm font-medium text-slate-500">{label}</p>
        </div>
    );
}

export default function MainArea() {
    return (
        <main className="min-h-screen flex-1 bg-slate-100/80 pl-[260px]">
            <div className="mx-auto max-w-[1400px] px-6 pb-10 pt-6">
                <nav className="text-sm text-slate-500" aria-label="Breadcrumb">
                    <ol className="flex flex-wrap items-center gap-2">
                        <li>
                            <a href="#" className="hover:text-slate-700">
                                Dashboard
                            </a>
                        </li>
                        <li aria-hidden>/</li>
                        <li>
                            <a href="#" className="hover:text-slate-700">
                                Clients
                            </a>
                        </li>
                        <li aria-hidden>/</li>
                        <li className="font-medium text-slate-800">Client #193</li>
                    </ol>
                </nav>

                <section className="relative mt-5 overflow-hidden rounded-2xl shadow-card">
                    <div className="relative h-40 bg-gradient-to-r from-violet-400 via-violet-300 to-teal-300">
                        <button
                            type="button"
                            className="absolute right-4 top-3 inline-flex items-center gap-2 rounded-lg border border-white/40 bg-white/20 px-3 py-1.5 text-xs font-semibold text-slate-800 backdrop-blur-sm transition hover:bg-white/30"
                        >
                            <IconCamera className="h-3.5 w-3.5" />
                            Change Cover
                        </button>
                    </div>
                    <div className="relative bg-white px-6 pb-6 pt-0">
                        <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                            <div className="flex flex-col gap-4 sm:flex-row sm:items-end">
                                <div className="relative -mt-14 shrink-0 sm:-mt-16">
                                    <img
                                        src={AVATAR}
                                        alt=""
                                        className="h-28 w-28 rounded-full border-4 border-white object-cover shadow-md sm:h-32 sm:w-32"
                                    />
                                    <button
                                        type="button"
                                        className="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-slate-100 text-slate-600 shadow-sm hover:bg-slate-200"
                                        aria-label="Change photo"
                                    >
                                        <IconCamera className="h-3.5 w-3.5" />
                                    </button>
                                </div>
                                <div className="pb-1 pt-2 sm:pt-0">
                                    <h1 className="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                                        Jane Doe
                                    </h1>
                                    <p className="mt-1 text-sm text-slate-500 sm:text-base">
                                        Senior Event Coordinator
                                    </p>
                                    <div className="mt-4 flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            {'<- Back'}
                                        </button>
                                        <button
                                            type="button"
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            <IconPencil className="h-4 w-4" />
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            <IconTrash className="h-4 w-4" />
                                            Delete
                                        </button>
                                        <button
                                            type="button"
                                            className="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-violet-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white shadow-md hover:opacity-95"
                                        >
                                            <IconDocument className="h-4 w-4" />
                                            Log Interaction
                                        </button>
                                    </div>
                                    <p className="mt-4 text-sm font-bold text-slate-800">
                                        #Client ID: #193
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div className="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <KpiCard
                        icon={IconStore}
                        iconWrapClass="bg-violet-100 text-violet-700"
                        label="Total Booths"
                        value="15"
                        chart={<RingProgress percent={75} color="#7c3aed" />}
                    />
                    <KpiCard
                        icon={IconCalendar}
                        iconWrapClass="bg-pink-100 text-pink-600"
                        label="Total Bookings"
                        value="23"
                        chart={<Sparkline gradId="sparkPink" stroke="#db2777" fill="#db2777" />}
                    />
                    <KpiCard
                        icon={IconCheck}
                        iconWrapClass="bg-teal-100 text-teal-600"
                        label="Paid Booths"
                        value="12"
                        chart={<RingProgress percent={75} color="#0d9488" track="#ccfbf1" />}
                    />
                    <KpiCard
                        icon={IconDollar}
                        iconWrapClass="bg-slate-200 text-slate-700"
                        label="Total Revenue"
                        value="$14,500.00"
                        chart={<Sparkline gradId="sparkBlue" stroke="#1e3a8a" fill="#1e3a8a" />}
                    />
                </div>

                <div className="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-12">
                    <div className="xl:col-span-7">
                        <div className="rounded-2xl bg-white p-6 shadow-card">
                            <div className="flex items-center gap-2 border-b border-slate-100 pb-4">
                                <IconUsers className="h-5 w-5 text-violet-600" />
                                <h2 className="text-lg font-semibold text-slate-900">Client Information</h2>
                            </div>
                            <div className="mt-5 grid gap-6 lg:grid-cols-2">
                                <div className="space-y-3 text-sm">
                                    {[
                                        ['Name', 'Jane Doe'],
                                        ['Company', 'Global Tech Solutions'],
                                        ['Position', 'Senior Event Coordinator'],
                                        ['Email', 'jane.doe@gts.com'],
                                        ['Phone', '+1 (555) 123-4567'],
                                        ['Address', 'San Francisco, CA'],
                                        ['Gender', 'Female'],
                                    ].map(([k, v]) => (
                                        <div key={k} className="grid grid-cols-[110px_1fr] gap-2">
                                            <span className="text-slate-500">{k}</span>
                                            <span className="font-medium text-slate-900">{v}</span>
                                        </div>
                                    ))}
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-slate-500">Primary Contact For</p>
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {['CES', 'Dreamforce'].map((tag) => (
                                            <span
                                                key={tag}
                                                className="rounded-full bg-violet-50 px-3 py-1 text-sm font-medium text-violet-800"
                                            >
                                                {tag}
                                            </span>
                                        ))}
                                    </div>
                                </div>
                            </div>

                            <div className="mt-8 border-t border-slate-100 pt-6">
                                <h3 className="text-base font-semibold text-slate-900">
                                    Recent Client Activity Feed
                                </h3>
                                <ul className="mt-4 space-y-4">
                                    <li className="flex gap-3">
                                        <span className="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-slate-400" />
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm text-slate-800">
                                                <span className="font-medium text-slate-500">Log:</span> Call about
                                                booth requirements
                                            </p>
                                            <p className="mt-0.5 text-xs text-slate-400">Jan 12th</p>
                                        </div>
                                    </li>
                                    <li className="flex gap-3">
                                        <span className="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                            <IconCheck className="h-3 w-3" />
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm text-slate-800">CES Booking Confirmed</p>
                                            <p className="mt-0.5 text-xs text-slate-400">Jan 15th</p>
                                        </div>
                                    </li>
                                    <li className="flex gap-3">
                                        <span className="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-slate-200 text-slate-500">
                                            <IconDocument className="h-3 w-3" />
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="text-sm text-slate-800">
                                                Note added regarding custom booth design
                                            </p>
                                            <p className="mt-0.5 text-xs text-slate-400">Jan 18th</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div className="xl:col-span-5">
                        <div className="rounded-2xl bg-white p-6 shadow-card">
                            <div className="flex items-center gap-2 border-b border-slate-100 pb-4">
                                <IconList className="h-5 w-5 text-violet-600" />
                                <h2 className="text-lg font-semibold text-slate-900">Activity Statistics</h2>
                            </div>
                            <div className="mt-5 flex flex-wrap gap-4">
                                <div className="flex min-w-[140px] flex-1 items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                        <IconCheck className="h-5 w-5" />
                                    </span>
                                    <p className="text-sm font-semibold text-slate-900">0 Confirmed</p>
                                </div>
                                <div className="flex min-w-[140px] flex-1 items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                        <IconClock className="h-5 w-5" />
                                    </span>
                                    <p className="text-sm font-semibold text-slate-900">0 Reserved</p>
                                </div>
                                <div className="flex min-w-[140px] flex-1 basis-full items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3 sm:basis-auto">
                                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <IconX className="h-5 w-5" />
                                    </span>
                                    <p className="text-sm font-semibold text-slate-900">2 Cancelled (2)</p>
                                </div>
                            </div>

                            <div className="mt-8 border-t border-slate-100 pt-6">
                                <div className="flex items-center gap-2 pb-4">
                                    <IconCalendar className="h-5 w-5 text-violet-600" />
                                    <h2 className="text-lg font-semibold text-slate-900">Upcoming Client Events</h2>
                                </div>
                                <div className="overflow-x-auto rounded-xl border border-slate-100">
                                    <table className="min-w-full text-left text-sm">
                                        <thead className="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th className="whitespace-nowrap px-4 py-3">Event Name</th>
                                                <th className="whitespace-nowrap px-4 py-3">Date</th>
                                                <th className="whitespace-nowrap px-4 py-3">Location</th>
                                                <th className="whitespace-nowrap px-4 py-3">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-slate-100 bg-white">
                                            <tr>
                                                <td className="px-4 py-3 font-medium text-slate-900">
                                                    Dreamforce &apos;24 Booth
                                                </td>
                                                <td className="px-4 py-3 text-slate-600">Sep 10th - 11th</td>
                                                <td className="px-4 py-3 text-slate-600">SF Moscone</td>
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                                                        Confirmed
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td className="px-4 py-3 font-medium text-slate-900">CES &apos;25 Booth</td>
                                                <td className="px-4 py-3 text-slate-600">Jan 8th - 11th</td>
                                                <td className="px-4 py-3 text-slate-600">Las Vegas</td>
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-900">
                                                        Reservation
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td className="px-4 py-3 font-medium text-slate-900">
                                                    InnovateX &apos;25 Gala
                                                </td>
                                                <td className="px-4 py-3 text-slate-600">Mar 1st</td>
                                                <td className="px-4 py-3 text-slate-600">San Jose</td>
                                                <td className="px-4 py-3">
                                                    <span className="inline-flex rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">
                                                        Planning
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    );
}
