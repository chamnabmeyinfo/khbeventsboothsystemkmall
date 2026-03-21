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

function NavItem({ icon: Icon, label, active, hasDropdown }) {
    return (
        <li>
            <a
                href="#"
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

export default function Sidebar() {
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
                    <NavItem icon={IconDashboard} label="Dashboard" />
                    <NavItem icon={IconUsers} label="Clients" active />
                </NavGroup>
                <NavGroup title="Operations">
                    <NavItem icon={IconGrid} label="Booth Inventory" />
                    <NavItem icon={IconMap} label="Floor Plans" />
                    <NavItem icon={IconCalendar} label="Bookings" />
                    <NavItem icon={IconLink} label="Affiliates" />
                </NavGroup>
                <NavGroup title="Human Resources">
                    <NavItem icon={IconChart} label="Analytics" />
                    <NavItem icon={IconBriefcase} label="HR Management" hasDropdown />
                </NavGroup>
                <NavGroup title="Finance & Assets">
                    <NavItem icon={IconDollar} label="Finance Hub" hasDropdown />
                </NavGroup>
                <NavGroup title="System">
                    <NavItem icon={IconShield} label="Security & Staff" />
                    <NavItem icon={IconSettings} label="Global Settings" />
                </NavGroup>
            </nav>

            <div className="border-t border-white/10 p-4">
                <div className="flex items-center gap-3 rounded-xl bg-white/[0.04] px-3 py-3">
                    <button
                        type="button"
                        className="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/10 hover:text-white"
                        aria-label="Settings"
                    >
                        <IconSettings className="h-5 w-5" />
                    </button>
                    <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-violet-600 text-sm font-semibold text-white">
                        V
                    </div>
                    <div className="min-w-0 flex-1">
                        <p className="truncate text-sm font-semibold text-white">vutha_admin</p>
                        <p className="truncate text-xs text-slate-500">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>
    );
}
