import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Loader2 } from "lucide-react";
import logo from "@/assets/logo.png";
import hotelBg from "@/assets/hotel.jpg";

export default function Login() {
  const { login, isLoading, error, clearError } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    e.stopPropagation();
    clearError();
    try {
      await login(email, password);
      navigate("/");
    } catch (err) {
      console.error("Login error:", err);
    }
  };

  const testCredentials = [
    { role: "Admin",     email: "admin@hrharmony.com",     pass: "Admin@1234" },
    { role: "HR",        email: "hr@hrharmony.com",        pass: "Hr@12345" },
    { role: "Accountant",email: "accountant@hrharmony.com",pass: "Account@1" },
  ];

  return (
    <div className="min-h-screen flex">

      {/* ── Left panel — branding ── */}
      <div
        className="hidden lg:flex lg:w-1/2 relative flex-col justify-between p-12"
        style={{ backgroundImage: `url(${hotelBg})`, backgroundSize: "cover", backgroundPosition: "center" }}
      >
        {/* Dark gradient overlay */}
        <div className="absolute inset-0" style={{
          background: "linear-gradient(135deg, rgba(43,53,136,0.85) 0%, rgba(0,0,0,0.6) 100%)"
        }} />

        {/* Logo + Hotel name */}
        <div className="relative z-10 flex items-center gap-3">
          <img src={logo} alt="Blue Lotus Hotel" style={{ width: 48, height: 30 }} className="object-contain" />
          <div>
            <p className="text-white font-bold text-base tracking-wide" style={{ fontFamily: "'Montserrat', sans-serif" }}>
              BLUE LOTUS HOTEL
            </p>
          </div>
        </div>

        {/* Center quote */}
        <div className="relative z-10">
          <h2 className="text-white text-4xl font-bold leading-snug mb-4" style={{ fontFamily: "'Montserrat', sans-serif" }}>
            Managing people,<br />
            <span style={{ color: "#FAEC1D" }}>inspiring excellence.</span>
          </h2>
        </div>

        {/* Bottom credit */}
        <div className="relative z-10">
          <p className="text-white/30 text-xs">© {new Date().getFullYear()} Blue Lotus Hotel. All rights reserved.</p>
        </div>
      </div>

      {/* ── Right panel — login form ── */}
      <div className="flex-1 flex flex-col items-center justify-center px-6 py-12 bg-white">
        
        {/* Mobile logo */}
        <div className="lg:hidden flex items-center gap-2 mb-8">
          <img src={logo} alt="Blue Lotus Hotel" style={{ width: 40, height: 25 }} className="object-contain" />
          <div>
            <p className="font-bold text-sm" style={{ color: "#2B3588", fontFamily: "'Montserrat', sans-serif" }}>BLUE LOTUS HOTEL</p>
          </div>
        </div>

        <div className="w-full max-w-md">

          {/* Heading */}
          <div className="mb-8">
            <h1 className="text-3xl font-bold text-gray-900 mb-1" style={{ fontFamily: "'Montserrat', sans-serif" }}>
              Welcome back
            </h1>
            <p className="text-gray-400 text-sm">Sign in to your account to continue</p>
          </div>

          {/* Form */}
          <form onSubmit={handleSubmit} noValidate className="space-y-5">

            <div className="space-y-1.5">
              <label htmlFor="email" className="text-sm font-medium text-gray-700">
                Email address
              </label>
              <input
                id="email"
                type="text"
                inputMode="email"
                autoComplete="email"
                placeholder="you@bluelotus.com"
                value={email}
                onChange={e => setEmail(e.target.value)}
                required
                autoFocus
                className="w-full h-11 rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:border-transparent transition"
                style={{ "--tw-ring-color": "#2B3588" } as React.CSSProperties}
                onFocus={e => e.currentTarget.style.boxShadow = "0 0 0 2px #2B3588"}
                onBlur={e => e.currentTarget.style.boxShadow = "none"}
              />
            </div>

            <div className="space-y-1.5">
              <div className="flex items-center justify-between">
                <label htmlFor="password" className="text-sm font-medium text-gray-700">
                  Password
                </label>
                <button
                  type="button"
                  className="text-xs font-medium hover:underline"
                  style={{ color: "#2B3588" }}
                  onClick={() => alert("Contact HR to reset password")}
                >
                  Forgot password?
                </button>
              </div>
              <div className="relative">
                <input
                  id="password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="current-password"
                  placeholder="••••••••"
                  value={password}
                  onChange={e => setPassword(e.target.value)}
                  required
                  className="w-full h-11 rounded-lg border border-gray-200 bg-gray-50 px-4 pr-12 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none transition"
                  onFocus={e => e.currentTarget.style.boxShadow = "0 0 0 2px #2B3588"}
                  onBlur={e => e.currentTarget.style.boxShadow = "none"}
                />
                <button
                  type="button"
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs font-medium"
                  onClick={() => setShowPassword(p => !p)}
                >
                  {showPassword ? "Hide" : "Show"}
                </button>
              </div>
            </div>

            {error && (
              <div className="rounded-lg bg-red-50 border border-red-100 px-4 py-3">
                <p className="text-sm text-red-600">{error}</p>
              </div>
            )}

            <Button
              type="submit"
              className="w-full h-11 text-sm font-semibold rounded-lg text-white transition-all"
              style={{ backgroundColor: "#2B3588" }}
              disabled={isLoading || !email || !password}
              onMouseEnter={e => (e.currentTarget.style.backgroundColor = "#232c70")}
              onMouseLeave={e => (e.currentTarget.style.backgroundColor = "#2B3588")}
            >
              {isLoading
                ? <><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Signing in...</>
                : "Sign In"
              }
            </Button>
          </form>
        </div>
      </div>
    </div>
  );
}