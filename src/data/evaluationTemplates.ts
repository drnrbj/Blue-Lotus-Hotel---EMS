// src/data/evaluationTemplates.ts

export interface EvaluationTemplate {
  title: string;
  description: string;
  sections: {
    type: "likert" | "open_ended";
    title: string;
    description?: string;
    questions: string[];
  }[];
}

export const EVALUATION_TEMPLATES: Record<string, EvaluationTemplate> = {
  "Front Office": {
    title: "Front Office Performance Evaluation",
    description: "Evaluates guest service, communication, and front desk operations",
    sections: [
      {
        type: "likert",
        title: "Guest Service & Communication",
        questions: [
          "Greets guests warmly and professionally",
          "Handles check-in/check-out efficiently",
          "Responds to guest inquiries promptly",
        ],
      },
      {
        type: "open_ended",
        title: "Feedback",
        questions: ["What are the employee's strengths?", "Areas for improvement:"],
      },
    ],
  },
  // Add more departments as needed
};

export function getTemplateForDepartment(department: string): EvaluationTemplate | null {
  return EVALUATION_TEMPLATES[department] || null;
}