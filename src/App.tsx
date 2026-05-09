import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";

import Dashboard         from "./pages/Dashboard";
import Employees         from "./pages/Employees";
import Attendance        from "./pages/Attendance";
import Accounting        from "./pages/Accounting";
import Performance       from "./pages/Performance";
import Leave             from "./pages/Leave";
import Recruitment       from "./pages/Recruitment";
import Training          from "./pages/Training";
import ArchivedEmployees from "./pages/ArchivedEmployees";
import Login             from "./pages/Login";
import NotFound          from "./pages/NotFound";

const queryClient = new QueryClient();

function PrivateRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuth();
  return isAuthenticated ? <>{children}</> : <Navigate to="/login" replace />;
}

function RoleRoute({ children, roles }: { children: React.ReactNode; roles: string[] }) {
  const { isAuthenticated, user } = useAuth();
  if (!isAuthenticated) return <Navigate to="/login" replace />;
  if (!roles.includes(user?.role ?? "")) return <Navigate to="/" replace />;
  return <>{children}</>;
}

function PublicRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuth();
  return isAuthenticated ? <Navigate to="/" replace /> : <>{children}</>;
}

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          {/* Public */}
          <Route path="/login" element={<PublicRoute><Login /></PublicRoute>} />

          // REPLACE all routes after the public ones with this:

          {/* Dashboard — all authenticated */}
          <Route path="/"  element={<PrivateRoute><Dashboard /></PrivateRoute>} />

          {/* HR + Admin */}
          <Route path="/employees" element={
            <RoleRoute roles={["Admin", "HR"]}><Employees /></RoleRoute>
          } />

          {/* Admin + HR */}
          <Route path="/attendance" element={
            <RoleRoute roles={["Admin", "HR"]}><Attendance /></RoleRoute>
          } />
          <Route path="/leave" element={
            <RoleRoute roles={["Admin", "HR"]}><Leave /></RoleRoute>
          } />
          <Route path="/recruitment" element={
            <RoleRoute roles={["Admin", "HR"]}><Recruitment /></RoleRoute>
          } />
          <Route path="/archived-employees" element={
            <RoleRoute roles={["Admin", "HR"]}><ArchivedEmployees /></RoleRoute>
          } />

          {/* Admin + HR */}
          <Route path="/performance" element={
            <RoleRoute roles={["Admin", "HR"]}><Performance /></RoleRoute>
          } />

          {/* Accountant only */}
          <Route path="/payroll" element={
            <RoleRoute roles={["Accountant"]}><Accounting /></RoleRoute>
          } />

          <Route path="/training" element={
            <RoleRoute roles={["Admin", "HR"]}><Training /></RoleRoute>
          } />

          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;