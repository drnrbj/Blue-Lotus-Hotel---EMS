import { useEffect, useState } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem,
  DropdownMenuSeparator, DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Eye, Pencil, Archive, MoreHorizontal, Search, Loader2, Users } from "lucide-react";
import { cn } from "@/lib/utils";
import type { Employee } from "@/types/employee";

interface Props {
  employees: Employee[];
  isLoading: boolean;
  isAdmin: boolean;
  isHR: boolean;
  onView: (emp: Employee) => void;
  onEdit: (emp: Employee) => void;
  onArchive: (emp: Employee) => void;
  onSearch: (value: string) => void;
  onFilter: (status: string) => void;
  onDeptFilter: (dept: string) => void;
}

const STATUS_COLORS: Record<string, string> = {
  active: "bg-green-100 text-green-700",
  on_leave: "bg-yellow-100 text-yellow-700",
  onboarding: "bg-purple-100 text-purple-700",
  suspended: "bg-orange-100 text-orange-700",
  terminated: "bg-red-100 text-red-700",
};

const SHIFT_COLORS: Record<string, string> = {
  morning: "bg-sky-100 text-sky-700",
  afternoon: "bg-amber-100 text-amber-700",
  night: "bg-indigo-100 text-indigo-700",
};

export function EmployeeTable({
  employees, isLoading, isAdmin, isHR,
  onView, onEdit, onArchive, onSearch, onFilter, onDeptFilter,
}: Props) {
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [hideOnboarding, setHideOnboarding] = useState(true);
  const itemsPerPage = 5;

  const departments = [...new Set(employees.map(e => e.department).filter(Boolean))].sort();
  const visibleEmployees = hideOnboarding
    ? employees.filter(e => e.status !== "onboarding")
    : employees;
  const totalPages = Math.ceil(visibleEmployees.length / itemsPerPage);
  const paginated = visibleEmployees.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

  useEffect(() => { setCurrentPage(1); }, [employees.length]);

  return (
    <div className="space-y-4">

      {/* Filters */}
      <div className="flex flex-wrap items-center gap-3">
        <div className="relative flex-1 min-w-[200px]">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            value={search}
            onChange={e => { setSearch(e.target.value); onSearch(e.target.value); }}
            placeholder="Search name, email..."
            className="pl-9"
          />
        </div>

        <Select onValueChange={v => onFilter(v === "all" ? "" : v)}>
          <SelectTrigger className="w-44">
            <SelectValue placeholder="All statuses" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All statuses</SelectItem>
            <SelectItem value="active">Active</SelectItem>
            <SelectItem value="onboarding">Onboarding</SelectItem>
            <SelectItem value="on_leave">On Leave</SelectItem>
            <SelectItem value="suspended">Suspended</SelectItem>
            <SelectItem value="terminated">Terminated</SelectItem>
          </SelectContent>
        </Select>

        <Select onValueChange={v => onDeptFilter(v === "all" ? "" : v)}>
          <SelectTrigger className="w-44">
            <SelectValue placeholder="All departments" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">All departments</SelectItem>
            {departments.map(dept => (
              <SelectItem key={dept} value={dept}>{dept}</SelectItem>
            ))}
          </SelectContent>
        </Select>

        <button
          type="button"
          onClick={() => { setHideOnboarding(p => !p); setCurrentPage(1); }}
          className={`h-9 px-3 rounded-md border text-xs font-medium transition-all ${hideOnboarding
              ? "border-gray-200 bg-white text-gray-500 hover:border-gray-300"
              : "border-purple-300 bg-purple-50 text-purple-700"
            }`}
        >
          {hideOnboarding ? "Show Onboarding" : "Hide Onboarding"}
        </button>
      </div>

      {/* Table */}
      {isLoading && employees.length === 0 ? (
        <div className="flex justify-center py-20">
          <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
        </div>
      ) : employees.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-dashed py-20">
          <Users className="h-10 w-10 text-muted-foreground/40 mb-3" />
          <p className="font-medium text-muted-foreground">No employees found</p>
          <p className="text-sm text-muted-foreground mt-1">Employees are added through the Recruitment pipeline</p>
        </div>
      ) : (
        <div className="space-y-4">
          <div className="rounded-xl border border-border bg-card overflow-hidden">
            <table className="w-full text-sm">
              <thead className="bg-muted/30 border-b border-border">
                <tr>
                  <th className="px-4 py-3 text-left font-semibold">Employee</th>
                  <th className="px-4 py-3 text-left font-semibold">Department</th>
                  <th className="px-4 py-3 text-left font-semibold">Job Category</th>
                  <th className="px-4 py-3 text-left font-semibold">Shift</th>
                  <th className="px-4 py-3 text-left font-semibold">Status</th>
                  <th className="px-4 py-3 text-right font-semibold">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                {paginated.map(emp => (
                  <tr key={emp.id} className="hover:bg-muted/20 transition-colors">
                    <td className="px-4 py-3">
                      <p className="font-medium">
                        {emp.first_name} {emp.last_name}
                        {emp.name_extension ? ` ${emp.name_extension}` : ""}
                      </p>
                      <p className="text-xs text-muted-foreground">{emp.email}</p>
                    </td>
                    <td className="px-4 py-3 text-muted-foreground">{emp.department}</td>
                    <td className="px-4 py-3 text-muted-foreground text-xs">{emp.job_category}</td>
                    <td className="px-4 py-3">
                      <Badge className={cn("text-xs border-0 capitalize", SHIFT_COLORS[emp.shift_sched] ?? "bg-gray-100 text-gray-600")}>
                        {emp.shift_sched}
                      </Badge>
                    </td>
                    <td className="px-4 py-3">
                      <Badge className={cn("text-xs border-0 capitalize", STATUS_COLORS[emp.status] ?? "bg-gray-100 text-gray-600")}>
                        {emp.status?.replace("_", " ")}
                      </Badge>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex items-center justify-end gap-1">
                        <Button variant="ghost" size="sm" className="h-8 w-8 p-0"
                          onClick={() => onView(emp)} title="View profile">
                          <Eye className="h-4 w-4" />
                        </Button>
                        {isHR && (
                          <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                              <Button variant="ghost" size="sm" className="h-8 w-8 p-0">
                                <MoreHorizontal className="h-4 w-4" />
                              </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                              <DropdownMenuItem onClick={() => onEdit(emp)}>
                                <Pencil className="mr-2 h-4 w-4" /> Edit
                              </DropdownMenuItem>
                              <DropdownMenuSeparator />
                              <DropdownMenuItem
                                className="text-red-600 focus:text-red-600"
                                onClick={() => onArchive(emp)}
                              >
                                <Archive className="mr-2 h-4 w-4" /> Archive
                              </DropdownMenuItem>
                            </DropdownMenuContent>
                          </DropdownMenu>
                        )}
                        {isAdmin && (
                          <Button variant="ghost" size="sm" className="h-8 w-8 p-0" onClick={() => onEdit(emp)} title="Edit system role">
                            <Pencil className="h-4 w-4 text-[#2B3588]" />
                          </Button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {totalPages > 1 && (
            <div className="flex items-center justify-between px-1">
              <p className="text-xs text-muted-foreground">
                Showing {((currentPage - 1) * itemsPerPage) + 1}–{Math.min(currentPage * itemsPerPage, employees.length)} of {employees.length} employees
              </p>
              <div className="flex items-center gap-1">
                <Button
                  variant="outline" size="sm"
                  className="h-8 px-3 text-xs"
                  disabled={currentPage === 1}
                  onClick={() => setCurrentPage(p => p - 1)}
                >
                  Previous
                </Button>
                {Array.from({ length: totalPages }, (_, i) => i + 1)
                  .filter(page =>
                    page === 1 ||
                    page === totalPages ||
                    Math.abs(page - currentPage) <= 1
                  )
                  .reduce<(number | string)[]>((acc, page, idx, arr) => {
                    if (idx > 0 && (page as number) - (arr[idx - 1] as number) > 1) {
                      acc.push("...");
                    }
                    acc.push(page);
                    return acc;
                  }, [])
                  .map((page, idx) =>
                    page === "..." ? (
                      <span key={`ellipsis-${idx}`} className="px-1 text-xs text-muted-foreground">…</span>
                    ) : (
                      <Button
                        key={page}
                        variant={currentPage === page ? "default" : "outline"}
                        size="sm"
                        className={`h-8 w-8 p-0 text-xs ${currentPage === page ? "bg-[#2B3588] hover:bg-[#232c70] text-white" : ""}`}
                        onClick={() => setCurrentPage(page as number)}
                      >
                        {page}
                      </Button>
                    )
                  )}
                <Button
                  variant="outline" size="sm"
                  className="h-8 px-3 text-xs"
                  disabled={currentPage === totalPages}
                  onClick={() => setCurrentPage(p => p + 1)}
                >
                  Next
                </Button>
              </div>
            </div>
          )}
        </div>
      )}

    </div>
  );
}