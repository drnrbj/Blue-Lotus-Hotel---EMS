// src/pages/Attendance.tsx
import { useState, useEffect, useRef, useCallback } from "react";
import { DashboardLayout } from "@/components/layout/DashboardLayout";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/hooks/useAuth";
import { authFetch } from "@/hooks/api";
import {
  Users, UserCheck, UserX, Clock, Calendar, Download,
  Upload, Loader2, CheckCircle, XCircle, Search, AlertCircle,
  RefreshCw, Pencil, Plus,
} from "lucide-react";
import { cn } from "@/lib/utils";

// ─── Types ────────────────────────────────────────────────────────────────────

interface AttendanceRecord {
  id: number;
  employee_id: number;
  date: string;
  time_in: string | null;
  time_out: string | null;
  status: "present" | "late" | "absent" | "on_leave" | "half_day";
  minutes_late: number;
  hours_worked: number;
  notes: string | null;
  employee?: { id: number; first_name: string; last_name: string; department: string; shift_sched: string };
}

interface LeaveRequest {
  id: number;
  employee_id: number;
  employee_name: string | null;
  department: string | null;
  leave_type: string;
  start_date: string;
  end_date: string;
  days_requested: number;
  number_of_days?: number;
  reason: string;
  status: "pending" | "approved" | "rejected" | "cancelled";
  rejected_reason?: string | null;
  created_at: string;
}

interface LeaveBalance {
  id: number;
  leave_type: string;
  entitled_days: number;
  used_days: number;
  carried_over: number;
  remaining_days: number;
}

interface LiveStatus {
  total_employees: number;
  present: number;
  late: number;
  absent: number;
  on_leave: number;
  date: string;
  recent_clockins: { id: number; name: string; department: string; time: string; status: string }[];
  dept_breakdown: { department: string; clocked_in: number; total: number }[];
}

const STATUS_COLORS: Record<string, string> = {
  present: "bg-green-100 text-green-700",
  late: "bg-amber-100 text-amber-700",
  absent: "bg-red-100 text-red-700",
  on_leave: "bg-blue-100 text-blue-700",
  half_day: "bg-orange-100 text-orange-700",
};

const LEAVE_LABELS: Record<string, string> = {
  vacation: "Vacation",
  sick: "Sick Leave",
  emergency: "Emergency",
  maternity: "Maternity",
  paternity: "Paternity",
  bereavement: "Bereavement",
  solo_parent: "Solo Parent",
  unpaid: "Unpaid",
};

// ─── API helper ───────────────────────────────────────────────────────────────

async function apiFetch<T>(url: string, options?: RequestInit): Promise<T> {
  const res = await authFetch(url, options);
  const body = await res.json();
  if (!res.ok) throw new Error(body.message ?? "Request failed");
  return (body.data ?? body) as T;
}

// ═══════════════════════════════════════════════════════════════════════════
// TAB 1 — LIVE DASHBOARD (FIX #6)
// ═══════════════════════════════════════════════════════════════════════════

function LiveDashboard() {
  const { toast } = useToast();
  const [status, setStatus] = useState<LiveStatus | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const load = useCallback(async () => {
    setLoading(true); setError(null);
    try {
      setStatus(await apiFetch<LiveStatus>("/api/attendance/live-status"));
    } catch (e) {
      const msg = e instanceof Error ? e.message : "Failed to load live status";
      setError(msg);
      toast({ title: msg, variant: "destructive" });
    } finally { setLoading(false); }
  }, []);

  useEffect(() => { load(); }, [load]);

  if (loading) return <div className="flex justify-center py-20"><Loader2 className="h-8 w-8 animate-spin text-muted-foreground" /></div>;

  // FIX #6: graceful empty state when no employees exist yet
  if (error || !status) return (
    <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-20 gap-3 text-muted-foreground">
      <AlertCircle className="h-10 w-10 text-muted-foreground/40" />
      <p>{error ?? "No data available"}</p>
      <Button variant="outline" size="sm" onClick={load}>Retry</Button>
    </div>
  );

  const rate = status.total_employees > 0
    ? Math.round(((status.present + status.late) / status.total_employees) * 100)
    : 0;

  return (
    <div className="space-y-5">
      {/* Stat cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {[
          { label: "Total Employees", value: status.total_employees, Icon: Users, color: "bg-blue-100 text-blue-600" },
          { label: "Present Today", value: status.present + status.late, Icon: UserCheck, color: "bg-green-100 text-green-600" },
          { label: "On Leave", value: status.on_leave, Icon: Calendar, color: "bg-blue-100 text-blue-600" },
          { label: "Absent", value: status.absent, Icon: UserX, color: "bg-red-100 text-red-600" },
        ].map(({ label, value, Icon, color }) => (
          <div key={label} className="rounded-xl border border-border bg-card p-4 flex items-center gap-4">
            <div className={cn("h-11 w-11 rounded-xl flex items-center justify-center shrink-0", color)}>
              <Icon className="h-5 w-5" />
            </div>
            <div>
              <p className="text-2xl font-bold">{value}</p>
              <p className="text-xs text-muted-foreground">{label}</p>
            </div>
          </div>
        ))}
      </div>

      {/* Attendance rate bar */}
      {/* <div className="rounded-xl border border-border bg-card p-4">
        <div className="h-3 rounded-full bg-muted overflow-hidden flex gap-0.5">
          {status.total_employees > 0 && (<>
            <div className="h-full bg-green-500" style={{ width: `${(status.present / status.total_employees) * 100}%` }} />
            <div className="h-full bg-amber-400" style={{ width: `${(status.late / status.total_employees) * 100}%` }} />
            <div className="h-full bg-blue-400" style={{ width: `${(status.on_leave / status.total_employees) * 100}%` }} />
          </>)}
        </div>
        <div className="flex gap-4 mt-2 text-xs text-muted-foreground">
          {[
            { label: "Present", count: status.present, color: "bg-green-500" },
            { label: "Late", count: status.late, color: "bg-amber-400" },
            { label: "On Leave", count: status.on_leave, color: "bg-blue-400" },
            { label: "Absent", count: status.absent, color: "bg-red-400" },
          ].map(({ label, count, color }) => (
            <span key={label} className="flex items-center gap-1">
              <div className={cn("h-2 w-2 rounded-full", color)} />{label}: {count}
            </span>
          ))}
        </div>
      </div> */}

      <div className="grid lg:grid-cols-2 gap-5">
        {/* Recent clock-ins */}
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <div className="px-4 py-3 border-b border-border bg-muted/30 flex items-center justify-between">
            <span className="font-medium text-sm">Recent Clock-ins</span>
            <Button variant="ghost" size="sm" className="h-7 w-7 p-0" onClick={load}>
              <RefreshCw className="h-3.5 w-3.5" />
            </Button>
          </div>
          <div className="divide-y divide-border">
            {status.recent_clockins.length === 0 ? (
              <p className="text-center py-8 text-sm text-muted-foreground">No clock-ins yet today</p>
            ) : status.recent_clockins.map(ci => (
              <div key={`${ci.id}-${ci.time}`} className="flex items-center gap-3 px-4 py-2.5">
                <div className="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700 shrink-0">
                  {ci.name.split(" ").map(n => n[0]).join("").slice(0, 2).toUpperCase()}
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium truncate">{ci.name}</p>
                  <p className="text-xs text-muted-foreground">{ci.department}</p>
                </div>
                <div className="text-right shrink-0">
                  <p className="text-xs font-mono">{ci.time}</p>
                  <Badge
                    className={cn(
                      "text-[10px] border-0",
                      STATUS_COLORS[ci.status] ?? "bg-gray-100 text-gray-600"
                    )}
                  >
                    {ci.status?.charAt(0).toUpperCase() + ci.status?.slice(1)}
                  </Badge>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Department breakdown */}
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <div className="px-4 py-3 border-b border-border bg-muted/30">
            <span className="font-medium text-sm">Department Breakdown</span>
          </div>
          <div className="p-4 space-y-3">
            {status.dept_breakdown.length === 0 ? (
              <p className="text-sm text-muted-foreground text-center py-4">No department data</p>
            ) : status.dept_breakdown.map(dept => {
              const pct = dept.total > 0 ? (dept.clocked_in / dept.total) * 100 : 0;
              return (
                <div key={dept.department}>
                  <div className="flex justify-between text-xs mb-1">
                    <span className="text-muted-foreground truncate">{dept.department}</span>
                    <span className="font-medium ml-2 shrink-0">{dept.clocked_in}/{dept.total}</span>
                  </div>
                  <div className="h-2 bg-muted rounded-full overflow-hidden">
                    <div className="h-full bg-blue-500 rounded-full transition-all" style={{ width: `${pct}%` }} />
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
// TAB 2 — ATTENDANCE HISTORY
// ═══════════════════════════════════════════════════════════════════════════

function AttendanceHistory({ canManage }: { canManage: boolean }) {
  const { toast } = useToast();
  const [records, setRecords] = useState<AttendanceRecord[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [statusF, setStatusF] = useState("all");
  const [startDate, setStart] = useState(() => { const d = new Date(); d.setDate(1); return d.toISOString().slice(0, 10); });
  const [endDate, setEnd] = useState(() => new Date().toISOString().slice(0, 10));
  const [editOpen, setEditOpen] = useState(false);
  const [editRow, setEditRow] = useState<Partial<AttendanceRecord>>({});
  const [saving, setSaving] = useState(false);

  // ── Pagination state ──────────────────────────────────────────────────────
  const [page, setPage] = useState(1);
  const PAGE_SIZE = 10;

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams({ start_date: startDate, end_date: endDate, per_page: "200" });
      if (statusF !== "all") params.set("status", statusF);
      const data = await apiFetch<{ data: AttendanceRecord[] } | AttendanceRecord[]>(`/api/attendance?${params}`);
      setRecords(Array.isArray(data) ? data : ((data as { data?: AttendanceRecord[] }).data ?? []));
      setPage(1); // reset to first page on new fetch
    } catch (e) { toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" }); }
    finally { setLoading(false); }
  }, [startDate, endDate, statusF]);

  useEffect(() => { load(); }, [load]);

  // Reset page when search changes
  useEffect(() => { setPage(1); }, [search]);

  const handleExport = async () => {
    try {
      const params = new URLSearchParams({ start_date: startDate, end_date: endDate });
      const res = await authFetch(`/api/attendance/export?${params}`);
      if (!res.ok) throw new Error("Export failed");
      const blob = await res.blob();
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `attendance_${startDate}_${endDate}.csv`;
      a.click();
      URL.revokeObjectURL(url);
    } catch (e) {
      toast({ title: e instanceof Error ? e.message : "Export failed", variant: "destructive" });
    }
  };

  const handleSave = async () => {
    if (!editRow.employee_id || !editRow.date) {
      toast({ title: "Employee ID and date are required", variant: "destructive" }); return;
    }
    setSaving(true);
    try {
      await apiFetch("/api/attendance/manual", { method: "POST", body: JSON.stringify(editRow) });
      toast({ title: "Record saved successfully", variant: "success" });
      setEditOpen(false); setEditRow({}); load();
    } catch (e) { toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" }); }
    finally { setSaving(false); }
  };

  // ── Filtering + pagination derivations ───────────────────────────────────
  const filtered = records.filter(r => {
    if (!search) return true;
    const name = r.employee ? `${r.employee.first_name} ${r.employee.last_name}` : "";
    return name.toLowerCase().includes(search.toLowerCase())
      || (r.employee?.department ?? "").toLowerCase().includes(search.toLowerCase());
  });

  const totalPages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
  const safePage = Math.min(page, totalPages);
  const paginated = filtered.slice((safePage - 1) * PAGE_SIZE, safePage * PAGE_SIZE);

  // Build page number array with ellipsis: [1, …, 4, 5, 6, …, 12]
  const pageNumbers = (() => {
    if (totalPages <= 7) return Array.from({ length: totalPages }, (_, i) => i + 1);
    const pages: (number | "…")[] = [];
    const addRange = (from: number, to: number) => {
      for (let i = from; i <= to; i++) pages.push(i);
    };
    pages.push(1);
    if (safePage > 4) pages.push("…");
    const start = Math.max(2, safePage - 1);
    const end = Math.min(totalPages - 1, safePage + 1);
    addRange(start, end);
    if (safePage < totalPages - 3) pages.push("…");
    if (totalPages > 1) pages.push(totalPages);
    return pages;
  })();

  return (
    <div className="space-y-4">
      {/* ── Filters ─────────────────────────────────────────────────────── */}
      <div className="flex flex-wrap gap-3 items-end">
        <div className="relative flex-1 min-w-[180px]">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            placeholder="Search employee / department..."
            value={search}
            onChange={e => setSearch(e.target.value)}
            className="pl-9"
          />
        </div>
        <Input type="date" value={startDate} onChange={e => setStart(e.target.value)} className="w-40" />
        <Input type="date" value={endDate} onChange={e => setEnd(e.target.value)} className="w-40" />
        <Select value={statusF} onValueChange={setStatusF}>
          <SelectTrigger className="w-36"><SelectValue placeholder="All statuses" /></SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All statuses</SelectItem>
            <SelectItem value="present">Present</SelectItem>
            <SelectItem value="late">Late</SelectItem>
            <SelectItem value="absent">Absent</SelectItem>
            <SelectItem value="on_leave">On Leave</SelectItem>
          </SelectContent>
        </Select>
        <Button variant="outline" size="sm" onClick={handleExport} className="gap-1">
          <Download className="h-4 w-4" /> Export CSV
        </Button>
        {canManage && (
          <Button
            size="sm"
            className="gap-1 bg-[#2B3588] hover:bg-[#232c70]"
            onClick={() => { setEditRow({}); setEditOpen(true); }}
          >
            <Pencil className="h-4 w-4" /> Manual Entry
          </Button>
        )}
      </div>

      {/* ── Table ───────────────────────────────────────────────────────── */}
      {loading ? (
        <div className="flex justify-center py-16">
          <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
        </div>
      ) : (
        <>
          <div className="rounded-xl border border-border bg-card overflow-hidden">
            <table className="w-full text-sm">
              <thead className="bg-muted/30 border-b border-border">
                <tr>
                  <th className="px-4 py-3 text-left font-semibold">Employee</th>
                  <th className="px-4 py-3 text-left font-semibold">Date</th>
                  <th className="px-4 py-3 text-left font-semibold">Shift</th>
                  <th className="px-4 py-3 text-left font-semibold">Time In</th>
                  <th className="px-4 py-3 text-left font-semibold">Time Out</th>
                  <th className="px-4 py-3 text-right font-semibold">Hours</th>
                  <th className="px-4 py-3 text-center font-semibold">Late</th>
                  <th className="px-4 py-3 text-center font-semibold">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                {paginated.length === 0 ? (
                  <tr>
                    <td colSpan={canManage ? 9 : 8} className="text-center py-12 text-muted-foreground">
                      No records found
                    </td>
                  </tr>
                ) : paginated.map(r => (
                  <tr key={`${r.employee_id}-${r.date}`} className="hover:bg-muted/20">
                    <td className="px-4 py-2.5">
                      <p className="font-medium">
                        {r.employee ? `${r.employee.first_name} ${r.employee.last_name}` : `#${r.employee_id}`}
                      </p>
                      <p className="text-xs text-muted-foreground">{r.employee?.department}</p>
                    </td>
                    <td className="px-4 py-2.5 text-xs text-muted-foreground">{r.date}</td>
                    <td className="px-4 py-2.5 text-xs capitalize text-muted-foreground">{r.employee?.shift_sched ?? "—"}</td>
                    <td className="px-4 py-2.5 font-mono text-xs">{r.time_in ?? "—"}</td>
                    <td className="px-4 py-2.5 font-mono text-xs">{r.time_out ?? "—"}</td>
                    <td className="px-4 py-2.5 text-right text-xs">
                      {r.hours_worked > 0 ? `${r.hours_worked}h` : "—"}
                    </td>
                    <td className="px-4 py-2.5 text-center text-xs">
                      {r.minutes_late > 0
                        ? <span className="text-amber-600 font-medium">{r.minutes_late}m</span>
                        : "—"}
                    </td>
                    <td className="px-4 py-2.5 text-center">
                      <Badge className={cn("text-xs border-0 capitalize", STATUS_COLORS[r.status])}>
                        {r.status.replace("_", " ")}
                      </Badge>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* ── Pagination footer ────────────────────────────────────────── */}
          <div className="flex items-center justify-between gap-4 flex-wrap">
            <p className="text-xs text-muted-foreground">
              Showing{" "}
              <span className="font-medium text-foreground">
                {filtered.length === 0 ? 0 : (safePage - 1) * PAGE_SIZE + 1}–{Math.min(safePage * PAGE_SIZE, filtered.length)}
              </span>{" "}
              of <span className="font-medium text-foreground">{filtered.length}</span> records
            </p>

            {totalPages > 1 && (
              <div className="flex items-center gap-1">
                {/* Prev */}
                <button
                  onClick={() => setPage(p => Math.max(1, p - 1))}
                  disabled={safePage === 1}
                  className={cn(
                    "h-8 w-8 rounded-lg flex items-center justify-center text-sm transition-colors",
                    safePage === 1
                      ? "text-muted-foreground/40 cursor-not-allowed"
                      : "hover:bg-muted text-muted-foreground hover:text-foreground"
                  )}
                  aria-label="Previous page"
                >
                  <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
                  </svg>
                </button>

                {/* Page numbers */}
                {pageNumbers.map((p, i) =>
                  p === "…" ? (
                    <span key={`ellipsis-${i}`} className="h-8 w-8 flex items-center justify-center text-xs text-muted-foreground select-none">
                      …
                    </span>
                  ) : (
                    <button
                      key={p}
                      onClick={() => setPage(p as number)}
                      className={cn(
                        "h-8 w-8 rounded-lg flex items-center justify-center text-xs font-medium transition-colors",
                        safePage === p
                          ? "bg-[#2B3588] text-white shadow-sm"
                          : "hover:bg-muted text-muted-foreground hover:text-foreground"
                      )}
                    >
                      {p}
                    </button>
                  )
                )}

                {/* Next */}
                <button
                  onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                  disabled={safePage === totalPages}
                  className={cn(
                    "h-8 w-8 rounded-lg flex items-center justify-center text-sm transition-colors",
                    safePage === totalPages
                      ? "text-muted-foreground/40 cursor-not-allowed"
                      : "hover:bg-muted text-muted-foreground hover:text-foreground"
                  )}
                  aria-label="Next page"
                >
                  <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            )}
          </div>
        </>
      )}

      {/* ── Manual entry dialog ──────────────────────────────────────────── */}
      <Dialog open={editOpen} onOpenChange={setEditOpen}>
        <DialogContent className="max-w-sm">
          <DialogHeader><DialogTitle className="text-2xl font-semibold">Manual Attendance Entry</DialogTitle></DialogHeader>
          <div className="space-y-3">
            <div>
              <label className="text-sm font-medium">Employee ID *</label>
              <Input
                type="number" className="mt-1" value={editRow.employee_id ?? ""}
                onChange={e => setEditRow(p => ({ ...p, employee_id: Number(e.target.value) }))}
                placeholder="Employee ID"
              />
            </div>
            <div>
              <label className="text-sm font-medium">Date *</label>
              <Input
                type="date" className="mt-1" value={editRow.date ?? ""}
                onChange={e => setEditRow(p => ({ ...p, date: e.target.value }))}
              />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="text-sm font-medium">Time In</label>
                <Input
                  type="time" className="mt-1" value={editRow.time_in ?? ""}
                  onChange={e => setEditRow(p => ({ ...p, time_in: e.target.value }))}
                />
              </div>
              <div>
                <label className="text-sm font-medium">Time Out</label>
                <Input
                  type="time" className="mt-1" value={editRow.time_out ?? ""}
                  onChange={e => setEditRow(p => ({ ...p, time_out: e.target.value }))}
                />
              </div>
            </div>
            <div>
              <label className="text-sm font-medium">Override Status (leave blank = auto-calc)</label>
              <Select
                value={(editRow.status as string) ?? ""}
                onValueChange={v => setEditRow(p => ({ ...p, status: v as AttendanceRecord["status"] }))}
              >
                <SelectTrigger className="mt-1"><SelectValue placeholder="Auto-calculate from shift" /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="present">Present</SelectItem>
                  <SelectItem value="late">Late</SelectItem>
                  <SelectItem value="absent">Absent</SelectItem>
                  <SelectItem value="on_leave">On Leave</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div>
              <label className="text-sm font-medium">Notes</label>
              <Input
                className="mt-1" value={editRow.notes ?? ""}
                onChange={e => setEditRow(p => ({ ...p, notes: e.target.value }))}
                placeholder="Optional"
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setEditOpen(false)}>Cancel</Button>
            <Button onClick={handleSave} disabled={saving}>
              {saving && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Save
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
// TAB 3 — EXCEL IMPORT
// ═══════════════════════════════════════════════════════════════════════════

function AttendanceImport() {
  const { toast } = useToast();
  const fileRef = useRef<HTMLInputElement>(null);
  const [preview, setPreview] = useState<Record<string, string>[]>([]);
  const [importing, setImporting] = useState(false);
  const [result, setResult] = useState<{ saved: number; errors: string[] } | null>(null);

  const handleFile = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    try {
      const XLSX = await import("xlsx");
      const buf = await file.arrayBuffer();
      const wb = XLSX.read(buf, { type: "buffer" });
      const ws = wb.Sheets[wb.SheetNames[0]];
      const rows = XLSX.utils.sheet_to_json<Record<string, string>>(ws, { defval: "" });
      setPreview(rows.slice(0, 5));
      setResult(null);
    } catch (e) {
      toast({ title: "Failed to read file", variant: "destructive" });
    }
  };

  const handleImport = async () => {
    const file = fileRef.current?.files?.[0];
    if (!file) { toast({ title: "Please select a file", variant: "destructive" }); return; }

    setImporting(true);
    try {
      const XLSX = await import("xlsx");
      const buf = await file.arrayBuffer();
      const wb = XLSX.read(buf, { type: "buffer" });
      const ws = wb.Sheets[wb.SheetNames[0]];
      const rows = XLSX.utils.sheet_to_json<Record<string, string>>(ws, { defval: "" });

      const normalised = rows
        .filter(r => r.employee_id && r.date)
        .map(r => ({
          employee_id: Number(r.employee_id),
          date: String(r.date).trim(),
          time_in: String(r.time_in || "").trim() || null,
          time_out: String(r.time_out || "").trim() || null,
          // FIX #8: pass shift so backend uses employee's shift_sched for late calc
          shift: String(r.shift || "").trim() || null,
          status: String(r.status || "").trim() || null,
          notes: String(r.notes || "").trim() || null,
        }));

      const res = await authFetch("/api/attendance/import", {
        method: "POST",
        body: JSON.stringify({ rows: normalised }),
      });
      const body = await res.json();
      if (!res.ok) throw new Error(body.message ?? "Import failed");

      setResult(body.data);
      toast({ title: `${body.data.saved} records imported` });
      setPreview([]);
      if (fileRef.current) fileRef.current.value = "";
    } catch (e) {
      toast({ title: e instanceof Error ? e.message : "Import failed", variant: "destructive" });
    } finally { setImporting(false); }
  };

  return (
    <div className="space-y-5 max-w-2xl">
      <div className="rounded-xl border border-border bg-card p-5 space-y-4">
        <h3 className="font-semibold">Import Attendance from Excel / CSV</h3>
        <p className="text-sm text-muted-foreground">
          Required columns: <code className="text-xs bg-muted px-1 rounded">employee_id</code>,{" "}
          <code className="text-xs bg-muted px-1 rounded">date (YYYY-MM-DD)</code>. Optional:{" "}
          <code className="text-xs bg-muted px-1 rounded">time_in (HH:MM)</code>,{" "}
          <code className="text-xs bg-muted px-1 rounded">time_out</code>,{" "}
          <code className="text-xs bg-muted px-1 rounded">shift (morning/afternoon/night)</code>,{" "}
          <code className="text-xs bg-muted px-1 rounded">status</code>,{" "}
          <code className="text-xs bg-muted px-1 rounded">notes</code>.<br />
          If shift is omitted, the employee's assigned shift schedule is used for late calculation.
        </p>

        <div
          className="border-2 border-dashed border-border rounded-lg p-8 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all"
          onClick={() => fileRef.current?.click()}
        >
          <Upload className="h-8 w-8 text-muted-foreground mx-auto mb-2" />
          <p className="text-sm font-medium">Click to select .xlsx / .xls / .csv file</p>
          <input ref={fileRef} type="file" accept=".xlsx,.xls,.csv" className="hidden" onChange={handleFile} />
        </div>

        {preview.length > 0 && (
          <div>
            <p className="text-xs font-medium text-muted-foreground mb-2">Preview (first 5 rows):</p>
            <div className="overflow-x-auto rounded-lg border border-border">
              <table className="w-full text-xs">
                <thead className="bg-muted/30">
                  <tr>{Object.keys(preview[0]).map(k => <th key={k} className="px-3 py-2 text-left font-medium">{k}</th>)}</tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {preview.map((row, i) => (
                    <tr key={i}>{Object.values(row).map((v, j) => <td key={j} className="px-3 py-1.5">{String(v)}</td>)}</tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}

        <Button
          onClick={handleImport}
          disabled={importing || !fileRef.current?.files?.length}
          className="w-full gap-2"
        >
          {importing ? <Loader2 className="h-4 w-4 animate-spin" /> : <Upload className="h-4 w-4" />}
          {importing ? "Importing…" : "Import Records"}
        </Button>
      </div>

      {result && (
        <div className={cn("rounded-xl border p-4 space-y-2",
          result.errors.length > 0 ? "border-amber-200 bg-amber-50" : "border-green-200 bg-green-50")}>
          <p className="font-medium text-sm flex items-center gap-2">
            {result.errors.length === 0
              ? <CheckCircle className="h-4 w-4 text-green-600" />
              : <AlertCircle className="h-4 w-4 text-amber-600" />}
            {result.saved} records imported successfully
          </p>
          {result.errors.map((err, i) => <p key={i} className="text-xs text-red-600">{err}</p>)}
        </div>
      )}
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
// TAB 4 — LEAVE MANAGEMENT (FIX #9: HR creates, Admin approves)
// ═══════════════════════════════════════════════════════════════════════════

function LeaveManagement({ canManage, canApprove, currentEmployeeId }: {
  canManage: boolean;    // HR or Admin: can see all + submit on behalf
  canApprove: boolean;   // FIX #9: only Admin can approve/reject
  currentEmployeeId?: number;
}) {
  const { toast } = useToast();
  const [requests, setRequests] = useState<LeaveRequest[]>([]);
  const [balances, setBalances] = useState<LeaveBalance[]>([]);
  const [loading, setLoading] = useState(true);
  const [statusF, setStatusF] = useState("all");
  const [formOpen, setFormOpen] = useState(false);
  const [rejectId, setRejectId] = useState<number | null>(null);
  const [rejectReason, setRejectReason] = useState("");
  const [acting, setActing] = useState<number | null>(null);
  const [form, setForm] = useState({
    leave_type: "vacation", start_date: "", end_date: "", reason: "", employee_id: "",
  });
  const [empSearch, setEmpSearch] = useState("");
  const [employees, setEmployees] = useState<{ id: number; first_name: string; last_name: string; department: string; employee_code: string | null; admin_code: string | null; role: string }[]>([]);
  const [empDropOpen, setEmpDropOpen] = useState(false);
  const [selectedEmp, setSelectedEmp] = useState<{ id: number; first_name: string; last_name: string; department: string; employee_code: string | null; admin_code: string | null; role: string } | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      // For HR/Admin: fetch ALL leave requests
      // For regular employees: fetch only their own requests
      const leaveRequestsUrl = canManage
        ? "/api/leave-requests?all=true"  // Add ?all=true to get all requests
        : "/api/leave-requests";

      const [reqs, bals] = await Promise.all([
        apiFetch<{ data: LeaveRequest[] } | LeaveRequest[]>(leaveRequestsUrl).then(data =>
          Array.isArray(data) ? data : ((data as { data?: LeaveRequest[] }).data ?? [])
        ),
        apiFetch<LeaveBalance[]>(
          `/api/leave-balances${currentEmployeeId && !canManage ? `?employee_id=${currentEmployeeId}` : ""}`
        ).catch(() => [] as LeaveBalance[]),
      ]);
      setRequests(Array.isArray(reqs) ? reqs : []);
      setBalances(Array.isArray(bals) ? bals : []);
    } catch (e) {
      console.error("Failed to load leave data:", e);
      toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" });
    }
    finally { setLoading(false); }
  }, [currentEmployeeId, canManage]); // Add canManage to dependencies

  useEffect(() => { load(); }, [load]);

  useEffect(() => {
    const close = () => setEmpDropOpen(false);
    document.addEventListener("click", close);
    return () => document.removeEventListener("click", close);
  }, []);

  // Fetch employees with their custom codes
  useEffect(() => {
    if (!canManage) return;
    authFetch("/api/employees?per_page=200")
      .then(r => r.json())
      .then(body => {
        const data = body.data?.data ?? body.data ?? [];
        setEmployees(Array.isArray(data) ? data : []);
      })
      .catch(() => { });
  }, [canManage]);

  // Helper function to get employee code
  const getEmployeeCode = (emp: any) => {
    if (emp.admin_code) return emp.admin_code;
    if (emp.employee_code) return emp.employee_code;
    return null;
  };

  const submit = async () => {
    if (!form.start_date || !form.end_date || !form.reason) {
      toast({ title: "Fill all required fields", variant: "destructive" }); return;
    }

    // Validate date range
    if (new Date(form.end_date) < new Date(form.start_date)) {
      toast({ title: "End date cannot be before start date", variant: "destructive" }); return;
    }

    // Check leave balance before submitting
    const leaveBalance = balances.find(b => b.leave_type === form.leave_type);
    if (leaveBalance) {
      if (leaveBalance.remaining_days <= 0 && form.leave_type !== "unpaid") {
        toast({
          title: "Insufficient leave balance",
          description: `You have no remaining ${LEAVE_LABELS[form.leave_type] ?? form.leave_type} days. Consider filing Unpaid Leave instead.`,
          variant: "destructive",
        });
        return;
      }
    } else if (!["unpaid", "emergency", "maternity", "paternity", "bereavement", "solo_parent"].includes(form.leave_type)) {
      // No balance record found — likely probationary/not yet entitled
      toast({
        title: "Not yet eligible",
        description: "You may not be eligible for this leave type yet. Probationary employees are not entitled to Service Incentive Leave. Please contact HR.",
        variant: "destructive",
      });
      return;
    }

    setActing(-1);
    try {
      const payload: Record<string, string> = { ...form };
      if (!form.employee_id) delete payload.employee_id;
      await apiFetch("/api/leave-requests", { method: "POST", body: JSON.stringify(payload) });
      toast({ title: "Leave request submitted successfully", variant: "success" });
      setFormOpen(false);
      setForm({ leave_type: "vacation", start_date: "", end_date: "", reason: "", employee_id: "" });
      load();
    } catch (e) { toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" }); }
    finally { setActing(null); }
  };

  const approve = async (id: number) => {
    setActing(id);
    try { await apiFetch(`/api/leave-requests/${id}/approve`, { method: "POST" }); toast({ title: "Leave approved", variant: "success" }); load(); }
    catch (e) { toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" }); }
    finally { setActing(null); }
  };

  const reject = async () => {
    if (!rejectId || !rejectReason.trim()) { toast({ title: "Provide a reason", variant: "destructive" }); return; }
    setActing(rejectId);
    try {
      await apiFetch(`/api/leave-requests/${rejectId}/reject`, {
        method: "POST",
        body: JSON.stringify({ reason: rejectReason }),
      });
      toast({ title: "Rejected", variant: "success" }); setRejectId(null); setRejectReason(""); load();
    } catch (e) { toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" }); }
    finally { setActing(null); }
  };

  const cancel = async (id: number) => {
    setActing(id);
    try { await apiFetch(`/api/leave-requests/${id}/cancel`, { method: "POST" }); toast({ title: "Cancelled", variant: "success" }); load(); }
    catch (e) { toast({ title: e instanceof Error ? e.message : "Failed", variant: "destructive" }); }
    finally { setActing(null); }
  };

  const filtered = requests.filter(r => statusF === "all" || r.status === statusF);
  const pending = requests.filter(r => r.status === "pending").length;

  const statusColors: Record<string, string> = {
    pending: "bg-amber-100 text-amber-700",
    approved: "bg-green-100 text-green-700",
    rejected: "bg-red-100 text-red-700",
    cancelled: "bg-gray-100 text-gray-600",
  };

  return (
    <div className="space-y-5">
      {/* Leave balance cards */}
      {balances.length > 0 && (
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3">
          {balances.filter(b => ["vacation", "sick", "emergency", "unpaid"].includes(b.leave_type)).map(b => (
            <div key={b.id} className={cn("rounded-xl border bg-card p-3", b.remaining_days <= 0 && b.leave_type !== "unpaid" ? "border-red-200 bg-red-50/40" : "border-border")}>
              <p className="text-xs text-muted-foreground">{LEAVE_LABELS[b.leave_type] ?? b.leave_type}</p>
              <p className={cn("text-xl font-bold mt-1", b.remaining_days <= 0 && b.leave_type !== "unpaid" ? "text-red-500" : "")}>
                {b.remaining_days}
              </p>
              <p className="text-xs text-muted-foreground">of {b.entitled_days + b.carried_over} days</p>
              {b.remaining_days <= 0 && b.leave_type !== "unpaid" && (
                <p className="text-[10px] text-red-500 mt-1 font-medium">No balance</p>
              )}
              <div className="mt-2 h-1.5 rounded-full bg-muted overflow-hidden">
                <div
                  className="h-full bg-blue-500 rounded-full"
                  style={{ width: `${(b.entitled_days + b.carried_over) > 0 ? (b.remaining_days / (b.entitled_days + b.carried_over)) * 100 : 0}%` }}
                />
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Header */}
      <div className="flex items-center justify-between flex-wrap gap-3">
        <div className="flex items-center gap-0.5 bg-muted/50 p-1 rounded-lg">
          {["all", "pending", "approved", "rejected"].map(s => (
            <button key={s} onClick={() => setStatusF(s)}
              className={cn("px-3 py-1 rounded-md text-xs font-medium transition-all capitalize",
                statusF === s ? "bg-background shadow-sm text-foreground" : "text-muted-foreground hover:text-foreground")}>
              {s}{s === "pending" && pending > 0 ? ` (${pending})` : ""}
            </button>
          ))}
        </div>
        {!canApprove && (
          <Button size="sm" className="gap-1 bg-[#2B3588] hover:bg-[#232c70]" onClick={() => setFormOpen(true)}>
            <Plus className="h-4 w-4" /> Request Leave
          </Button>
        )}
      </div>

      {loading ? (
        <div className="flex justify-center py-12"><Loader2 className="h-8 w-8 animate-spin text-muted-foreground" /></div>
      ) : (
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-muted/30 border-b border-border">
              <tr>
                {canManage && <th className="px-4 py-3 text-left font-semibold">Employee ID</th>}
                {canManage && <th className="px-4 py-3 text-left font-semibold">Employee Name</th>}
                <th className="px-4 py-3 text-left font-semibold">Type</th>
                <th className="px-4 py-3 text-left font-semibold">Dates</th>
                <th className="px-4 py-3 text-center font-semibold">Days</th>
                <th className="px-4 py-3 text-left font-semibold">Reason</th>
                <th className="px-4 py-3 text-left font-semibold">Status</th>
                {canApprove && <th className="px-4 py-3 text-right font-semibold">Actions</th>}
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {filtered.length === 0 ? (
                <tr><td colSpan={canManage && canApprove ? 8 : canManage ? 7 : canApprove ? 6 : 5} className="text-center py-12 text-muted-foreground">No requests found</td></tr>
              ) : filtered.map(r => (
                <tr key={r.id} className="hover:bg-muted/20">
                  {canManage && (
                    <>
                      <td className="px-4 py-2.5">
                        <Badge className="text-xs border-0 bg-blue-100 text-blue-700 font-mono">
                          {(() => {
                            const emp = employees.find(e => e.id === r.employee_id);
                            if (emp?.admin_code) return emp.admin_code;
                            if (emp?.employee_code) return emp.employee_code;
                            return `#${r.employee_id}`;
                          })()}
                        </Badge>
                      </td>
                      <td className="px-4 py-2.5">
                        <p className="font-medium">{r.employee_name ?? "—"}</p>
                        <p className="text-xs text-muted-foreground">{r.department}</p>
                      </td>
                    </>
                  )}
                  <td className="px-4 py-2.5">
                    <Badge className="text-xs border-0 bg-blue-100 text-blue-700">
                      {LEAVE_LABELS[r.leave_type] ?? r.leave_type}
                    </Badge>
                  </td>
                  <td className="px-4 py-2.5 text-xs text-muted-foreground">{r.start_date} → {r.end_date}</td>
                  <td className="px-4 py-2.5 text-center font-semibold">
                    {r.days_requested > 0 ? r.days_requested : (() => {
                      // Calculate days if days_requested is 0
                      const start = new Date(r.start_date);
                      const end = new Date(r.end_date);
                      let days = 0;
                      const current = new Date(start);
                      while (current <= end) {
                        const dayOfWeek = current.getDay();
                        if (dayOfWeek >= 1 && dayOfWeek <= 5) days++;
                        current.setDate(current.getDate() + 1);
                      }
                      return days;
                    })()}
                  </td>
                  <td className="px-4 py-2.5 text-xs text-muted-foreground max-w-[150px] truncate">{r.reason}</td>
                  <td className="px-4 py-2.5">
                    <Badge className={cn("text-xs border-0 capitalize", statusColors[r.status])}>{r.status}</Badge>
                    {r.rejected_reason && <p className="text-[10px] text-red-500 mt-0.5 ml-2">{r.rejected_reason}</p>}
                  </td>
                  {canApprove && (
                    <td className="px-4 py-2.5 text-right">
                      {r.status === "pending" && (
                        <div className="flex items-center justify-end gap-1">
                          <Button size="sm" className="h-8 px-3 rounded-full bg-green-600 hover:bg-green-700 text-white text-xs"
                            disabled={acting === r.id} onClick={() => approve(r.id)}>
                            {acting === r.id ? <Loader2 className="h-3 w-3 animate-spin mr-1" /> : <CheckCircle className="h-3.5 w-3.5 mr-1" />}
                            Approve
                          </Button>
                          <Button size="sm" className="h-8 px-3 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs"
                            disabled={acting === r.id} onClick={() => { setRejectId(r.id); setRejectReason(""); }}>
                            <XCircle className="h-3.5 w-3.5 mr-1" /> Reject
                          </Button>
                        </div>
                      )}
                    </td>
                  )}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {balances.length === 0 && !canManage && (
        <div className="rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
          <p className="text-xs text-amber-700 font-medium">⚠ No leave balance found</p>
          <p className="text-xs text-amber-600 mt-0.5">
            You may be on probationary status and not yet entitled to paid leave.
            Only Emergency, Maternity, Paternity, Bereavement, and Solo Parent leaves may be available.
            Contact HR for clarification.
          </p>
        </div>
      )}

      {/* Submit dialog */}
      <Dialog open={formOpen} onOpenChange={setFormOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader><DialogTitle className="text-2xl font-semibold">Request Leave</DialogTitle></DialogHeader>
          <div className="space-y-3">
            {/* HR sees employee_id field to submit on behalf */}
            {canManage && (
              <div className="relative">
                <label className="text-sm font-medium">Employee</label>
                <div className="relative mt-1">
                  <Input
                    placeholder="Search by id, name, or department..."
                    value={selectedEmp
                      ? `${getEmployeeCode(selectedEmp) || `#${selectedEmp.id}`} — ${selectedEmp.first_name} ${selectedEmp.last_name} — ${selectedEmp.department}`
                      : empSearch}
                    onChange={e => {
                      setEmpSearch(e.target.value);
                      setSelectedEmp(null);
                      setForm(p => ({ ...p, employee_id: "" }));
                      setEmpDropOpen(true);
                    }}
                    onFocus={() => setEmpDropOpen(true)}
                    className="pr-8"
                  />
                  {selectedEmp && (
                    <button
                      type="button"
                      className="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                      onClick={() => { setSelectedEmp(null); setEmpSearch(""); setForm(p => ({ ...p, employee_id: "" })); }}
                    >
                      ×
                    </button>
                  )}
                </div>
                {empDropOpen && !selectedEmp && (
                  <div className="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-lg border border-border bg-white shadow-lg">
                    {employees
                      .filter(e => {
                        const q = empSearch.toLowerCase();
                        const employeeCode = getEmployeeCode(e)?.toLowerCase() || "";
                        const fullName = `${e.first_name} ${e.last_name}`.toLowerCase();
                        const department = e.department?.toLowerCase() || "";
                        const id = String(e.id);
                        return !q || fullName.includes(q) || department.includes(q) || id.includes(q) || employeeCode.includes(q);
                      })
                      .slice(0, 20)
                      .map(e => (
                        <button
                          key={e.id}
                          type="button"
                          className="w-full text-left px-3 py-2 text-sm hover:bg-muted/50 flex items-center justify-between gap-2"
                          onMouseDown={ev => ev.preventDefault()}
                          onClick={() => {
                            setSelectedEmp(e);
                            setEmpSearch("");
                            setForm(p => ({ ...p, employee_id: String(e.id) }));
                            setEmpDropOpen(false);
                          }}
                        >
                          <div className="flex flex-col">
                            <span className="font-medium">
                              {e.first_name} {e.last_name}
                            </span>
                            <span className="text-xs text-muted-foreground">
                              {e.department}
                            </span>
                          </div>
                          <Badge className="text-xs font-mono bg-blue-100 text-blue-700">
                            {getEmployeeCode(e) || `#${e.id}`}
                          </Badge>
                        </button>
                      ))}
                    {employees.filter(e => {
                      const q = empSearch.toLowerCase();
                      const employeeCode = getEmployeeCode(e)?.toLowerCase() || "";
                      const fullName = `${e.first_name} ${e.last_name}`.toLowerCase();
                      const department = e.department?.toLowerCase() || "";
                      const id = String(e.id);
                      return !q || fullName.includes(q) || department.includes(q) || id.includes(q) || employeeCode.includes(q);
                    }).length === 0 && (
                        <p className="px-3 py-4 text-sm text-muted-foreground text-center">No employees found</p>
                      )}
                  </div>
                )}
              </div>
            )}
            <div>
              <label className="text-sm font-medium">Leave Type <span className="text-red-500">*</span></label>
              <Select value={form.leave_type} onValueChange={v => setForm(p => ({ ...p, leave_type: v }))}>
                <SelectTrigger className="mt-1"><SelectValue /></SelectTrigger>
                <SelectContent>
                  {Object.entries(LEAVE_LABELS).map(([k, v]) => (
                    <SelectItem key={k} value={k}>
                      <div className="flex flex-col">
                        <span>{v}</span>
                      </div>
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {/* Requirement notes per leave type */}
              {form.leave_type === "sick" && (
                <div className="mt-2 rounded-lg bg-blue-50 border border-blue-200 px-3 py-2">
                  <p className="text-xs text-blue-700 font-medium">Sick Leave Requirements</p>
                  <p className="text-xs text-blue-600 mt-0.5">
                    • Medical certificate required for absences of 3 or more consecutive days.<br />
                    • Must be filed within 5 days of return from absence.<br />
                    • Available to regular employees only.
                  </p>
                </div>
              )}
              {form.leave_type === "emergency" && (
                <div className="mt-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                  <p className="text-xs text-amber-700 font-medium">Emergency Leave Requirements</p>
                  <p className="text-xs text-amber-600 mt-0.5">
                    • Must provide supporting documents (e.g. incident report, hospital records).<br />
                    • Notify your supervisor or HR as soon as possible.<br />
                    • Limited to 3 days per year.
                  </p>
                </div>
              )}
              {form.leave_type === "maternity" && (
                <div className="mt-2 rounded-lg bg-pink-50 border border-pink-200 px-3 py-2">
                  <p className="text-xs text-pink-700 font-medium">Maternity Leave Requirements</p>
                  <p className="text-xs text-pink-600 mt-0.5">
                    • Must submit medical certificate confirming pregnancy.<br />
                    • File at least 30 days before expected leave date.<br />
                    • Entitled to 105 days per RA 11210.
                  </p>
                </div>
              )}
              {form.leave_type === "paternity" && (
                <div className="mt-2 rounded-lg bg-sky-50 border border-sky-200 px-3 py-2">
                  <p className="text-xs text-sky-700 font-medium">Paternity Leave Requirements</p>
                  <p className="text-xs text-sky-600 mt-0.5">
                    • Must be a married male employee.<br />
                    • Submit marriage certificate and birth/delivery documents.<br />
                    • Entitled to 7 days per RA 8187.
                  </p>
                </div>
              )}
              {form.leave_type === "bereavement" && (
                <div className="mt-2 rounded-lg bg-gray-50 border border-gray-200 px-3 py-2">
                  <p className="text-xs text-gray-700 font-medium">Bereavement Leave Requirements</p>
                  <p className="text-xs text-gray-600 mt-0.5">
                    • Must submit death certificate of the deceased.<br />
                    • Applicable for immediate family members only.<br />
                    • Limited to 3 days.
                  </p>
                </div>
              )}
              {form.leave_type === "solo_parent" && (
                <div className="mt-2 rounded-lg bg-purple-50 border border-purple-200 px-3 py-2">
                  <p className="text-xs text-purple-700 font-medium">Solo Parent Leave Requirements</p>
                  <p className="text-xs text-purple-600 mt-0.5">
                    • Must present valid Solo Parent ID issued by DSWD.<br />
                    • Entitled to 7 working days per year per RA 8972.
                  </p>
                </div>
              )}
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="text-sm font-medium">Start Date <span className="text-red-500">*</span></label>
                <Input type="date" className="mt-1" value={form.start_date} onChange={e => setForm(p => ({ ...p, start_date: e.target.value }))} />
              </div>
              <div>
                <label className="text-sm font-medium">End Date <span className="text-red-500">*</span></label>
                <Input type="date" className="mt-1" value={form.end_date} onChange={e => setForm(p => ({ ...p, end_date: e.target.value }))} />
              </div>
            </div>
            <div>
              <label className="text-sm font-medium">Reason <span className="text-red-500">*</span></label>
              <textarea
                className="mt-1 w-full rounded-md border border-input px-3 py-2 text-sm min-h-[80px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                value={form.reason} onChange={e => setForm(p => ({ ...p, reason: e.target.value }))}
                placeholder="Reason for leave..." />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" className="bg-gray-200" onClick={() => setFormOpen(false)}>Cancel</Button>
            <Button className="bg-[#2B3588]" onClick={submit} disabled={acting === -1}>
              {acting === -1 && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Submit
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Reject dialog */}
      <Dialog open={rejectId !== null} onOpenChange={() => setRejectId(null)}>
        <DialogContent className="max-w-sm">
          <DialogHeader><DialogTitle>Reject Leave Request</DialogTitle></DialogHeader>
          <div>
            <label className="text-sm font-medium">Rejection reason <span className="text-red-500">*</span></label>
            <Input className="mt-1" value={rejectReason} onChange={e => setRejectReason(e.target.value)}
              placeholder="e.g. Peak season, insufficient balance…" />
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setRejectId(null)}>Cancel</Button>
            <Button variant="destructive" onClick={reject} disabled={acting === rejectId}>
              {acting === rejectId && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Reject
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════════
// MAIN PAGE
// ═══════════════════════════════════════════════════════════════════════════

export default function Attendance() {
  const { user } = useAuth();
  const role = user?.role ?? "";
  const isHR = role === "HR";
  const isAdmin = role === "Admin";

  const canManage = isHR;        // HR only: History + Import + manual entry
  const canApprove = isAdmin;     // Admin only: approve/reject leave

  const [currentEmployeeId, setCurrentEmployeeId] = useState<number | undefined>();
  useEffect(() => {
    authFetch("/api/auth/me").then(r => r.json()).then(b => {
      const emp = b.data?.employee ?? b.employee;
      if (emp?.id) setCurrentEmployeeId(emp.id);
    }).catch(() => { });
  }, []);

  return (
    <DashboardLayout>
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-bold text-foreground">Attendance & Timekeeping</h1>
        {isHR && (
          <>
            <Button
              className="gap-2 bg-[#2B3588] hover:bg-[#232c70] text-white"
              onClick={() => document.getElementById('import-file-input')?.click()}
            >
              <Upload className="h-4 w-4" /> Import Attendance
            </Button>
            <input
              id="import-file-input"
              type="file"
              accept=".xlsx,.xls,.csv"
              className="hidden"
              onChange={async (e) => {
                const file = e.target.files?.[0];
                if (!file) return;
                try {
                  const XLSX = await import("xlsx");
                  const buf = await file.arrayBuffer();
                  const wb = XLSX.read(buf, { type: "buffer" });
                  const ws = wb.Sheets[wb.SheetNames[0]];
                  const rows = XLSX.utils.sheet_to_json<Record<string, string>>(ws, { defval: "" });
                  const normalised = rows
                    .filter(r => r.employee_id && r.date)
                    .map(r => ({
                      employee_id: Number(r.employee_id),
                      date: String(r.date).trim(),
                      time_in: String(r.time_in || "").trim() || null,
                      time_out: String(r.time_out || "").trim() || null,
                      shift: String(r.shift || "").trim() || null,
                      status: String(r.status || "").trim() || null,
                      notes: String(r.notes || "").trim() || null,
                    }));
                  const res = await authFetch("/api/attendance/import", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ rows: normalised }),
                  });
                  const body = await res.json();
                  if (!res.ok) throw new Error(body.message ?? "Import failed");
                  alert(`${body.data.saved} records imported successfully`);
                  e.target.value = "";
                } catch (err) {
                  alert(err instanceof Error ? err.message : "Import failed");
                }
              }}
            />
          </>
        )}
      </div>
      <Tabs defaultValue="dashboard">
        <TabsList className={`grid w-full ${isHR ? "grid-cols-3" : "grid-cols-3"}`}>
          <TabsTrigger value="dashboard">Live Dashboard</TabsTrigger>
          {(isHR || isAdmin) && <TabsTrigger value="history">History</TabsTrigger>}
          <TabsTrigger value="leave">Leave Requests</TabsTrigger>
        </TabsList>

        <TabsContent value="dashboard" className="mt-6"><LiveDashboard /></TabsContent>
        {(isHR || isAdmin) && <TabsContent value="history" className="mt-6"><AttendanceHistory canManage={isHR} /></TabsContent>}
        <TabsContent value="leave" className="mt-6">
          <LeaveManagement
            canManage={isAdmin || isHR}
            canApprove={canApprove}
            currentEmployeeId={currentEmployeeId}
          />
        </TabsContent>
      </Tabs>
    </DashboardLayout>
  );
}