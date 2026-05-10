// src/components/layout/Header.tsx
import { useAuth } from "@/hooks/useAuth";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";

export function Header() {
  const { user } = useAuth();

  const initials = user?.name
    ? user.name.split(" ").map((n) => n[0]).join("").toUpperCase().slice(0, 2)
    : "?";

  return (
    <header className="flex h-16 items-center justify-end border-b border-border bg-card px-6">
      {/* Right side */}
      <div className="flex items-center gap-2">
        <Avatar className="h-8 w-8">
          <AvatarFallback
            className="text-white text-xs font-semibold"
            style={{ backgroundColor: "#2B3588" }}
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