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

function statusPillClass(tone) {
    switch (tone) {
        case 'green':
            return 'bg-emerald-100 text-emerald-800';
        case 'yellow':
            return 'bg-amber-100 text-amber-900';
        case 'red':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-slate-200 text-slate-700';
    }
}

function formatMoney(n) {
    const num = Number(n);
    if (Number.isNaN(num)) {
        return '$0.00';
    }
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(num);
}

export default function MainArea({ data }) {
    const { client, stats, primaryTags, activity, upcomingEvents, routes } = data;
    const r = routes || {};
    const c = client || {};
    const s = stats || {};

    const fields = [
        ['Name', c.name || '—'],
        ['Company', c.company || 'N/A'],
        ['Position', c.position || 'N/A'],
        ['Email', c.email || 'N/A'],
        ['Phone', c.phone || 'N/A'],
        ['Address', c.address || 'N/A'],
        ['Gender', c.genderLabel || 'N/A'],
    ];

    const boothsRing = Math.min(100, Math.max(0, Number(s.boothsRingPct) || 0));
    const paidRing = Math.min(100, Math.max(0, Number(s.paidRingPct) || 0));

    const onDelete = () => {
        if (typeof window.submitClientDeleteForm === 'function') {
            window.submitClientDeleteForm();
        }
    };

    const openCover = () => {
        if (typeof window.openCoverUploadModal === 'function') {
            window.openCoverUploadModal();
        }
    };

    const openAvatar = () => {
        if (typeof window.openAvatarUploadModal === 'function') {
            window.openAvatarUploadModal();
        }
    };

    return (
        <main className="min-h-screen flex-1 bg-slate-100/80 pl-[260px]">
            <div className="mx-auto max-w-[1400px] px-6 pb-10 pt-6">
                <nav className="text-sm text-slate-500" aria-label="Breadcrumb">
                    <ol className="flex flex-wrap items-center gap-2">
                        <li>
                            <a href={r.dashboard || '#'} className="hover:text-slate-700">
                                Dashboard
                            </a>
                        </li>
                        <li aria-hidden>/</li>
                        <li>
                            <a href={r.clientsIndex || '#'} className="hover:text-slate-700">
                                Clients
                            </a>
                        </li>
                        <li aria-hidden>/</li>
                        <li className="font-medium text-slate-800">Client #{c.id}</li>
                    </ol>
                </nav>

                <section className="relative mt-5 overflow-hidden rounded-2xl shadow-card">
                    <div
                        id="profileCover"
                        className={`relative h-40 overflow-hidden bg-gradient-to-r from-violet-400 via-violet-300 to-teal-300 ${
                            c.coverUrl ? '' : ''
                        }`}
                    >
                        {c.coverUrl ? (
                            <img
                                id="coverImage"
                                src={c.coverUrl}
                                alt=""
                                className="absolute inset-0 h-full w-full object-cover"
                                style={{ objectPosition: c.coverPosition || 'center center' }}
                                data-initial-position={c.coverPosition || 'center center'}
                            />
                        ) : null}
                        <button
                            type="button"
                            onClick={openCover}
                            className="absolute right-4 top-3 z-10 inline-flex items-center gap-2 rounded-lg border border-white/40 bg-white/20 px-3 py-1.5 text-xs font-semibold text-slate-800 backdrop-blur-sm transition hover:bg-white/30"
                        >
                            <IconCamera className="h-3.5 w-3.5" />
                            Change Cover
                        </button>
                    </div>
                    <div className="relative bg-white px-6 pb-6 pt-0">
                        <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                            <div className="flex flex-col gap-4 sm:flex-row sm:items-end">
                                <div className="relative -mt-14 shrink-0 sm:-mt-16">
                                    {c.avatarUrl ? (
                                        <img
                                            src={c.avatarUrl}
                                            alt=""
                                            className="h-28 w-28 rounded-full border-4 border-white object-cover shadow-md sm:h-32 sm:w-32"
                                        />
                                    ) : (
                                        <div className="flex h-28 w-28 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-violet-400 to-teal-300 text-2xl font-bold text-white shadow-md sm:h-32 sm:w-32">
                                            {(c.name || '?').charAt(0).toUpperCase()}
                                        </div>
                                    )}
                                    <button
                                        type="button"
                                        onClick={openAvatar}
                                        className="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-slate-100 text-slate-600 shadow-sm hover:bg-slate-200"
                                        aria-label="Change photo"
                                    >
                                        <IconCamera className="h-3.5 w-3.5" />
                                    </button>
                                </div>
                                <div className="pb-1 pt-2 sm:pt-0">
                                    <h1 className="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                                        {c.name}
                                    </h1>
                                    <p className="mt-1 text-sm text-slate-500 sm:text-base">
                                        {c.position || '—'}
                                    </p>
                                    <div className="mt-4 flex flex-wrap gap-2">
                                        <a
                                            href={r.clientBack || r.clientsIndex || '#'}
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            {'<- Back'}
                                        </a>
                                        <a
                                            href={r.clientEdit || '#'}
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            <IconPencil className="h-4 w-4" />
                                            Edit
                                        </a>
                                        <button
                                            type="button"
                                            onClick={onDelete}
                                            className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50"
                                        >
                                            <IconTrash className="h-4 w-4" />
                                            Delete
                                        </button>
                                        <a
                                            href={r.booksIndex || '#'}
                                            className="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-violet-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white shadow-md hover:opacity-95"
                                        >
                                            <IconDocument className="h-4 w-4" />
                                            Log Interaction
                                        </a>
                                    </div>
                                    <p className="mt-4 text-sm font-bold text-slate-800">
                                        #Client ID: #{c.id}
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
                        value={String(s.totalBooths ?? 0)}
                        chart={<RingProgress percent={boothsRing || 8} color="#7c3aed" />}
                    />
                    <KpiCard
                        icon={IconCalendar}
                        iconWrapClass="bg-pink-100 text-pink-600"
                        label="Total Bookings"
                        value={String(s.totalBookings ?? 0)}
                        chart={<Sparkline gradId="sparkPink" stroke="#db2777" fill="#db2777" />}
                    />
                    <KpiCard
                        icon={IconCheck}
                        iconWrapClass="bg-teal-100 text-teal-600"
                        label="Paid Booths"
                        value={String(s.paidBooths ?? 0)}
                        chart={<RingProgress percent={paidRing || 8} color="#0d9488" track="#ccfbf1" />}
                    />
                    <KpiCard
                        icon={IconDollar}
                        iconWrapClass="bg-slate-200 text-slate-700"
                        label="Total Revenue"
                        value={formatMoney(s.totalRevenue)}
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
                                    {fields.map(([k, v]) => (
                                        <div key={k} className="grid grid-cols-[110px_1fr] gap-2">
                                            <span className="text-slate-500">{k}</span>
                                            <span className="font-medium text-slate-900">{v}</span>
                                        </div>
                                    ))}
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-slate-500">Primary Contact For</p>
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {(primaryTags || []).length ? (
                                            primaryTags.map((tag) => (
                                                <span
                                                    key={tag}
                                                    className="rounded-full bg-violet-50 px-3 py-1 text-sm font-medium text-violet-800"
                                                >
                                                    {tag}
                                                </span>
                                            ))
                                        ) : (
                                            <span className="text-sm text-slate-400">—</span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className="mt-8 border-t border-slate-100 pt-6">
                                <h3 className="text-base font-semibold text-slate-900">
                                    Recent Client Activity Feed
                                </h3>
                                <ul className="mt-4 space-y-4">
                                    {(activity || []).length === 0 ? (
                                        <li className="text-sm text-slate-500">No recent activity.</li>
                                    ) : (
                                        activity.map((item, idx) => (
                                            <li key={idx} className="flex gap-3">
                                                {item.variant === 'success' ? (
                                                    <span className="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                                                        <IconCheck className="h-3 w-3" />
                                                    </span>
                                                ) : item.variant === 'note' ? (
                                                    <span className="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-slate-200 text-slate-500">
                                                        <IconDocument className="h-3 w-3" />
                                                    </span>
                                                ) : (
                                                    <span className="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-slate-400" />
                                                )}
                                                <div className="min-w-0 flex-1">
                                                    <p className="text-sm text-slate-800">{item.title}</p>
                                                    {item.date ? (
                                                        <p className="mt-0.5 text-xs text-slate-400">{item.date}</p>
                                                    ) : null}
                                                </div>
                                            </li>
                                        ))
                                    )}
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
                                    <p className="text-sm font-semibold text-slate-900">
                                        {s.confirmed ?? 0} Confirmed
                                    </p>
                                </div>
                                <div className="flex min-w-[140px] flex-1 items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                                        <IconClock className="h-5 w-5" />
                                    </span>
                                    <p className="text-sm font-semibold text-slate-900">
                                        {s.reserved ?? 0} Reserved
                                    </p>
                                </div>
                                <div className="flex min-w-[140px] flex-1 basis-full items-center gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3 sm:basis-auto">
                                    <span className="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600">
                                        <IconX className="h-5 w-5" />
                                    </span>
                                    <p className="text-sm font-semibold text-slate-900">
                                        {s.cancelled ?? 0} Cancelled ({s.cancelled ?? 0})
                                    </p>
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
                                            {(upcomingEvents || []).length === 0 ? (
                                                <tr>
                                                    <td colSpan={4} className="px-4 py-6 text-center text-slate-500">
                                                        No bookings to display.
                                                    </td>
                                                </tr>
                                            ) : (
                                                upcomingEvents.map((row, i) => (
                                                    <tr key={i}>
                                                        <td className="px-4 py-3 font-medium text-slate-900">
                                                            {row.name}
                                                        </td>
                                                        <td className="px-4 py-3 text-slate-600">{row.date}</td>
                                                        <td className="px-4 py-3 text-slate-600">{row.location}</td>
                                                        <td className="px-4 py-3">
                                                            <span
                                                                className={`inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold ${statusPillClass(row.statusTone)}`}
                                                            >
                                                                {row.statusLabel}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                ))
                                            )}
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
