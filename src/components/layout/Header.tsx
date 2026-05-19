// src/components/layout/Header.tsx
import { useAuth } from "@/hooks/useAuth";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Menu } from "lucide-react";
import { cn } from "@/lib/utils";

interface HeaderProps {
  collapsed?: boolean;
  onToggleCollapse?: () => void;
}

export function Header({ collapsed = false, onToggleCollapse }: HeaderProps) {
  const { user } = useAuth();

  const initials = user?.name
    ? user.name.split(" ").map((n) => n[0]).join("").toUpperCase().slice(0, 2)
    : "?";

  return (
    <header className="flex h-16 items-center justify-between border-b border-border bg-card px-6">
      {/* Left side - Collapse button */}
      <div className="flex items-center gap-3">
        <Button
          variant="ghost"
          size="sm"
          className="h-8 w-8 p-0 text-muted-foreground hover:text-foreground"
          onClick={onToggleCollapse}
        >
          <Menu className="h-5 w-5" />
        </Button>
        
        {/* Optional: Page title can go here */}
        {/* <span className="text-sm font-medium text-muted-foreground hidden sm:inline">
          Dashboard
        </span> */}
      </div>

      {/* Right side - User info */}
      <div className="flex items-center gap-2">
        <Avatar className="h-8 w-8">
          <AvatarFallback
            className="text-white text-xs font-semibold"
            style={{ backgroundColor: "#44AFE4" }}
          >
            {initials}
          </AvatarFallback>
        </Avatar>

        {user && (
          <div className="hidden sm:block">
            <p className="text-sm font-medium text-foreground leading-none">
              {user.name}
            </p>
            <p className="text-xs text-muted-foreground mt-0.5">
              {user.role}
            </p>
          </div>
        )}
      </div>
    </header>
  );
}