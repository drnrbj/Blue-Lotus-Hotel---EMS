// src/components/employees/EmployeeForm.tsx
import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Loader2 } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import { cn } from "@/lib/utils";
import type { Employee, EmployeeFormData } from "@/types/employee";

const SALARY_MAP: Record<string, number> = {
  'Front Desk Agent': 18000, 'Concierge': 19000, 'Reservations Agent': 18500,
  'Guest Relations Officer': 20000, 'Bell Staff': 16000,
  'Room Attendant': 16000, 'Laundry Attendant': 15500,
  'Housekeeping Supervisor': 22000, 'Public Area Cleaner': 15000,
  'Waiter/Waitress': 16500, 'Bartender': 18000, 'Chef de Partie': 25000,
  'Sous Chef': 32000, 'Executive Chef': 45000, 'Kitchen Steward': 15000,
  'Maintenance Technician': 19000, 'Electrician': 22000, 'Plumber': 21000,
  'Maintenance Supervisor': 28000,
  'HR Officer': 25000, 'Accounting Staff': 24000, 'Payroll Officer': 26000,
  'General Manager': 65000, 'Department Manager': 42000, 'Supervisor': 28000,
  'Security Guard': 17000, 'Security Supervisor': 22000,
  'Sales Manager': 38000, 'Marketing Officer': 28000, 'Reservations Manager': 32000,
};

const JOB_CATEGORIES_BY_DEPT: Record<string, string[]> = {
  'Front Office': ['Front Desk Agent', 'Concierge', 'Reservations Agent', 'Guest Relations Officer', 'Bell Staff'],
  'Housekeeping': ['Room Attendant', 'Laundry Attendant', 'Housekeeping Supervisor', 'Public Area Cleaner'],
  'Food & Beverage': ['Waiter/Waitress', 'Bartender', 'Chef de Partie', 'Sous Chef', 'Executive Chef', 'Kitchen Steward'],
  'Maintenance': ['Maintenance Technician', 'Electrician', 'Plumber', 'Maintenance Supervisor'],
  'Administration': ['HR Officer', 'Accounting Staff', 'Payroll Officer', 'General Manager', 'Department Manager', 'Supervisor'],
  'Security': ['Security Guard', 'Security Supervisor'],
  'Sales & Marketing': ['Sales Manager', 'Marketing Officer', 'Reservations Manager'],
};

const DEPARTMENTS = Object.keys(JOB_CATEGORIES_BY_DEPT);

type RoleType = "Employee" | "HR" | "Manager" | "Accountant" | "Admin";

const determineRole = (dept: string, job: string): RoleType => {
  if (dept === 'Administration' && job === 'HR Officer') return 'HR';
  if (dept === 'Administration' && job === 'Accounting Staff') return 'Accountant';
  if (dept === 'Administration' && job === 'Payroll Officer') return 'Accountant';
  if (job === 'General Manager' || job === 'Department Manager') return 'Admin';
  if (job === 'Supervisor' || job.includes('Supervisor')) return 'Manager';
  return 'Employee';
};

type FormData = {
  first_name: string; last_name: string; middle_name: string; name_extension: string;
  date_of_birth: string; email: string; phone_number: string; home_address: string;
  emergency_contact_name: string; emergency_contact_number: string; relationship: string;
  tin: string; sss_number: string; pagibig_number: string; philhealth_number: string;
  bank_name: string; account_name: string; account_number: string;
  start_date: string; department: string; job_category: string;
  employment_type: string; shift_sched: string;
  basic_salary: string; role: RoleType;
};

// Which tab each required field lives on — used to auto-navigate on error
const FIELD_TAB: Partial<Record<keyof FormData, string>> = {
  first_name: "personal", last_name: "personal",
  date_of_birth: "personal", email: "personal", home_address: "personal",
  department: "employment", job_category: "employment", start_date: "employment",
  phone_number: "contact",
};

const FIELD_LABELS: Partial<Record<keyof FormData, string>> = {
  first_name: "First Name", last_name: "Last Name",
  date_of_birth: "Date of Birth", email: "Email",
  home_address: "Home Address", phone_number: "Phone Number",
  department: "Department", job_category: "Job Category",
  start_date: "Start Date",
};

// ─── Field component OUTSIDE EmployeeForm so it never remounts on re-render ──
interface FieldProps {
  label: string;
  field: keyof FormData;
  form: FormData;
  errors: Partial<Record<keyof FormData, string>>;
  onChange: (field: keyof FormData, value: string) => void;
  required?: boolean;
  type?: string;
  placeholder?: string;
  readOnly?: boolean;
}

function Field({ label, field, form, errors, onChange, required, type = "text", placeholder, readOnly }: FieldProps) {
  return (
    <div>
      <label className="text-xs font-medium text-foreground/80">
        {label}{required && <span className="text-red-500 ml-0.5">*</span>}
      </label>
      <Input
        className={cn(
          "mt-1 h-9",
          errors[field] && "border-red-400 bg-red-50",
          readOnly && "bg-muted/30"
        )}
        type={type}
        value={form[field] ?? ""}
        onChange={e => onChange(field, e.target.value)}
        placeholder={placeholder}
        readOnly={readOnly}
      />
      {errors[field] && <p className="text-red-500 text-xs mt-0.5">{errors[field]}</p>}
    </div>
  );
}

interface Props {
  employee?: Employee;
  onSubmit: (data: EmployeeFormData) => Promise<void>;
  onCancel: () => void;
  isLoading?: boolean;
  isAdminView?: boolean;
}

export function EmployeeForm({ employee, onSubmit, onCancel, isLoading, isAdminView }: Props) {
  const { toast } = useToast();
  const [tab, setTab] = useState("personal");

  const [form, setForm] = useState<FormData>({
    first_name: employee?.first_name ?? "",
    last_name: employee?.last_name ?? "",
    middle_name: employee?.middle_name ?? "",
    name_extension: employee?.name_extension ?? "",
    date_of_birth: employee?.date_of_birth
  ? new Date(employee.date_of_birth).toISOString().split("T")[0]
  : "",
    email: employee?.email ?? "",
    phone_number: employee?.phone_number ?? "",
    home_address: employee?.home_address ?? "",
    emergency_contact_name: employee?.emergency_contact_name ?? "",
    emergency_contact_number: employee?.emergency_contact_number ?? "",
    relationship: employee?.relationship ?? "",
    tin: employee?.tin ?? "",
    sss_number: employee?.sss_number ?? "",
    pagibig_number: employee?.pagibig_number ?? "",
    philhealth_number: employee?.philhealth_number ?? "",
    bank_name: employee?.bank_name ?? "",
    account_name: employee?.account_name ?? "",
    account_number: employee?.account_number ?? "",
    start_date: employee?.start_date ?? "",
    department: employee?.department ?? "",
    job_category: employee?.job_category ?? "",
    employment_type: employee?.employment_type ?? "probationary",
    shift_sched: employee?.shift_sched ?? "morning",
    basic_salary: employee?.basic_salary ? String(employee.basic_salary) : "",
    role: (employee?.role as RoleType) ?? "Employee",
  });

  const [errors, setErrors] = useState<Partial<Record<keyof FormData, string>>>({});


  const handleChange = (field: keyof FormData, value: string) => {
    setForm(prev => ({ ...prev, [field]: value }));
    // Clear error for this field as soon as user fills it in
    if (errors[field]) setErrors(prev => ({ ...prev, [field]: undefined }));
  };

  // Auto-fill salary when job_category changes
  useEffect(() => {
    if (form.job_category && SALARY_MAP[form.job_category]) {
      setForm(prev => ({ ...prev, basic_salary: String(SALARY_MAP[form.job_category]) }));
    }
  }, [form.job_category]);

  // Auto-fill role when department and job_category change
  useEffect(() => {
    if (form.department && form.job_category) {
      setForm(prev => ({ ...prev, role: determineRole(form.department, form.job_category) }));
    }
  }, [form.department, form.job_category]);

  const availableCategories = form.department ? (JOB_CATEGORIES_BY_DEPT[form.department] ?? []) : [];

  const validate = (): boolean => {
    const e: Partial<Record<keyof FormData, string>> = {};
    if (!form.first_name.trim()) e.first_name = "First name is required";
    if (!form.last_name.trim()) e.last_name = "Last name is required";
    if (!form.date_of_birth) e.date_of_birth = "Date of birth is required";
    if (!form.email.trim()) e.email = "Email is required";
    if (!form.phone_number.trim()) e.phone_number = "Phone number is required";
    if (!form.home_address.trim()) e.home_address = "Home address is required";
    if (!form.start_date) e.start_date = "Start date is required";
    if (!form.department) e.department = "Department is required";
    if (!form.job_category) e.job_category = "Job category is required";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    // Admin view — only submit role
    if (isAdminView) {
      await onSubmit({ role: form.role } as any);
      return;
    }

    // Run validation — populates errors state (turns fields red)
    const newErrors: Partial<Record<keyof FormData, string>> = {};
    if (!form.first_name.trim()) newErrors.first_name = "Required";
    if (!form.last_name.trim()) newErrors.last_name = "Required";
    if (!form.date_of_birth || form.date_of_birth.trim() === "") {
      newErrors.date_of_birth = "Date of Birth is required";
    }
    if (!form.email.trim()) newErrors.email = "Required";
    if (!form.phone_number.trim()) newErrors.phone_number = "Required";
    if (!form.home_address.trim()) newErrors.home_address = "Required";
    if (!form.start_date) newErrors.start_date = "Required";
    if (!form.department) newErrors.department = "Required";
    if (!form.job_category) newErrors.job_category = "Required";

    setErrors(newErrors);  // ← this makes the red borders appear

    if (Object.keys(newErrors).length > 0) {
      // Show toast listing every missing field
      const LABELS: Partial<Record<keyof FormData, string>> = {
        first_name: "First Name", last_name: "Last Name",
        date_of_birth: "Date of Birth", email: "Email",
        phone_number: "Phone Number", home_address: "Home Address",
        start_date: "Start Date", department: "Department",
        job_category: "Job Category",
      };
      const missing = Object.keys(newErrors).map(f => LABELS[f as keyof FormData] ?? f).join(", ");
      toast({
        title: "Cannot update employee",
        description: `Please fill in: ${missing}`,
        variant: "destructive",
      });

      // Navigate to the tab that has the first missing field
      const TAB_MAP: Partial<Record<keyof FormData, string>> = {
        first_name: "personal", last_name: "personal",
        date_of_birth: "personal", email: "personal", home_address: "personal",
        department: "employment", job_category: "employment", start_date: "employment",
        phone_number: "contact",
      };
      const firstField = Object.keys(newErrors)[0] as keyof FormData;
      if (TAB_MAP[firstField]) setTab(TAB_MAP[firstField]!);

      return; // ← BLOCKS the API call
    }

    // All valid — build payload and submit
    const formData: EmployeeFormData = {
      first_name: form.first_name,
      last_name: form.last_name,
      middle_name: form.middle_name || undefined,
      name_extension: form.name_extension || undefined,
      date_of_birth: form.date_of_birth,
      email: form.email,
      phone_number: form.phone_number,
      home_address: form.home_address,
      emergency_contact_name: form.emergency_contact_name,
      emergency_contact_number: form.emergency_contact_number,
      relationship: form.relationship,
      tin: form.tin || undefined,
      sss_number: form.sss_number || undefined,
      pagibig_number: form.pagibig_number || undefined,
      philhealth_number: form.philhealth_number || undefined,
      bank_name: form.bank_name || undefined,
      account_name: form.account_name || undefined,
      account_number: form.account_number || undefined,
      start_date: form.start_date,
      end_date: undefined,
      department: form.department,
      job_category: form.job_category,
      employment_type: form.employment_type as Employee["employment_type"],
      shift_sched: form.shift_sched as Employee["shift_sched"],
      basic_salary: parseFloat(form.basic_salary) || 0,
      role: form.role,
      manager_id: undefined,
    };
    await onSubmit(formData);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      {isAdminView ? (
        <div className="space-y-3 py-2">
          <p className="text-sm text-muted-foreground">
            You can only edit the System Role for this employee.
          </p>
          <div>
            <label className="text-xs font-medium">Employee</label>
            <Input className="mt-1 h-9 bg-muted/30" value={`${employee?.first_name} ${employee?.last_name}`} readOnly />
          </div>
          <div>
            <label className="text-xs font-medium">Department</label>
            <Input className="mt-1 h-9 bg-muted/30" value={employee?.department ?? ""} readOnly />
          </div>
          <div>
            <label className="text-xs font-medium">Job Category</label>
            <Input className="mt-1 h-9 bg-muted/30" value={employee?.job_category ?? ""} readOnly />
          </div>
          <div>
            <label className="text-xs font-medium font-semibold">System Role</label>
            <Select value={form.role} onValueChange={v => handleChange("role", v as RoleType)}>
              <SelectTrigger className="mt-1 h-9"><SelectValue /></SelectTrigger>
              <SelectContent>
                <SelectItem value="Employee">Employee</SelectItem>
                <SelectItem value="HR">HR</SelectItem>
                <SelectItem value="Accountant">Accountant</SelectItem>
                <SelectItem value="Admin">Admin</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>
      ) : (
        // ── controlled tab so we can navigate to the offending tab on error ──
        <Tabs value={tab} onValueChange={setTab}>
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="personal">Personal</TabsTrigger>
            <TabsTrigger value="employment">Employment</TabsTrigger>
            <TabsTrigger value="contact">Contact</TabsTrigger>
            <TabsTrigger value="banking">Banking</TabsTrigger>
          </TabsList>

          <TabsContent value="personal" className="space-y-3 pt-4">
            <div className="grid grid-cols-2 gap-3">
              <Field label="First Name" field="first_name" form={form} errors={errors} onChange={handleChange} required />
              <Field label="Last Name" field="last_name" form={form} errors={errors} onChange={handleChange} required />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <Field label="Middle Name" field="middle_name" form={form} errors={errors} onChange={handleChange} />
              <Field label="Name Extension" field="name_extension" form={form} errors={errors} onChange={handleChange} placeholder="Jr., Sr., III" />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <Field label="Date of Birth" field="date_of_birth" form={form} errors={errors} onChange={handleChange} required type="date" />
              <Field label="Email" field="email" form={form} errors={errors} onChange={handleChange} required type="email" />
            </div>
            <Field label="Home Address" field="home_address" form={form} errors={errors} onChange={handleChange} required />
          </TabsContent>

          <TabsContent value="employment" className="space-y-3 pt-4">
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="text-xs font-medium text-foreground/80">
                  Department<span className="text-red-500 ml-0.5">*</span>
                </label>
                <Select value={form.department} onValueChange={v => { handleChange("department", v); handleChange("job_category", ""); }}>
                  <SelectTrigger className={cn("mt-1 h-9", errors.department && "border-red-400 bg-red-50")}>
                    <SelectValue placeholder="Select department" />
                  </SelectTrigger>
                  <SelectContent>
                    {DEPARTMENTS.map(d => <SelectItem key={d} value={d}>{d}</SelectItem>)}
                  </SelectContent>
                </Select>
                {errors.department && <p className="text-red-500 text-xs mt-0.5">{errors.department}</p>}
              </div>
              <div>
                <label className="text-xs font-medium text-foreground/80">
                  Job Category<span className="text-red-500 ml-0.5">*</span>
                </label>
                <Select value={form.job_category} onValueChange={v => handleChange("job_category", v)} disabled={!form.department}>
                  <SelectTrigger className={cn("mt-1 h-9", errors.job_category && "border-red-400 bg-red-50")}>
                    <SelectValue placeholder={form.department ? "Select category" : "Select dept first"} />
                  </SelectTrigger>
                  <SelectContent>
                    {availableCategories.map(c => <SelectItem key={c} value={c}>{c}</SelectItem>)}
                  </SelectContent>
                </Select>
                {errors.job_category && <p className="text-red-500 text-xs mt-0.5">{errors.job_category}</p>}
              </div>
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="text-xs font-medium">Employment Type<span className="text-red-500 ml-0.5">*</span></label>
                <Select value={form.employment_type} onValueChange={v => handleChange("employment_type", v)}>
                  <SelectTrigger className="mt-1 h-9"><SelectValue /></SelectTrigger>
                  <SelectContent>
                    <SelectItem value="regular">Regular</SelectItem>
                    <SelectItem value="probationary">Probationary</SelectItem>
                    <SelectItem value="contractual">Contractual</SelectItem>
                    <SelectItem value="part_time">Part-time</SelectItem>
                    <SelectItem value="intern">Intern</SelectItem>
                  </SelectContent>
                </Select>
              </div>
              <div>
                <label className="text-xs font-medium">Shift Schedule</label>
                <Select value={form.shift_sched} onValueChange={v => handleChange("shift_sched", v)}>
                  <SelectTrigger className="mt-1 h-9"><SelectValue /></SelectTrigger>
                  <SelectContent>
                    <SelectItem value="morning">Morning (07:00–15:00)</SelectItem>
                    <SelectItem value="afternoon">Afternoon (15:00–23:00)</SelectItem>
                    <SelectItem value="night">Night (23:00–07:00)</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-3">
              <Field label="Start Date" field="start_date" form={form} errors={errors} onChange={handleChange} required type="date" />
              <div>
                <label className="text-xs font-medium">Basic Salary (₱)</label>
                <Input className="mt-1 h-9 bg-muted/30" type="number" value={form.basic_salary} readOnly />
                {form.basic_salary && (
                  <p className="text-xs text-green-600 mt-0.5">₱{Number(form.basic_salary).toLocaleString("en-PH")} / month</p>
                )}
              </div>
            </div>
          </TabsContent>

          <TabsContent value="contact" className="space-y-3 pt-4">
            <Field label="Phone Number" field="phone_number" form={form} errors={errors} onChange={handleChange} required placeholder="+63 9XX XXX XXXX" />
            <div className="rounded-lg border p-4 space-y-3">
              <p className="text-sm font-semibold">Emergency Contact</p>
              <div className="grid grid-cols-2 gap-3">
                <Field label="Contact Name" field="emergency_contact_name" form={form} errors={errors} onChange={handleChange} />
                <Field label="Contact Number" field="emergency_contact_number" form={form} errors={errors} onChange={handleChange} />
              </div>
              <Field label="Relationship" field="relationship" form={form} errors={errors} onChange={handleChange} placeholder="Spouse, Parent, Sibling" />
            </div>
            <div className="rounded-lg border p-4 space-y-3">
              <p className="text-sm font-semibold">Government IDs</p>
              <div className="grid grid-cols-2 gap-3">
                <Field label="TIN" field="tin" form={form} errors={errors} onChange={handleChange} placeholder="Tax ID Number" />
                <Field label="SSS Number" field="sss_number" form={form} errors={errors} onChange={handleChange} />
                <Field label="PhilHealth" field="philhealth_number" form={form} errors={errors} onChange={handleChange} />
                <Field label="Pag-IBIG" field="pagibig_number" form={form} errors={errors} onChange={handleChange} />
              </div>
            </div>
          </TabsContent>

          <TabsContent value="banking" className="space-y-3 pt-4">
            <div className="grid grid-cols-2 gap-3">
              <Field label="Bank Name" field="bank_name" form={form} errors={errors} onChange={handleChange} placeholder="BDO, BPI, Metrobank…" />
              <Field label="Account Name" field="account_name" form={form} errors={errors} onChange={handleChange} placeholder="Name on account" />
            </div>
            <Field label="Account Number" field="account_number" form={form} errors={errors} onChange={handleChange} />
            <p className="text-xs text-muted-foreground">Required for payroll disbursement.</p>
          </TabsContent>
        </Tabs>
      )}

      <div className="flex justify-end gap-3 border-t pt-4">
        <Button type="button" className="bg-gray-200" variant="outline" onClick={onCancel}>Cancel</Button>
        <Button type="submit" className="bg-[#2B3588]" disabled={isLoading}>
          {isLoading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
          {employee ? "Update Employee" : "Create Employee"}
        </Button>
      </div>
    </form>
  );
}