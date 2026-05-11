import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { DashboardLayout } from "@/components/layout/DashboardLayout";
import { useAuth } from "@/hooks/useAuth";
import { authFetch } from "@/hooks/api";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  Users, CheckCircle, Clock, Briefcase,
  CalendarDays, UserPlus, Loader2, AlertCircle, ChevronRight,
} from "lucide-react";
import { cn } from "@/lib/utils";

interface DashboardData {
  total_employees: number;
  present_today: number;
  absent_today: number;
  pending_leaves: number;
  open_jobs: number;
  dept_headcount: { department: string; count: number }[];
  recent_hires: { id: number; full_name: string; department: string; job_category: string; start_date: string }[];
  pending_leave_list: { id: number; employee: string; leave_type: string; start_date: string; end_date: string; reason: string }[];
}

function StatCard({
  icon: Icon, label, value, sub, iconBg, to,
}: {
  icon: React.ElementType;
  label: string;
  value: string | number;
  sub?: string;
  iconBg: string;
  to?: string;
}) {
  const inner = (
    <div className="rounded-xl border border-border bg-card p-4 flex items-center gap-4 hover:border-border/80 hover:shadow-sm transition-all cursor-pointer">
      <div className={cn("h-11 w-11 rounded-xl flex items-center justify-center shrink-0", iconBg)}>
        <Icon className="h-5 w-5" />
      </div>
      <div className="min-w-0">
        <p className="text-2xl font-bold text-foreground leading-none">{value}</p>
        {sub && <p className="text-xs text-muted-foreground mt-0.5">{sub}</p>}
        <p className="text-xs text-muted-foreground mt-0.5 truncate">{label}</p>
      </div>
    </div>
  );
  return to ? <Link to={to}>{inner}</Link> : <div>{inner}</div>;
}

function Section({ title, icon: Icon, to, children }: {
  title: string; icon: React.ElementType; to?: string; children: React.ReactNode;
}) {
  return (
    <div className="rounded-xl border border-border bg-card overflow-hidden">
      <div className="flex items-center justify-between px-4 py-3 border-b border-border bg-muted/30">
        <div className="flex items-center gap-2">
          <Icon className="h-4 w-4 text-muted-foreground" />
          <span className="text-sm font-medium text-foreground">
            {title}
          </span>
        </div>
        {to && (
          <Link to={to} className="text-xs text-blue-600 hover:underline flex items-center gap-0.5">
            View all <ChevronRight className="h-3 w-3" />
          </Link>
        )}
      </div>
      <div className="divide-y divide-border">{children}</div>
    </div>
  );
}

export default function Dashboard() {
  const { user } = useAuth();
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  const isAccountant = user?.role === "Accountant";

  useEffect(() => {
    authFetch("/api/dashboard/stats")
      .then(r => r.json())
      .then(body => setData(body.data ?? body))
      .catch(() => setError(true))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <DashboardLayout>
        <div className="flex items-center justify-center h-64">
          <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
        </div>
      </DashboardLayout>
    );
  }

  if (error || !data) {
    return (
      <DashboardLayout>
        <div className="flex flex-col items-center justify-center h-64 text-muted-foreground gap-3">
          <AlertCircle className="h-10 w-10" />
          <p>Could not load dashboard data.</p>
          <Button variant="outline" onClick={() => window.location.reload()}>Retry</Button>
        </div>
      </DashboardLayout>
    );
  }

  const attendanceRate = data.total_employees > 0
    ? Math.round((data.present_today / data.total_employees) * 100)
    : 0;

  return (
    <DashboardLayout>
      <div className="space-y-6">

        {/* Title */}
        <h1 className="text-3xl font-bold text-foreground">Dashboard</h1>

        {/* KPI Cards — 6 cards, 3 per row */}
        <div className="grid grid-cols-2 lg:grid-cols-3 gap-4">
          <StatCard
            icon={Users} label="Total Employees"
            value={data.total_employees}
            iconBg="bg-blue-100 text-blue-600"
            to="/employees"
          />
          <StatCard
            icon={CheckCircle} label="Present Today"
            value={data.present_today}
            iconBg="bg-green-100 text-green-600"
            to="/attendance"
          />
          <StatCard
            icon={Users} label="Absent Today"
            value={data.absent_today}
            iconBg="bg-red-100 text-red-600"
            to="/attendance"
          />
          <StatCard
            icon={Clock} label="Pending Leaves"
            value={data.pending_leaves}
            iconBg={data.pending_leaves > 0 ? "bg-amber-100 text-amber-600" : "bg-muted text-muted-foreground"}
            to="/attendance"
          />
          <StatCard
            icon={Briefcase} label="Open Positions"
            value={data.open_jobs}
            iconBg="bg-purple-100 text-purple-600"
            to="/recruitment"
          />
          <StatCard
            icon={UserPlus} label="Recent Hires"
            value={data.recent_hires?.length ?? 0}
            iconBg="bg-indigo-100 text-indigo-600"
            to="/employees"
          />
        </div>

        {/* Two column section */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">

          {/* Pending Leave Requests */}
          <Section title="Pending Leave Requests" icon={Clock} to="/attendance">
            {data.pending_leave_list && data.pending_leave_list.length > 0 ? (
              data.pending_leave_list.map(lr => (
                <div key={lr.id} className="flex items-center justify-between px-4 py-3">
                  <div className="min-w-0">
                    <p className="text-sm font-medium truncate">{lr.employee}</p>
                    <p className="text-xs text-muted-foreground">
                      {lr.leave_type?.replace("_", " ")} · {lr.start_date} → {lr.end_date}
                    </p>
                  </div>
                  <Badge className="ml-3 shrink-0 bg-amber-100 text-amber-700 border-0 text-xs">Pending</Badge>
                </div>
              ))
            ) : (
              <div className="px-4 py-8 text-center text-sm text-muted-foreground">
                No pending leave requests.
              </div>
            )}
          </Section>

          {/* Recent Hires */}
          <Section title="Recent Hires" icon={UserPlus} to="/employees">
            {data.recent_hires && data.recent_hires.length > 0 ? (
              data.recent_hires.map(hire => (
                <div key={hire.id} className="flex items-center gap-3 px-4 py-3">
                  <div className="min-w-0 flex-1">
                    <p className="text-sm font-medium truncate">{hire.full_name}</p>
                    <p className="text-xs text-muted-foreground truncate">
                      {hire.job_category} · {hire.department}
                    </p>
                  </div>
                  <p className="text-xs text-muted-foreground shrink-0">
                    {hire.start_date
                      ? new Date(hire.start_date).toLocaleDateString("en-PH", { month: "short", day: "numeric" })
                      : "—"}
                  </p>
                </div>
              ))
            ) : (
              <div className="px-4 py-8 text-center text-sm text-muted-foreground">
                No new hires in the last 30 days
              </div>
            )}
          </Section>

        </div>

        {/* Dept headcount */}
        {data.dept_headcount && data.dept_headcount.length > 0 && (
          <div className="rounded-xl border border-border bg-card p-4">
            <span className="text-sm font-medium text-foreground mb-3 block">Headcount by Department</span>
            <div className="space-y-2">
              {data.dept_headcount.map(dept => {
                const max = Math.max(...data.dept_headcount.map(d => d.count));
                const pct = max > 0 ? (dept.count / max) * 100 : 0;
                return (
                  <div key={dept.department} className="flex items-center gap-3 text-sm">
                    <span className="w-36 text-muted-foreground truncate text-xs">{dept.department}</span>
                    <div className="flex-1 h-2 rounded-full bg-muted overflow-hidden">
                      <div className="h-full bg-blue-500 rounded-full transition-all" style={{ width: `${pct}%` }} />
                    </div>
                    <span className="w-6 text-right text-xs font-medium">{dept.count}</span>
                  </div>
                );
              })}
            </div>
          </div>
        )}

      </div>
    </DashboardLayout>
  );
}