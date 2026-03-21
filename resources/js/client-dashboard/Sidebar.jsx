import {
    IconBriefcase,
    IconCalendar,
    IconChart,
    IconChevronDown,
    IconDashboard,
    IconDollar,
    IconGrid,
    IconLink,
    IconMap,
    IconSettings,
    IconShield,
    IconUsers,
} from './icons';

function NavGroup({ title, children }) {
    return (
        <div className="mt-6 first:mt-0">
            <p className="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">{title}</p>
            <ul className="mt-2 space-y-0.5">{children}</ul>
        </div>
    );
}

function NavItem({ icon: Icon, label, active, hasDropdown, href = '#' }) {
    return (
        <li>
            <a
                href={href}
                className={`group flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors ${
                    active
                        ? 'relative bg-white/[0.06] text-sky-400'
                        : 'text-slate-300 hover:bg-sidebar-hover hover:text-white'
                }`}
            >
                {active && <span className="absolute right-0 top-1/2 h-8 w-1 -translate-y-1/2 rounded-l bg-sky-500" />}
                <Icon className={`h-5 w-5 shrink-0 ${active ? 'text-sky-400' : 'text-slate-400 group-hover:text-white'}`} />
                <span className="flex-1 font-medium">{label}</span>
                {hasDropdown && (
                    <IconChevronDown className="h-4 w-4 shrink-0 text-slate-500 group-hover:text-slate-300" />
                )}
            </a>
        </li>
    );
}

const DEFAULT_NAV = {
    dashboard: '#',
    clientsIndex: '#',
    boothsIndex: '#',
    floorPlansIndex: '#',
    booksIndex: '#',
    affiliatesIndex: '#',
    hrDashboard: '#',
    financeDashboard: '#',
    usersIndex: '#',
    settingsIndex: '#',
};

const DEFAULT_USER = {
    initial: 'U',
    username: 'User',
    roleLabel: 'User',
};

export default function Sidebar({ nav: navProp, user: userProp }) {
    const nav = { ...DEFAULT_NAV, ...navProp };
    const user = { ...DEFAULT_USER, ...userProp };

    return (
        <aside className="fixed left-0 top-0 z-40 flex h-screen w-[260px] flex-col bg-sidebar text-slate-200">
            <div className="flex items-start gap-3 border-b border-white/10 px-5 py-6">
                <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500 to-teal-400 text-sm font-bold text-white">
                    KHB
                </div>
                <div>
                    <p className="text-base font-bold leading-tight text-white">KHB Events</p>
                    <p className="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">
                        MANAGEMENT SYSTEM
                    </p>
                </div>
            </div>

            <nav className="flex-1 overflow-y-auto px-3 py-4">
                <NavGroup title="Overview">
                    <NavItem icon={IconDashboard} label="Dashboard" href={nav.dashboard} />
                    <NavItem icon={IconUsers} label="Clients" active href={nav.clientsIndex} />
                </NavGroup>
                <NavGroup title="Operations">
                    <NavItem icon={IconGrid} label="Booth Inventory" href={nav.boothsIndex} />
                    <NavItem icon={IconMap} label="Floor Plans" href={nav.floorPlansIndex} />
                    <NavItem icon={IconCalendar} label="Bookings" href={nav.booksIndex} />
                    <NavItem icon={IconLink} label="Affiliates" href={nav.affiliatesIndex} />
                </NavGroup>
                <NavGroup title="Human Resources">
                    <NavItem icon={IconChart} label="Analytics" href={nav.dashboard} />
                    <NavItem icon={IconBriefcase} label="HR Management" hasDropdown href={nav.hrDashboard} />
                </NavGroup>
                <NavGroup title="Finance & Assets">
                    <NavItem icon={IconDollar} label="Finance Hub" hasDropdown href={nav.financeDashboard} />
                </NavGroup>
                <NavGroup title="System">
                    <NavItem icon={IconShield} label="Security & Staff" href={nav.usersIndex} />
                    <NavItem icon={IconSettings} label="Global Settings" href={nav.settingsIndex} />
                </NavGroup>
            </nav>

            <div className="border-t border-white/10 p-4">
                <div className="flex items-center gap-3 rounded-xl bg-white/[0.04] px-3 py-3">
                    <a
                        href={nav.settingsIndex}
                        className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/10 hover:text-white"
                        aria-label="Settings"
                    >
                        <IconSettings className="h-5 w-5" />
                    </a>
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-violet-600 text-sm font-semibold text-white">
                        {user.initial}
                    </div>
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-semibold text-white">{user.username}</p>
                        <p className="truncate text-xs text-slate-500">{user.roleLabel}</p>
                    </div>
                </div>
            </div>
        </aside>
    );
}
