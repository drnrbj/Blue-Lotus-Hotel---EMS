// src/components/layout/Sidebar.tsx
import logo from "@/assets/logo.png";
import { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import {
  LayoutDashboard,
  Users,
  Clock,
  DollarSign,
  BarChart3,
  Briefcase,
  ChevronLeft,
  LogOut,
} from "lucide-react";
import { cn } from "@/lib/utils";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";

interface NavItem {
  label: string;
  path: string;
  icon: React.ElementType;
  roles?: string[];
}

const NAV_ITEMS: NavItem[] = [
  { label: "Dashboard",   path: "/",            icon: LayoutDashboard },
  { label: "Employee",    path: "/employees",   icon: Users,      roles: ["Admin", "HR"] },
  { label: "Attendance",  path: "/attendance",  icon: Clock,      roles: ["Admin", "HR"] },
  { label: "Payroll",     path: "/payroll",     icon: DollarSign, roles: ["Accountant"] },
  { label: "Performance", path: "/performance", icon: BarChart3,  roles: ["Admin", "HR"] },
  { label: "Recruitment", path: "/recruitment", icon: Briefcase,  roles: ["Admin", "HR"] },
];

interface SidebarProps {
  onNavigate?: (path: string) => void;
}

export function Sidebar({ onNavigate }: SidebarProps = {}) {
  const { pathname } = useLocation();
  const { user, logout } = useAuth();
  const [collapsed, setCollapsed] = useState(false);

  const role = user?.role ?? "";

  const visibleItems = NAV_ITEMS.filter(
    (item) => !item.roles || item.roles.includes(role)
  );

  return (
    <aside
      style={{ backgroundColor: "#2B3588" }}
      className={cn(
        "flex flex-col transition-all duration-300 h-screen sticky top-0 shadow-xl",
        collapsed ? "w-20" : "w-64"
      )}
    >
      {/* ── Logo ── */}
      <div
        className={cn(
          "flex h-20 items-center px-4 flex-shrink-0 border-b",
          collapsed ? "justify-center" : "gap-3"
        )}
        style={{ borderColor: "rgba(250,236,29,0.15)" }}
      >
        {/* Logo image — replace src with your actual logo path */}
        <img
          src={logo}
          alt="Blue Lotus Hotel"
          style={{ width: 81, height: 48 }}
          className="shrink-0 object-contain"
        />

        {!collapsed && (
          <div className="leading-tight">
            <p className="font-extrabold text-white tracking-wide"
               style={{ fontSize: "15px", lineHeight: "1.2" }}>
              BLUE LOTUS HOTEL
            </p>
            <p className="font-medium text-white/60"
               style={{ fontSize: "10px", letterSpacing: "0.04em" }}>
              Employee Management System
            </p>
          </div>
        )}
      </div>

      {/* ── Navigation ── */}
      <nav className="flex-1 overflow-y-auto py-6 px-3">
        <TooltipProvider delayDuration={300}>
          <div className="space-y-1">
            {visibleItems.map((item) => {
              const Icon = item.icon;
              const isActive =
                pathname === item.path ||
                (item.path !== "/" && pathname.startsWith(item.path));

              const NavLink = () => (
                <Link
                  to={item.path}
                  onClick={() => onNavigate?.(item.path)}
                  className={cn(
                    "flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200",
                    isActive
                      ? "text-white shadow-md"
                      : "text-white/60 hover:text-white",
                    collapsed && "justify-center px-2"
                  )}
                  style={isActive ? { backgroundColor: "#44AFE4" } : undefined}
                  onMouseEnter={e => {
                    if (!isActive)
                      (e.currentTarget as HTMLElement).style.backgroundColor = "rgba(68,175,228,0.15)";
                  }}
                  onMouseLeave={e => {
                    if (!isActive)
                      (e.currentTarget as HTMLElement).style.backgroundColor = "";
                  }}
                >
                  <Icon className={cn(
                    "h-5 w-5 shrink-0",
                    isActive ? "text-white" : "text-white/50"
                  )} />
                  {!collapsed && <span>{item.label}</span>}
                </Link>
              );

              if (collapsed) {
                return (
                  <Tooltip key={item.path}>
                    <TooltipTrigger asChild><NavLink /></TooltipTrigger>
                    <TooltipContent
                      side="right"
                      className="font-medium border-none"
                      style={{ backgroundColor: "#44AFE4", color: "#fff" }}
                    >
                      {item.label}
                    </TooltipContent>
                  </Tooltip>
                );
              }

              return <NavLink key={item.path} />;
            })}
          </div>
        </TooltipProvider>
      </nav>

      {/* ── Bottom: User info + collapse + logout ── */}
      <div
        className="flex-shrink-0 px-3 py-4 border-t"
        style={{ borderColor: "rgba(250,236,29,0.15)" }}
      >
        {collapsed ? (
          /* Collapsed state */
          <div className="flex flex-col items-center gap-3">
            {/* Collapse toggle */}
            <Tooltip>
              <TooltipTrigger asChild>
                <Button
                  variant="ghost" size="sm"
                  className="h-8 w-8 p-0 text-white/50 hover:text-white rotate-180"
                  style={{ backgroundColor: "transparent" }}
                  onClick={() => setCollapsed(false)}
                >
                  <ChevronLeft className="h-4 w-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent side="right" style={{ backgroundColor: "#44AFE4", color: "#fff" }} className="border-none">
                Expand
              </TooltipContent>
            </Tooltip>

            {/* Logout */}
            <Tooltip>
              <TooltipTrigger asChild>
                <Button
                  variant="ghost" size="sm"
                  className="h-8 w-8 p-0 text-white/50 hover:text-red-400"
                  style={{ backgroundColor: "transparent" }}
                  onClick={logout}
                >
                  <LogOut className="h-4 w-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent side="right" style={{ backgroundColor: "#44AFE4", color: "#fff" }} className="border-none">
                Logout
              </TooltipContent>
            </Tooltip>
          </div>
        ) : (
          /* Expanded state */
          <div
            className="flex items-center gap-2 rounded-lg px-2 py-2"
            style={{ backgroundColor: "rgba(255,255,255,0.06)" }}
          >
            {/* Name + Role */}
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-white truncate">
                {user?.name || "System Admin"}
              </p>
              <p className="text-xs truncate" style={{ color: "#FAEC1D", opacity: 0.8 }}>
                {user?.role || "Admin"}
              </p>
            </div>

            {/* Collapse toggle */}
            <Tooltip>
              <TooltipTrigger asChild>
                <Button
                  variant="ghost" size="sm"
                  className="h-7 w-7 p-0 shrink-0 text-white/40 hover:text-white"
                  style={{ backgroundColor: "transparent" }}
                  onClick={() => setCollapsed(true)}
                >
                  <ChevronLeft className="h-4 w-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent side="right" style={{ backgroundColor: "#44AFE4", color: "#fff" }} className="border-none">
                Collapse
              </TooltipContent>
            </Tooltip>

            {/* Logout */}
            <Tooltip>
              <TooltipTrigger asChild>
                <Button
                  variant="ghost" size="sm"
                  className="h-7 w-7 p-0 shrink-0 text-white/40 hover:text-red-400"
                  style={{ backgroundColor: "transparent" }}
                  onClick={logout}
                >
                  <LogOut className="h-4 w-4" />
                </Button>
              </TooltipTrigger>
              <TooltipContent side="right" style={{ backgroundColor: "#44AFE4", color: "#fff" }} className="border-none">
                Logout
              </TooltipContent>
            </Tooltip>
          </div>
        )}
      </div>
    </aside>
  );
}