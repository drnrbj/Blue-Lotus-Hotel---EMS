// src/components/layout/DashboardLayout.tsx
import { ReactNode, useState } from "react";
import { Sidebar } from "@/components/layout/Sidebar";
import { Header } from "./Header";

interface DashboardLayoutProps {
  children: ReactNode;
}

export function DashboardLayout({ children }: DashboardLayoutProps) {
  const [collapsed, setCollapsed] = useState(false);

  const handleToggleCollapse = () => {
    setCollapsed(!collapsed);
  };

  return (
    <div className="flex h-screen w-full overflow-hidden bg-background">
      {/* Sidebar */}
      <Sidebar collapsed={collapsed} />

      {/* Main content area */}
      <div className="flex flex-1 flex-col overflow-hidden">
        <Header collapsed={collapsed} onToggleCollapse={handleToggleCollapse} />
        <main className="flex-1 overflow-y-auto p-6">
          {children}
        </main>
      </div>
    </div>
  );
}