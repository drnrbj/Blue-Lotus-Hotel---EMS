// src/pages/Recruitment.tsx
// COMPLETE REVISED VERSION - WITH HR USERS FIX

import { useState, useEffect } from "react";
import { DashboardLayout } from "@/components/layout/DashboardLayout";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { useToast } from "@/hooks/use-toast";
import { useAuth } from "@/hooks/useAuth";
import { authFetch } from "@/hooks/api";
import {
  Search, Plus, Edit, Trash2, Calendar, UserCheck, UserX,
  CheckCircle, XCircle, Loader2, Briefcase, Users, GraduationCap,
} from "lucide-react";
import { cn } from "@/lib/utils";

// ─── Types ────────────────────────────────────────────────────────────────────

interface JobPosting {
  id: number;
  title: string;
  department: string;
  job_category: string;
  description: string;
  slots: number;
  posted_date?: string;
  deadline?: string;
  status: "open" | "closed";
  created_by: number;
  applicants_count?: number;
  hired_count?: number;
  created_at: string;
}

interface Applicant {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  resume_path: string | null;
  job_posting_id: number;
  pipeline_stage: "applied" | "reviewed" | "interview_scheduled" | "interviewed" | "hired" | "rejected";
  notes: string | null;
  hired_at: string | null;
  job_posting?: JobPosting;
  created_at: string;
}

interface Interview {
  id: number;
  applicant_id: number;
  interviewer_id: number;
  scheduled_at: string;
  status: "scheduled" | "completed" | "cancelled";
  feedback: string | null;
  applicant?: Applicant;
  interviewer?: { id: number; name: string; email: string };
  created_at: string;
}

interface TrainingAssignment {
  id: number;
  training_id: number;
  applicant_id: number;
  trainer_id: number | null;
  status: "pending" | "in_progress" | "completed";
  completed_at: string | null;
  training?: { id: number; title: string; description: string };
  applicant?: Applicant;
  employee?: { id: number; first_name: string; last_name: string; department: string };
  trainer?: { id: number; first_name: string; last_name: string; department: string };
}

interface HRUser {
  id: number;
  name: string;
  email: string;
}

// ─── Constants ────────────────────────────────────────────────────────────────

const DEPARTMENTS = [
  "Front Office", "Housekeeping", "Food & Beverage",
  "Maintenance", "Administration", "Security", "Sales & Marketing",
];

const JOB_CATEGORIES: Record<string, string[]> = {
  "Front Office": ["Front Desk Agent", "Concierge", "Reservations Agent", "Guest Relations Officer", "Bell Staff"],
  "Housekeeping": ["Room Attendant", "Laundry Attendant", "Housekeeping Supervisor", "Public Area Cleaner"],
  "Food & Beverage": ["Waiter/Waitress", "Bartender", "Chef de Partie", "Sous Chef", "Executive Chef", "Kitchen Steward"],
  "Maintenance": ["Maintenance Technician", "Electrician", "Plumber", "Maintenance Supervisor"],
  "Administration": ["HR Officer", "Accounting Staff", "Payroll Officer", "General Manager", "Department Manager", "Supervisor"],
  "Security": ["Security Guard", "Security Supervisor"],
  "Sales & Marketing": ["Sales Manager", "Marketing Officer", "Reservations Manager"],
};

const STAGE_STYLES: Record<string, string> = {
  applied: "bg-gray-100 text-gray-700",
  reviewed: "bg-blue-100 text-blue-700",
  interview_scheduled: "bg-purple-100 text-purple-700",
  interviewed: "bg-cyan-100 text-cyan-700",
  hired: "bg-green-100 text-green-700",
  rejected: "bg-red-100 text-red-700",
};

const STAGE_LABELS: Record<string, string> = {
  applied: "Applied",
  reviewed: "Reviewed",
  interview_scheduled: "Interview Scheduled",
  interviewed: "Interviewed",
  hired: "Hired",
  rejected: "Rejected",
};

// ─── Helper Functions ─────────────────────────────────────────────────────────

const safeFetch = async <T,>(url: string): Promise<T[]> => {
  try {
    const res = await authFetch(url);
    const body = await res.json();
    const data = body.data;
    return Array.isArray(data) ? data : (data?.data ?? []);
  } catch {
    return [];
  }
};

function Pagination({ currentPage, totalPages, totalItems, itemsPerPage, onPageChange }: {
  currentPage: number;
  totalPages: number;
  totalItems: number;
  itemsPerPage: number;
  onPageChange: (page: number) => void;
}) {
  if (totalPages <= 1) return null;
  return (
    <div className="flex items-center justify-between px-1">
      <p className="text-xs text-muted-foreground">
        Showing {((currentPage - 1) * itemsPerPage) + 1}–{Math.min(currentPage * itemsPerPage, totalItems)} of {totalItems}
      </p>
      <div className="flex items-center gap-1">
        <Button variant="outline" size="sm" className="h-8 px-3 text-xs"
          disabled={currentPage === 1} onClick={() => onPageChange(currentPage - 1)}>
          Previous
        </Button>
        {Array.from({ length: totalPages }, (_, i) => i + 1)
          .filter(p => p === 1 || p === totalPages || Math.abs(p - currentPage) <= 1)
          .reduce<(number | string)[]>((acc, p, idx, arr) => {
            if (idx > 0 && (p as number) - (arr[idx - 1] as number) > 1) acc.push("...");
            acc.push(p);
            return acc;
          }, [])
          .map((p, idx) =>
            p === "..." ? (
              <span key={`e-${idx}`} className="px-1 text-xs text-muted-foreground">…</span>
            ) : (
              <Button key={p} size="sm"
                className={`h-8 w-8 p-0 text-xs ${currentPage === p ? "bg-[#2B3588] hover:bg-[#232c70] text-white" : ""}`}
                variant={currentPage === p ? "default" : "outline"}
                onClick={() => onPageChange(p as number)}>
                {p}
              </Button>
            )
          )}
        <Button variant="outline" size="sm" className="h-8 px-3 text-xs"
          disabled={currentPage === totalPages} onClick={() => onPageChange(currentPage + 1)}>
          Next
        </Button>
      </div>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════
// TAB 1 — JOB VACANCIES
// ═══════════════════════════════════════════════════════════════════════

function JobVacanciesTab({ canManage }: { canManage: boolean }) {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;
  const { toast } = useToast();
  const [jobs, setJobs] = useState<JobPosting[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [open, setOpen] = useState(false);
  const [editing, setEditing] = useState<JobPosting | null>(null);
  const [saving, setSaving] = useState(false);
  const [dept, setDept] = useState("");
  const [form, setForm] = useState({
    title: "", department: "", job_category: "",
    description: "", slots: "1", deadline: "",
  });
  const [fieldErrors, setFieldErrors] = useState<Record<string, boolean>>({});

  const load = async () => {
    setLoading(true);
    setJobs(await safeFetch("/api/recruitment/job-postings"));
    setLoading(false);
  };

  useEffect(() => { load(); }, []);

  const openCreate = () => {
    setEditing(null);
    setDept("");
    setForm({ title: "", department: "", job_category: "", description: "", slots: "1", deadline: "" });
    setFieldErrors({});
    setOpen(true);
  };

  const openEdit = (job: JobPosting) => {
    setEditing(job);
    setDept(job.department);
    setForm({
      title: job.title, department: job.department,
      job_category: job.job_category, description: job.description,
      slots: String(job.slots), deadline: job.deadline ?? "",
    });
    setFieldErrors({});
    setOpen(true);
  };

  const validate = () => {
    const errors: Record<string, boolean> = {};
    if (!form.title.trim()) errors.title = true;
    if (!form.department) errors.department = true;
    if (!form.job_category) errors.job_category = true;
    if (!form.slots || parseInt(form.slots) < 1) errors.slots = true;
    setFieldErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const save = async () => {
    if (!validate()) {
      toast({ title: "Please fill in all required fields", variant: "destructive" });
      return;
    }
    setSaving(true);
    try {
      const body = { ...form, slots: parseInt(form.slots) };
      const url = editing
        ? `/api/recruitment/job-postings/${editing.id}`
        : "/api/recruitment/job-postings";
      const method = editing ? "PUT" : "POST";

      const res = await authFetch(url, { method, body: JSON.stringify(body) });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");

      toast({ title: editing ? "Job updated successfully" : "Job posted successfully", variant: "success" });
      setOpen(false);
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setSaving(false);
    }
  };

  const clearFieldError = (field: string) => {
    if (fieldErrors[field]) {
      setFieldErrors(prev => {
        const next = { ...prev };
        delete next[field];
        return next;
      });
    }
  };

  const del = async (id: number) => {
    if (!confirm("Delete this job posting?")) return;
    try {
      const res = await authFetch(`/api/recruitment/job-postings/${id}`, { method: "DELETE" });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Job deleted successfully", variant: "success" });
      load();
    } catch {
      toast({ title: "Failed to delete", variant: "destructive" });
    }
  };

  const toggleStatus = async (job: JobPosting) => {
    try {
      const res = await authFetch(`/api/recruitment/job-postings/${job.id}`, {
        method: "PUT",
        body: JSON.stringify({ status: job.status === "open" ? "closed" : "open" }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      load();
    } catch {
      toast({ title: "Failed", variant: "destructive" });
    }
  };

  const filtered = jobs.filter(j =>
    j.title.toLowerCase().includes(search.toLowerCase()) ||
    j.department.toLowerCase().includes(search.toLowerCase())
  );

  const totalPages = Math.ceil(filtered.length / itemsPerPage);
  const paginated = filtered.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);
  useEffect(() => { setCurrentPage(1); }, [search]);

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between gap-3">
        <div className="relative flex-1 max-w-sm">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search jobs..." value={search} onChange={e => setSearch(e.target.value)} className="pl-9" />
        </div>
        {canManage && (
          <Button onClick={openCreate} className="gap-2 bg-[#2B3588] hover:bg-[#232c70]">
            <Plus className="h-4 w-4" /> New Job Posting
          </Button>
        )}
      </div>

      {loading ? (
        <div className="flex justify-center py-16"><Loader2 className="h-8 w-8 animate-spin text-muted-foreground" /></div>
      ) : filtered.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16">
          <Briefcase className="h-10 w-10 text-muted-foreground/40 mb-3" />
          <p className="text-muted-foreground font-medium">No job postings yet</p>
        </div>
      ) : (
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-muted/30 border-b border-border">
              <tr>
                <th className="px-4 py-3 text-left font-semibold">Job Title</th>
                <th className="px-4 py-3 text-left font-semibold">Department</th>
                <th className="px-4 py-3 text-left font-semibold">Category</th>
                <th className="px-4 py-3 text-center font-semibold">Slots</th>
                <th className="px-4 py-3 text-center font-semibold">Applicants</th>
                <th className="px-4 py-3 text-center font-semibold">Status</th>
                <th className="px-4 py-3 text-right font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {paginated.map(job => (
                <tr key={job.id} className="hover:bg-muted/20 transition-colors">
                  <td className="px-4 py-3 font-medium">{job.title}</td>
                  <td className="px-4 py-3 text-muted-foreground">{job.department}</td>
                  <td className="px-4 py-3 text-muted-foreground">{job.job_category}</td>
                  <td className="px-4 py-3 text-center">{job.slots}</td>
                  <td className="px-4 py-3 text-center">{job.applicants_count ?? 0}</td>
                  <td className="px-4 py-3 text-center">
                    <Badge className={cn("text-xs border-0", job.status === "open" ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700")}>
                      {job.status === "open" ? "Open" : "Closed"}
                    </Badge>
                  </td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex items-center justify-end gap-1">
                      {canManage && (
                        <>
                          <Button variant="ghost" size="sm" className="h-8 w-8 p-0" onClick={() => openEdit(job)}><Edit className="h-4 w-4" /></Button>
                          <Button variant="ghost" size="sm" className="h-8 w-8 p-0" onClick={() => toggleStatus(job)}>
                            {job.status === "open" ? <XCircle className="h-4 w-4 text-red-500" /> : <CheckCircle className="h-4 w-4 text-green-500" />}
                          </Button>
                          <Button variant="ghost" size="sm" className="h-8 w-8 p-0 text-red-500" onClick={() => del(job.id)}><Trash2 className="h-4 w-4" /></Button>
                        </>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* ADD THIS: */}
      <Pagination
        currentPage={currentPage}
        totalPages={Math.ceil(filtered.length / itemsPerPage)}
        totalItems={filtered.length}
        itemsPerPage={itemsPerPage}
        onPageChange={setCurrentPage}
      />

      <Dialog open={open} onOpenChange={setOpen}>
        <DialogContent className="max-w-lg">
          <DialogHeader>
            <DialogTitle className="text-2xl font-semibold">
              {editing ? "Edit Job Posting" : "New Job Posting"}
            </DialogTitle>
          </DialogHeader>
          <div className="space-y-3">
            {/* Title Field */}
            <div>
              <Input
                placeholder="Job Title *"
                value={form.title}
                onChange={e => {
                  setForm(p => ({ ...p, title: e.target.value }));
                  clearFieldError("title");
                }}
                className={fieldErrors.title ? "border-red-500 focus-visible:ring-red-500" : ""}
              />
              {fieldErrors.title && (
                <p className="text-xs text-red-500 mt-1 ml-1">Job title is required</p>
              )}
            </div>

            {/* Department Field */}
            <div>
              <Select
                value={form.department}
                onValueChange={v => {
                  setDept(v);
                  setForm(p => ({ ...p, department: v, job_category: "" }));
                  clearFieldError("department");
                  clearFieldError("job_category");
                }}
              >
                <SelectTrigger className={fieldErrors.department ? "border-red-500 focus-visible:ring-red-500" : ""}>
                  <SelectValue placeholder="Select Department *" />
                </SelectTrigger>
                <SelectContent>
                  {DEPARTMENTS.map(d => <SelectItem key={d} value={d}>{d}</SelectItem>)}
                </SelectContent>
              </Select>
              {fieldErrors.department && (
                <p className="text-xs text-red-500 mt-1 ml-1">Department is required</p>
              )}
            </div>

            {/* Job Category Field */}
            <div>
              <Select
                value={form.job_category}
                onValueChange={v => {
                  setForm(p => ({ ...p, job_category: v }));
                  clearFieldError("job_category");
                }}
                disabled={!dept}
              >
                <SelectTrigger className={fieldErrors.job_category ? "border-red-500 focus-visible:ring-red-500" : ""}>
                  <SelectValue placeholder={dept ? "Select Job Category *" : "Select department first"} />
                </SelectTrigger>
                <SelectContent>
                  {(JOB_CATEGORIES[dept] ?? []).map(c => <SelectItem key={c} value={c}>{c}</SelectItem>)}
                </SelectContent>
              </Select>
              {fieldErrors.job_category && (
                <p className="text-xs text-red-500 mt-1 ml-1">Job category is required</p>
              )}
            </div>

            {/* Slots & Deadline */}
            <div className="grid grid-cols-2 gap-3">
              <div>
                <Input
                  type="number"
                  placeholder="Slots *"
                  min="1"
                  value={form.slots}
                  onChange={e => {
                    setForm(p => ({ ...p, slots: e.target.value }));
                    clearFieldError("slots");
                  }}
                  className={fieldErrors.slots ? "border-red-500 focus-visible:ring-red-500" : ""}
                />
                {fieldErrors.slots && (
                  <p className="text-xs text-red-500 mt-1 ml-1">At least 1 slot required</p>
                )}
              </div>
              <Input
                type="date"
                value={form.deadline}
                onChange={e => setForm(p => ({ ...p, deadline: e.target.value }))}
              />
            </div>

            {/* Description Field (optional) */}
            <textarea
              className="w-full rounded-md border border-input px-3 py-2 text-sm min-h-[80px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
              placeholder="Job Description (optional)"
              value={form.description}
              onChange={e => setForm(p => ({ ...p, description: e.target.value }))}
            />
          </div>
          <DialogFooter>
            <Button variant="outline" className="bg-gray-200" onClick={() => setOpen(false)}>
              Cancel
            </Button>
            <Button className="bg-[#2B3588]" onClick={save} disabled={saving}>
              {saving && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              {editing ? "Save Changes" : "Post Job"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}

function ApplicantManagementTab({ canManage, isAdmin }: { canManage: boolean; isAdmin: boolean }) {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;
  const { toast } = useToast();
  const [applicants, setApplicants] = useState<Applicant[]>([]);
  const [jobs, setJobs] = useState<JobPosting[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [addOpen, setAddOpen] = useState(false);
  const [schedOpen, setSchedOpen] = useState(false);
  const [selApp, setSelApp] = useState<Applicant | null>(null);
  const [hrUsers, setHrUsers] = useState<HRUser[]>([]);
  const [acting, setActing] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [addForm, setAddForm] = useState({ first_name: "", last_name: "", email: "", phone: "", job_posting_id: "" });
  const [addFieldErrors, setAddFieldErrors] = useState<Record<string, boolean>>({});
  const [schedForm, setSchedForm] = useState({ interviewer_id: "", scheduled_at: "" });
  const [schedFieldErrors, setSchedFieldErrors] = useState<Record<string, boolean>>({});

  const load = async () => {
    setLoading(true);
    const [apps, jbs] = await Promise.all([
      safeFetch<Applicant>("/api/recruitment/applicants"),
      safeFetch<JobPosting>("/api/recruitment/job-postings"),
    ]);
    setApplicants(apps);
    setJobs(jbs);
    setLoading(false);
  };

  // Fetch HR users from employees endpoint
  const fetchInterviewers = async () => {
    try {
      const res = await authFetch("/api/recruitment/interviewers");
      const data = await res.json();
      if (data.success) {
        setHrUsers(data.data);
      } else {
        // fallback: fetch employees and filter HR/Manager
        const empRes = await authFetch("/api/employees");
        const empData = await empRes.json();
        const employees = empData.data?.data || empData.data || [];
        const eligible = employees.filter((e: any) => e.role === 'HR' || e.role === 'Manager');
        setHrUsers(eligible.map((e: any) => ({ id: e.id, name: `${e.first_name} ${e.last_name}`, email: e.email })));
      }
    } catch {
      setHrUsers([]);
    }
  };

  useEffect(() => {
    load();
    fetchInterviewers();
  }, []);

  const openAddDialog = () => {
    setAddForm({ first_name: "", last_name: "", email: "", phone: "", job_posting_id: "" });
    setAddFieldErrors({});
    setAddOpen(true);
  };

  const openSchedDialog = (app: Applicant) => {
    setSelApp(app);
    setSchedForm({ interviewer_id: "", scheduled_at: "" });
    setSchedFieldErrors({});
    setSchedOpen(true);
  };

  const clearAddFieldError = (field: string) => {
    if (addFieldErrors[field]) {
      setAddFieldErrors(prev => {
        const next = { ...prev };
        delete next[field];
        return next;
      });
    }
  };

  const clearSchedFieldError = (field: string) => {
    if (schedFieldErrors[field]) {
      setSchedFieldErrors(prev => {
        const next = { ...prev };
        delete next[field];
        return next;
      });
    }
  };

  const validateAddForm = () => {
    const errors: Record<string, boolean> = {};
    if (!addForm.first_name.trim()) errors.first_name = true;
    if (!addForm.last_name.trim()) errors.last_name = true;
    if (!addForm.email.trim()) errors.email = true;
    else {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(addForm.email.trim())) errors.email = true;
    }
    if (!addForm.phone.trim()) errors.phone = true;  // ADD THIS LINE
    if (!addForm.job_posting_id) errors.job_posting_id = true;
    setAddFieldErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const validateSchedForm = () => {
    const errors: Record<string, boolean> = {};
    if (!schedForm.interviewer_id) errors.interviewer_id = true;
    if (!schedForm.scheduled_at) errors.scheduled_at = true;
    setSchedFieldErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const addApplicant = async () => {
    if (!validateAddForm()) {
      toast({ title: "Please fill in all required fields correctly", variant: "destructive" });
      return;
    }

    setSaving(true);
    try {
      const payload = {
        first_name: addForm.first_name.trim(),
        last_name: addForm.last_name.trim(),
        email: addForm.email.trim(),
        phone: addForm.phone?.trim() || '',
        job_posting_id: Number(addForm.job_posting_id)
      };

      console.log("Sending payload:", JSON.stringify(payload, null, 2));

      const res = await authFetch("/api/recruitment/applicants", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json"
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      console.log("Response status:", res.status);
      console.log("Response data:", data);

      if (res.ok && data.success) {
        toast({ title: "Applicant added successfully", variant: "success" });
        setAddOpen(false);
        setAddForm({ first_name: "", last_name: "", email: "", phone: "", job_posting_id: "" });
        setAddFieldErrors({});
        load();
      } else {
        if (data.errors) {
          const errorMessages = Object.values(data.errors).flat().join('\n');
          toast({ title: "Validation Error", description: errorMessages, variant: "destructive" });
        } else {
          toast({ title: data.message || "Failed to add applicant", variant: "destructive" });
        }
      }
    } catch (err) {
      console.error('Add applicant error:', err);
      toast({ title: err instanceof Error ? err.message : "Failed to add applicant", variant: "destructive" });
    } finally {
      setSaving(false);
    }
  };

  const scheduleInterview = async () => {
    if (!selApp) return;

    if (!validateSchedForm()) {
      toast({ title: "Fill all fields", variant: "destructive" });
      return;
    }

    setSaving(true);
    try {
      const res = await authFetch("/api/recruitment/interviews", {
        method: "POST",
        body: JSON.stringify({
          applicant_id: selApp.id,
          interviewer_id: Number(schedForm.interviewer_id),
          scheduled_at: schedForm.scheduled_at,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Interview scheduled successfully", variant: "success" });
      setSchedOpen(false);
      setSelApp(null);
      setSchedForm({ interviewer_id: "", scheduled_at: "" });
      setSchedFieldErrors({});
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setSaving(false);
    }
  };

  const hireApplicant = async (app: Applicant) => {
    setActing(app.id);
    try {
      const res = await authFetch(`/api/recruitment/applicants/${app.id}/hire`, { method: "POST" });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Applicant hired! Training record created.", variant: "success" });
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setActing(null);
    }
  };

  const rejectApplicant = async (app: Applicant) => {
    setActing(app.id);
    try {
      const res = await authFetch(`/api/recruitment/applicants/${app.id}/reject`, { method: "POST" });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Applicant rejected successfully", variant: "success" });
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setActing(null);
    }
  };

  const updateStage = async (app: Applicant, stage: string) => {
    setActing(app.id);
    try {
      const res = await authFetch(`/api/recruitment/applicants/${app.id}/stage`, {
        method: "PATCH",
        body: JSON.stringify({ pipeline_stage: stage }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Stage updated successfully", variant: "success" });
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setActing(null);
    }
  };

  const filtered = applicants.filter(a =>
    `${a.first_name} ${a.last_name}`.toLowerCase().includes(search.toLowerCase()) ||
    a.email.toLowerCase().includes(search.toLowerCase())
  );

  const totalPages = Math.ceil(filtered.length / itemsPerPage);
  const paginated = filtered.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  useEffect(() => { setCurrentPage(1); }, [search]);

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between gap-3">
        <div className="relative flex-1 max-w-sm">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search applicants..." value={search} onChange={e => setSearch(e.target.value)} className="pl-9" />
        </div>
        {canManage && (
          <Button onClick={openAddDialog} className="gap-2 bg-[#2B3588] hover:bg-[#232c70]">
            <Plus className="h-4 w-4" /> Add Applicant
          </Button>
        )}
      </div>

      {loading ? (
        <div className="flex justify-center py-16"><Loader2 className="h-8 w-8 animate-spin text-muted-foreground" /></div>
      ) : filtered.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16">
          <Users className="h-10 w-10 text-muted-foreground/40 mb-3" />
          <p className="text-muted-foreground font-medium">No applicants yet</p>
        </div>
      ) : (
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-muted/30 border-b border-border">
              <tr>
                <th className="px-4 py-3 text-left font-semibold">Applicant</th>
                <th className="px-4 py-3 text-left font-semibold">Applied For</th>
                <th className="px-4 py-3 text-left font-semibold">Stage</th>
                <th className="px-4 py-3 text-right font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {paginated.map(app => (
                <tr key={app.id} className="hover:bg-muted/20 transition-colors">
                  <td className="px-4 py-3">
                    <p className="font-medium">{app.first_name} {app.last_name}</p>
                    <p className="text-xs text-muted-foreground">{app.email}</p>
                  </td>
                  <td className="px-4 py-3 text-muted-foreground text-xs">
                    {app.job_posting?.title ?? "—"}<br />
                    <span className="text-muted-foreground/60">{app.job_posting?.department}</span>
                  </td>
                  <td className="px-4 py-3">
                    <Badge className={cn("text-xs border-0", STAGE_STYLES[app.pipeline_stage])}>
                      {STAGE_LABELS[app.pipeline_stage]}
                    </Badge>
                  </td>
                  <td className="px-4 py-3">
                    <div className="flex items-center justify-end gap-1 flex-wrap">
                      {acting === app.id && <Loader2 className="h-4 w-4 animate-spin" />}
                      {acting !== app.id && app.pipeline_stage === "applied" && (
                        <Button size="sm" variant="outline" className="text-xs h-7" onClick={() => updateStage(app, "reviewed")}>Mark Reviewed</Button>
                      )}
                      {acting !== app.id && app.pipeline_stage === "reviewed" && (
                        <Button size="sm" variant="outline" className="text-xs h-7" onClick={() => openSchedDialog(app)}>
                          <Calendar className="h-3 w-3 mr-1" /> Schedule Interview
                        </Button>
                      )}
                      {acting !== app.id && app.pipeline_stage === "interview_scheduled" && (
                        <Button size="sm" variant="outline" className="text-xs h-7" onClick={() => updateStage(app, "interviewed")}>Mark Interviewed</Button>
                      )}
                      {acting !== app.id && app.pipeline_stage === "interviewed" && (
                        <>
                          {isAdmin && (
                            <Button size="sm" className="text-xs h-7 bg-green-600 hover:bg-green-700" onClick={() => hireApplicant(app)}>
                              <UserCheck className="h-3 w-3 mr-1" /> Hire
                            </Button>
                          )}
                          {isAdmin && (
                            <Button size="sm" variant="destructive" className="text-xs h-7" onClick={() => rejectApplicant(app)}>
                              <UserX className="h-3 w-3 mr-1" /> Reject
                            </Button>
                          )}
                        </>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      <Pagination
        currentPage={currentPage}
        totalPages={Math.ceil(filtered.length / itemsPerPage)}
        totalItems={filtered.length}
        itemsPerPage={itemsPerPage}
        onPageChange={setCurrentPage}
      />

      {/* Add Applicant Dialog */}
      <Dialog open={addOpen} onOpenChange={setAddOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader><DialogTitle className="text-2xl font-semibold">Add Applicant</DialogTitle></DialogHeader>
          <div className="space-y-3">
            <div className="grid grid-cols-2 gap-3">
              <div>
                <Input
                  placeholder="First Name *"
                  value={addForm.first_name}
                  onChange={e => {
                    setAddForm(p => ({ ...p, first_name: e.target.value }));
                    clearAddFieldError("first_name");
                  }}
                  className={addFieldErrors.first_name ? "border-red-500 focus-visible:ring-red-500" : ""}
                />
                {addFieldErrors.first_name && (
                  <p className="text-xs text-red-500 mt-1 ml-1">First name is required</p>
                )}
              </div>
              <div>
                <Input
                  placeholder="Last Name *"
                  value={addForm.last_name}
                  onChange={e => {
                    setAddForm(p => ({ ...p, last_name: e.target.value }));
                    clearAddFieldError("last_name");
                  }}
                  className={addFieldErrors.last_name ? "border-red-500 focus-visible:ring-red-500" : ""}
                />
                {addFieldErrors.last_name && (
                  <p className="text-xs text-red-500 mt-1 ml-1">Last name is required</p>
                )}
              </div>
            </div>
            <div>
              <Input
                type="email"
                placeholder="Email *"
                value={addForm.email}
                onChange={e => {
                  setAddForm(p => ({ ...p, email: e.target.value }));
                  clearAddFieldError("email");
                }}
                className={addFieldErrors.email ? "border-red-500 focus-visible:ring-red-500" : ""}
              />
              {addFieldErrors.email && (
                <p className="text-xs text-red-500 mt-1 ml-1">
                  {addForm.email.trim() ? "Please enter a valid email address" : "Email is required"}
                </p>
              )}
            </div>
            <div>
              <Input
                placeholder="Phone *"
                value={addForm.phone}
                onChange={e => {
                  setAddForm(p => ({ ...p, phone: e.target.value }));
                  clearAddFieldError("phone");
                }}
                className={addFieldErrors.phone ? "border-red-500 focus-visible:ring-red-500" : ""}
              />
              {addFieldErrors.phone && (
                <p className="text-xs text-red-500 mt-1 ml-1">Phone number is required</p>
              )}
            </div>
            <div>
              <Select
                value={addForm.job_posting_id}
                onValueChange={v => {
                  setAddForm(p => ({ ...p, job_posting_id: v }));
                  clearAddFieldError("job_posting_id");
                }}
              >
                <SelectTrigger className={addFieldErrors.job_posting_id ? "border-red-500 focus-visible:ring-red-500" : ""}>
                  <SelectValue placeholder="Select Job Posting *" />
                </SelectTrigger>
                <SelectContent>
                  {jobs.filter(j => j.status === "open").map(j => (
                    <SelectItem key={j.id} value={String(j.id)}>{j.title} — {j.department}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {addFieldErrors.job_posting_id && (
                <p className="text-xs text-red-500 mt-1 ml-1">Please select a job posting</p>
              )}
            </div>
            <div>
              <label className="text-xs text-muted-foreground">Resume (PDF/DOC, optional)</label>
              <Input type="file" accept=".pdf,.doc,.docx" className="mt-1" />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" className="bg-gray-200" onClick={() => setAddOpen(false)}>Cancel</Button>
            <Button className="bg-[#2B3588]" onClick={addApplicant} disabled={saving}>
              {saving && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Add Applicant
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Schedule Interview Dialog */}
      <Dialog open={schedOpen} onOpenChange={setSchedOpen}>
        <DialogContent className="max-w-sm">
          <DialogHeader>
            <DialogTitle>Schedule Interview</DialogTitle>
            {selApp && <p className="text-sm text-muted-foreground">{selApp.first_name} {selApp.last_name}</p>}
          </DialogHeader>
          <div className="space-y-3">
            <div>
              <Select
                value={schedForm.interviewer_id}
                onValueChange={v => {
                  setSchedForm(p => ({ ...p, interviewer_id: v }));
                  clearSchedFieldError("interviewer_id");
                }}
              >
                <SelectTrigger className={schedFieldErrors.interviewer_id ? "border-red-500 focus-visible:ring-red-500" : ""}>
                  <SelectValue placeholder="Select Interviewer *" />
                </SelectTrigger>
                <SelectContent>
                  {hrUsers.length === 0
                    ? <SelectItem value="0" disabled>No HR users found</SelectItem>
                    : hrUsers.map(u => <SelectItem key={u.id} value={String(u.id)}>{u.name}</SelectItem>)}
                </SelectContent>
              </Select>
              {schedFieldErrors.interviewer_id && (
                <p className="text-xs text-red-500 mt-1 ml-1">Please select an interviewer</p>
              )}
            </div>
            <div>
              <Input
                type="datetime-local"
                value={schedForm.scheduled_at}
                onChange={e => {
                  setSchedForm(p => ({ ...p, scheduled_at: e.target.value }));
                  clearSchedFieldError("scheduled_at");
                }}
                className={schedFieldErrors.scheduled_at ? "border-red-500 focus-visible:ring-red-500" : ""}
              />
              {schedFieldErrors.scheduled_at && (
                <p className="text-xs text-red-500 mt-1 ml-1">Please select a date and time</p>
              )}
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" className="bg-gray-200" onClick={() => setSchedOpen(false)}>Cancel</Button>
            <Button className="bg-[#2B3588]" onClick={scheduleInterview} disabled={saving}>
              {saving && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Schedule
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════
// TAB 3 — SCHEDULED INTERVIEWS
// ═══════════════════════════════════════════════════════════════════════

function ScheduledInterviewsTab({ canComplete }: { canComplete: boolean }) {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;
  const { toast } = useToast();
  const [interviews, setInterviews] = useState<Interview[]>([]);
  const [loading, setLoading] = useState(true);
  const [completing, setCompleting] = useState<number | null>(null);

  const load = async () => {
    setLoading(true);
    setInterviews(await safeFetch("/api/recruitment/interviews"));
    setLoading(false);
  };

  useEffect(() => { load(); }, []);

  const complete = async (id: number) => {
    setCompleting(id);
    try {
      const res = await authFetch(`/api/recruitment/interviews/${id}/complete`, { method: "POST" });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Interview completed successfully", variant: "success" });
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setCompleting(null);
    }
  };

  const statusStyles: Record<string, string> = {
    scheduled: "bg-blue-100 text-blue-700",
    completed: "bg-green-100 text-green-700",
    cancelled: "bg-red-100 text-red-700",
  };

  const totalPages = Math.ceil(interviews.length / itemsPerPage);
  const paginated = interviews.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  return (
    <div className="space-y-4">
      {loading ? (
        <div className="flex justify-center py-16"><Loader2 className="h-8 w-8 animate-spin text-muted-foreground" /></div>
      ) : interviews.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16">
          <Calendar className="h-10 w-10 text-muted-foreground/40 mb-3" />
          <p className="text-muted-foreground font-medium">No interviews scheduled</p>
        </div>
      ) : (
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-muted/30 border-b border-border">
              <tr>
                <th className="px-4 py-3 text-left font-semibold">Applicant</th>
                <th className="px-4 py-3 text-left font-semibold">Position</th>
                <th className="px-4 py-3 text-left font-semibold">Date & Time</th>
                <th className="px-4 py-3 text-left font-semibold">Interviewer</th>
                <th className="px-4 py-3 text-left font-semibold">Status</th>
                <th className="px-4 py-3 text-right font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {paginated.map(iv => (
                <tr key={iv.id} className="hover:bg-muted/20 transition-colors">
                  <td className="px-4 py-3 font-medium">{iv.applicant?.first_name} {iv.applicant?.last_name}</td>
                  <td className="px-4 py-3 text-muted-foreground">{iv.applicant?.job_posting?.title ?? "—"}</td>
                  <td className="px-4 py-3 text-muted-foreground">
                    {iv.scheduled_at ? new Date(iv.scheduled_at).toLocaleString() : "—"}
                  </td>
                  <td className="px-4 py-3 text-muted-foreground">{iv.interviewer?.name ?? "—"}</td>
                  <td className="px-4 py-3">
                    <Badge className={cn("text-xs border-0", statusStyles[iv.status])}>
                      {iv.status.charAt(0).toUpperCase() + iv.status.slice(1)}
                    </Badge>
                  </td>
                  <td className="px-4 py-3 text-right">
                    {iv.status === "scheduled" && canComplete && (
                      <Button size="sm" variant="outline" className="text-xs h-7 gap-1"
                        disabled={completing === iv.id}
                        onClick={() => complete(iv.id)}>
                        {completing === iv.id ? <Loader2 className="h-3 w-3 animate-spin" /> : <CheckCircle className="h-3 w-3" />}
                        Complete
                      </Button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      <Pagination
        currentPage={currentPage}
        totalPages={Math.ceil(interviews.length / itemsPerPage)}
        totalItems={interviews.length}
        itemsPerPage={itemsPerPage}
        onPageChange={setCurrentPage}
      />
    </div>
  );
}

// ═══════════════════════════════════════════════════════════════════════
// TAB 4 — TRAINING PROGRAMS
// ═══════════════════════════════════════════════════════════════════════

function TrainingProgramsTab({ canManage }: { canManage: boolean }) {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;
  const { toast } = useToast();
  const [assignments, setAssignments] = useState<TrainingAssignment[]>([]);
  const [employees, setEmployees] = useState<{ id: number; first_name: string; last_name: string; department: string }[]>([]);
  const [loading, setLoading] = useState(true);
  const [trainerOpen, setTrainerOpen] = useState(false);
  const [selAssignment, setSelAssignment] = useState<TrainingAssignment | null>(null);
  const [trainerId, setTrainerId] = useState("");
  const [saving, setSaving] = useState(false);
  const [completing, setCompleting] = useState<number | null>(null);

  const load = async () => {
    setLoading(true);
    const [assgn, emps] = await Promise.all([
      safeFetch<TrainingAssignment>("/api/recruitment/training-assignments"),
      safeFetch<{ id: number; first_name: string; last_name: string; department: string }>("/api/employees"),
    ]);
    setAssignments(assgn);
    setEmployees(emps);
    setLoading(false);
  };

  useEffect(() => { load(); }, []);

  const [trainerError, setTrainerError] = useState(false);

  // Replace the assignTrainer function
  const assignTrainer = async () => {
    if (!selAssignment) return;

    if (!trainerId) {
      setTrainerError(true);
      toast({ title: "Please select a trainer", variant: "destructive" });
      return;
    }

    setSaving(true);
    try {
      const res = await authFetch(`/api/recruitment/training-assignments/${selAssignment.id}/assign-trainer`, {
        method: "POST",
        body: JSON.stringify({ trainer_id: parseInt(trainerId) }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Trainer assigned successfully", variant: "success" });
      setTrainerOpen(false);
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setSaving(false);
    }
  };

  const completeTraining = async (id: number) => {
    setCompleting(id);
    try {
      const res = await authFetch(`/api/recruitment/training-assignments/${id}/complete`, { method: "POST" });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message ?? "Failed");
      toast({ title: "Training completed! New hire record created.", variant: "success" });
      load();
    } catch (err) {
      toast({ title: err instanceof Error ? err.message : "Failed", variant: "destructive" });
    } finally {
      setCompleting(null);
    }
  };

  const statusColors = {
    pending: "bg-yellow-100 text-yellow-700",
    in_progress: "bg-blue-100 text-blue-700",
    completed: "bg-green-100 text-green-700"
  };

  const totalPages = Math.ceil(assignments.length / itemsPerPage);
  const paginated = assignments.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  return (
    <div className="space-y-4">
      {loading ? (
        <div className="flex justify-center py-16"><Loader2 className="h-8 w-8 animate-spin text-muted-foreground" /></div>
      ) : assignments.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-16">
          <GraduationCap className="h-10 w-10 text-muted-foreground/40 mb-3" />
          <p className="text-muted-foreground font-medium">No training records yet</p>
          <p className="text-sm text-muted-foreground mt-1">Training is auto-created when an applicant is hired</p>
        </div>
      ) : (
        <div className="rounded-xl border border-border bg-card overflow-hidden">
          <table className="w-full text-sm">
            <thead className="bg-muted/30 border-b border-border">
              <tr>
                <th className="px-4 py-3 text-left font-semibold">Training</th>
                <th className="px-4 py-3 text-left font-semibold">New Hire</th>
                <th className="px-4 py-3 text-left font-semibold">Trainer</th>
                <th className="px-4 py-3 text-left font-semibold">Status</th>
                <th className="px-4 py-3 text-right font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {paginated.map(a => (
                <tr key={a.id} className="hover:bg-muted/20 transition-colors">
                  <td className="px-4 py-3 font-medium">{a.training?.title ?? "—"}</td>
                  <td className="px-4 py-3 text-muted-foreground">
                    {a.employee?.first_name} {a.employee?.last_name}
                  </td>
                  <td className="px-4 py-3 text-muted-foreground">
                    {a.trainer ? `${a.trainer.first_name} ${a.trainer.last_name}` : <span className="text-orange-500 text-xs">Not assigned</span>}
                  </td>
                  <td className="px-4 py-3">
                    <Badge className={cn("text-xs border-0", statusColors[a.status])}>
                      {a.status === "in_progress" ? "In Progress" : a.status.charAt(0).toUpperCase() + a.status.slice(1)}
                    </Badge>
                  </td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex items-center justify-end gap-2">
                      {a.status === "pending" && canManage && (
                        <Button size="sm" variant="outline" className="text-xs h-7 gap-1"
                          onClick={() => { setSelAssignment(a); setTrainerId(""); setTrainerOpen(true); }}>
                          <UserCheck className="h-3 w-3" /> Assign Trainer
                        </Button>
                      )}
                      {a.status === "in_progress" && canManage && (
                        <Button size="sm" className="text-xs h-7 gap-1 bg-green-600 hover:bg-green-700"
                          disabled={completing === a.id}
                          onClick={() => completeTraining(a.id)}>
                          {completing === a.id ? <Loader2 className="h-3 w-3 animate-spin" /> : <CheckCircle className="h-3 w-3" />}
                          Complete Training
                        </Button>
                      )}
                      {a.status === "completed" && (
                        <Badge className="bg-green-100 text-green-700 border-0 text-xs">✓ Ready for Transfer</Badge>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      <Pagination
        currentPage={currentPage}
        totalPages={Math.ceil(assignments.length / itemsPerPage)}
        totalItems={assignments.length}
        itemsPerPage={itemsPerPage}
        onPageChange={setCurrentPage}
      />

      <Dialog open={trainerOpen} onOpenChange={(open) => {
        setTrainerOpen(open);
        if (!open) setTrainerError(false);
      }}>
        <DialogContent className="max-w-sm">
          <DialogHeader><DialogTitle className="text-2xl font-semibold">Assign Trainer</DialogTitle></DialogHeader>
          <div>
            <Select
              value={trainerId}
              onValueChange={(v) => {
                setTrainerId(v);
                setTrainerError(false);
              }}
            >
              <SelectTrigger className={trainerError ? "border-red-500 focus-visible:ring-red-500" : ""}>
                <SelectValue placeholder="Select Trainer *" />
              </SelectTrigger>
              <SelectContent>
                {employees.map(e => (
                  <SelectItem key={e.id} value={String(e.id)}>
                    {e.first_name} {e.last_name} — {e.department}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {trainerError && (
              <p className="text-xs text-red-500 mt-1 ml-1">Please select a trainer</p>
            )}
          </div>
          <DialogFooter>
            <Button variant="outline" className="bg-gray-200" onClick={() => setTrainerOpen(false)}>Cancel</Button>
            <Button className="bg-[#2B3588]" onClick={assignTrainer} disabled={saving}>
              {saving && <Loader2 className="mr-2 h-4 w-4 animate-spin" />} Assign
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
// ═══════════════════════════════════════════════════════════════════════
// MAIN PAGE
// ═══════════════════════════════════════════════════════════════════════

export default function Recruitment() {
  const { user } = useAuth();
  const isHR = user?.role === "HR";
  const isAdmin = user?.role === "Admin";

  return (
    <DashboardLayout>
      <div className="mb-6">
        <h1 className="font-display text-3xl font-semibold text-foreground">Recruitment</h1>
      </div>

      <Tabs defaultValue={isAdmin ? "applicants" : "vacancies"}>
        <TabsList className="grid w-full" style={{ gridTemplateColumns: isAdmin ? "1fr 1fr" : "repeat(4, 1fr)" }}>
          {isHR && <TabsTrigger value="vacancies">Job Vacancies</TabsTrigger>}
          <TabsTrigger value="applicants">Applicants</TabsTrigger>
          <TabsTrigger value="interviews">Interviews</TabsTrigger>
          {isHR && <TabsTrigger value="training">Training</TabsTrigger>}
        </TabsList>

        {isHR && <TabsContent value="vacancies" className="mt-6"><JobVacanciesTab canManage={isHR} /></TabsContent>}
        <TabsContent value="applicants" className="mt-6"><ApplicantManagementTab canManage={isHR} isAdmin={isAdmin} /></TabsContent>
        <TabsContent value="interviews" className="mt-6"><ScheduledInterviewsTab canComplete={isHR} /></TabsContent>
        {isHR && <TabsContent value="training" className="mt-6"><TrainingProgramsTab canManage={isHR} /></TabsContent>}
      </Tabs>
    </DashboardLayout>
  );
}