// ─────────────────────────────────────────────────────────────────────────────
// PATCH for src/components/evaluation/EvaluationFormBuilder.tsx
//
// 1. Add this import at the top of EvaluationFormBuilder.tsx:
//
//   import { getTemplateForDepartment, EVALUATION_TEMPLATES } from "@/data/evaluationTemplates";
//   import { Sparkles } from "lucide-react"; // add to existing lucide import
//
// 2. Add this state inside the component (near the other useState calls):
//
//   const [showTemplateConfirm, setShowTemplateConfirm] = useState(false);
//
// 3. Add this handler inside the component:
//
//   const applyTemplate = () => {
//     const template = getTemplateForDepartment(department);
//     if (!template) return;
//
//     // Separate likert and open-ended sections
//     const likertSections = template.sections.filter(s => s.type === "likert");
//     const openSection    = template.sections.find(s => s.type === "open_ended");
//
//     setSections(likertSections.length > 0
//       ? likertSections
//       : [{ title:"", description:"", type:"likert", likert_options:[...DEFAULT_LIKERT], questions:[], order:0 }]
//     );
//     setOpenQs(openSection?.questions ?? []);
//     if (!title.trim()) setTitle(template.title);
//     setShowTemplateConfirm(false);
//     toast({ title: "Template applied!", description: `Loaded ${template.sections.length} sections for ${department}.`, variant: "success" });
//   };
//
// 4. Replace the Department <Select> block in the form with this version
//    (adds the "Use Template" button beneath it when a template exists):
//
// ─────────────────────────────────────────────────────────────────────────────

// ─── DROP-IN REPLACEMENT for the Department <div> inside EvaluationFormBuilder ───
//
// Find this in your JSX:
//
//   <div ref={departmentRef}>
//     <label className="text-sm font-medium">
//       Department <span className="text-red-500">*</span>
//     </label>
//     <Select value={department} onValueChange={setDepartment}>
//       ...
//     </Select>
//     {errors.department && ( ... )}
//   </div>
//
// Replace the entire <div ref={departmentRef}> block with:

/*
<div ref={departmentRef}>
  <label className="text-sm font-medium">
    Department <span className="text-red-500">*</span>
  </label>
  <Select
    value={department}
    onValueChange={v => {
      setDepartment(v);
      // Clear template confirm if department changes
      setShowTemplateConfirm(false);
    }}
  >
    <SelectTrigger className={cn("mt-1", errors.department && "border-red-400 bg-red-50 focus:ring-red-400")}>
      <SelectValue placeholder="Select department" />
    </SelectTrigger>
    <SelectContent>
      {DEPARTMENTS.map(d => <SelectItem key={d} value={d}>{d}</SelectItem>)}
    </SelectContent>
  </Select>

  {errors.department && (
    <p className="mt-1 text-xs text-red-500 flex items-center gap-1">
      <AlertTriangle className="h-3 w-3" /> {errors.department}
    </p>
  )}

  // ── Template suggestion banner ──────────────────────────────────────────
  {department && getTemplateForDepartment(department) && (
    <div className="mt-2">
      {!showTemplateConfirm ? (
        <button
          type="button"
          onClick={() => setShowTemplateConfirm(true)}
          className="flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-medium transition-colors"
        >
          <Sparkles className="h-3.5 w-3.5" />
          Use {department} evaluation template
        </button>
      ) : (
        <div className="rounded-lg border border-blue-200 bg-blue-50 p-3 space-y-2">
          <p className="text-xs font-medium text-blue-800">
            Apply the {department} template?
          </p>
          <p className="text-xs text-blue-700">
            {getTemplateForDepartment(department)?.description}
          </p>
          <p className="text-xs text-blue-600">
            This will replace your current sections and questions.
          </p>
          <div className="flex gap-2">
            <button
              type="button"
              onClick={applyTemplate}
              className="rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors"
            >
              Yes, apply template
            </button>
            <button
              type="button"
              onClick={() => setShowTemplateConfirm(false)}
              className="rounded-md border border-blue-300 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors"
            >
              Cancel
            </button>
          </div>
        </div>
      )}
    </div>
  )}
</div>
*/

// ─────────────────────────────────────────────────────────────────────────────
// FULL UPDATED EvaluationFormBuilder.tsx
// Copy this entire file to replace src/components/evaluation/EvaluationFormBuilder.tsx
// ─────────────────────────────────────────────────────────────────────────────

export const PATCH_INSTRUCTIONS = `
Steps to integrate:
1. Copy evaluationTemplates.ts → src/data/evaluationTemplates.ts
2. Replace EvaluationFormBuilder.tsx with the updated version below
`;