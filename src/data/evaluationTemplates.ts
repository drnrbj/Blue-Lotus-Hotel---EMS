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
  "Human Resources": {
    title: "Human Resources Performance Evaluation",
    description: "Evaluates recruitment, employee relations, compliance, and HR operations",
    sections: [
      {
        type: "likert",
        title: "Recruitment & Talent Acquisition",
        description: "Effectiveness in hiring and onboarding",
        questions: [
          "Sources and attracts qualified candidates effectively",
          "Conducts thorough and fair interviews",
          "Completes background checks and reference verification",
          "Manages job postings and recruitment channels",
          "Ensures smooth onboarding process for new hires",
        ],
      },
      {
        type: "likert",
        title: "Employee Relations",
        description: "Handling employee concerns and company culture",
        questions: [
          "Addresses employee concerns promptly and professionally",
          "Maintains confidentiality of employee matters",
          "Promotes positive workplace culture",
          "Handles disciplinary actions fairly",
          "Organizes employee engagement activities",
        ],
      },
      {
        type: "likert",
        title: "Compliance & Documentation",
        description: "Legal compliance and record keeping",
        questions: [
          "Maintains accurate employee records",
          "Ensures compliance with labor laws",
          "Processes government-mandated benefits correctly",
          "Updates HR policies as needed",
          "Prepares reports and documentation on time",
        ],
      },
      {
        type: "open_ended",
        title: "HR Performance Review",
        questions: [
          "What are the HR team member's key strengths?",
          "What areas need improvement?",
          "Additional comments or recommendations:",
        ],
      },
    ],
  },

  "Finance": {
    title: "Finance Performance Evaluation",
    description: "Evaluates financial reporting, budgeting, and compliance",
    sections: [
      {
        type: "likert",
        title: "Financial Reporting & Accuracy",
        questions: [
          "Prepares accurate financial statements",
          "Reconciles accounts correctly and timely",
          "Identifies and corrects discrepancies promptly",
          "Maintains organized financial records",
          "Meets reporting deadlines consistently",
        ],
      },
      {
        type: "likert",
        title: "Budgeting & Cost Control",
        questions: [
          "Assists in budget preparation effectively",
          "Monitors budget vs actual variances",
          "Identifies cost-saving opportunities",
          "Controls expenses within approved budgets",
          "Provides accurate financial forecasts",
        ],
      },
      {
        type: "open_ended",
        title: "Finance Performance Review",
        questions: [
          "Financial strengths demonstrated:",
          "Areas for development:",
          "Overall assessment:",
        ],
      },
    ],
  },

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
          "Responds to guest inquiries promptly and accurately",
          "Maintains professional phone and email etiquette",
          "Handles guest complaints effectively and calmly",
        ],
      },
      {
        type: "likert",
        title: "Operational Efficiency",
        questions: [
          "Accurate with reservation systems and data entry",
          "Manages cash and payment transactions correctly",
          "Follows proper check-in/check-out procedures",
          "Coordinates with housekeeping and other departments",
          "Maintains organized and clean front desk area",
        ],
      },
      {
        type: "likert",
        title: "Problem Solving",
        questions: [
          "Handles difficult guests professionally",
          "Resolves issues without unnecessary escalation",
          "Thinks quickly to find solutions",
          "Knows when to involve supervisor",
          "Follows up on guest concerns appropriately",
        ],
      },
      {
        type: "open_ended",
        title: "Front Office Performance Review",
        questions: [
          "What are the employee's customer service strengths?",
          "What operational areas need improvement?",
          "Any additional feedback or observations:",
        ],
      },
    ],
  },

  "Food & Beverage": {
    title: "Food & Beverage Service Evaluation",
    description: "Evaluates service quality, food safety, and customer satisfaction",
    sections: [
      {
        type: "likert",
        title: "Service Quality",
        questions: [
          "Greets guests promptly and professionally",
          "Takes accurate orders and makes recommendations",
          "Serves food and beverages correctly and timely",
          "Handles special requests and dietary needs",
          "Maintains positive attitude under pressure",
        ],
      },
      {
        type: "likert",
        title: "Food Safety & Cleanliness",
        questions: [
          "Follows proper food handling procedures",
          "Maintains clean work and dining areas",
          "Follows safety and sanitation protocols",
          "Properly handles and stores food items",
          "Reports safety hazards immediately",
        ],
      },
      {
        type: "likert",
        title: "Teamwork & Efficiency",
        questions: [
          "Works well with kitchen and service team",
          "Anticipates guest needs proactively",
          "Manages multiple tables/sections effectively",
          "Assists colleagues when needed",
          "Completes side duties and closing tasks",
        ],
      },
      {
        type: "open_ended",
        title: "Food & Beverage Service Review",
        questions: [
          "What are the employee's service strengths?",
          "What areas need development?",
          "Additional observations or recommendations:",
        ],
      },
    ],
  },

  "Housekeeping": {
    title: "Housekeeping Performance Evaluation",
    description: "Evaluates cleanliness standards, efficiency, and attention to detail",
    sections: [
      {
        type: "likert",
        title: "Quality of Work",
        questions: [
          "Maintains high standards of room cleanliness",
          "Follows proper cleaning procedures and checklists",
          "Pays attention to detail in all tasks",
          "Properly handles cleaning equipment and chemicals",
          "Reports maintenance issues promptly",
        ],
      },
      {
        type: "likert",
        title: "Productivity & Time Management",
        questions: [
          "Completes assigned rooms within time standards",
          "Manages workload effectively",
          "Responds promptly to guest requests",
          "Minimizes waste of supplies",
          "Works efficiently without sacrificing quality",
        ],
      },
      {
        type: "open_ended",
        title: "Housekeeping Performance Review",
        questions: [
          "What does this employee do well?",
          "What could they improve?",
          "Additional comments:",
        ],
      },
    ],
  },

  "Rooms Division": {
    title: "Rooms Division Performance Evaluation",
    description: "Evaluates overall rooms operations including front office and housekeeping coordination",
    sections: [
      {
        type: "likert",
        title: "Rooms Operations Management",
        questions: [
          "Coordinates effectively between front office and housekeeping",
          "Manages room inventory and availability",
          "Handles room assignments and upgrades appropriately",
          "Monitors room status and cleanliness",
          "Optimizes room occupancy and revenue",
        ],
      },
      {
        type: "likert",
        title: "Guest Satisfaction",
        questions: [
          "Ensures guest room preferences are met",
          "Handles room-related complaints effectively",
          "Maintains high standards of room presentation",
          "Responds to guest requests promptly",
          "Follows up on room issues until resolved",
        ],
      },
      {
        type: "open_ended",
        title: "Rooms Division Review",
        questions: [
          "Rooms operations strengths:",
          "Areas for improvement:",
          "Overall assessment:",
        ],
      },
    ],
  },

  "Security": {
    title: "Security Performance Evaluation",
    description: "Evaluates vigilance, safety protocols, and emergency response",
    sections: [
      {
        type: "likert",
        title: "Security Operations",
        questions: [
          "Maintains vigilant observation at all times",
          "Follows security check procedures",
          "Patrols premises regularly and thoroughly",
          "Enforces access control properly",
          "Documents incidents accurately and completely",
        ],
      },
      {
        type: "likert",
        title: "Emergency Response",
        questions: [
          "Responds quickly to emergencies",
          "Follows emergency protocols correctly",
          "Remains calm under pressure",
          "Communicates clearly during incidents",
          "Coordinates effectively with emergency services",
        ],
      },
      {
        type: "open_ended",
        title: "Security Performance Review",
        questions: [
          "Security strengths demonstrated:",
          "Recommended improvements:",
          "Overall security performance assessment:",
        ],
      },
    ],
  },

  "Engineering": {
    title: "Engineering & Maintenance Performance Evaluation",
    description: "Evaluates technical skills, problem-solving, and safety compliance",
    sections: [
      {
        type: "likert",
        title: "Technical Competence",
        questions: [
          "Demonstrates strong technical knowledge",
          "Completes repairs correctly the first time",
          "Uses tools and equipment properly",
          "Follows maintenance schedules",
          "Documents work accurately",
        ],
      },
      {
        type: "likert",
        title: "Safety & Compliance",
        questions: [
          "Follows safety procedures at all times",
          "Uses personal protective equipment correctly",
          "Reports safety concerns immediately",
          "Maintains clean and organized work area",
          "Complies with all regulations and codes",
        ],
      },
      {
        type: "open_ended",
        title: "Engineering Performance Review",
        questions: [
          "What technical skills does the employee excel at?",
          "What training would be beneficial?",
          "Overall assessment:",
        ],
      },
    ],
  },

  "Sales & Marketing": {
    title: "Sales & Marketing Performance Evaluation",
    description: "Evaluates sales skills, marketing initiatives, and revenue generation",
    sections: [
      {
        type: "likert",
        title: "Sales Performance",
        questions: [
          "Achieves or exceeds sales targets",
          "Builds strong client relationships",
          "Identifies and pursues new opportunities",
          "Negotiates effectively",
          "Maintains accurate sales records and pipeline",
        ],
      },
      {
        type: "likert",
        title: "Marketing Initiatives",
        questions: [
          "Develops creative marketing campaigns",
          "Manages social media effectively",
          "Analyzes market trends successfully",
          "Creates compelling marketing materials",
          "Measures campaign ROI accurately",
        ],
      },
      {
        type: "open_ended",
        title: "Sales & Marketing Review",
        questions: [
          "Top sales achievements:",
          "Marketing successes:",
          "Areas for growth:",
        ],
      },
    ],
  },

  "All Departments": {
    title: "General Performance Evaluation",
    description: "Comprehensive evaluation applicable to all departments",
    sections: [
      {
        type: "likert",
        title: "Core Competencies",
        questions: [
          "Demonstrates reliability and punctuality",
          "Shows initiative and proactiveness",
          "Communicates effectively with colleagues",
          "Works well in a team environment",
          "Adapts to changing situations",
        ],
      },
      {
        type: "likert",
        title: "Work Quality & Productivity",
        questions: [
          "Produces high-quality work consistently",
          "Completes tasks on time",
          "Pays attention to detail",
          "Manages time effectively",
          "Follows instructions and procedures",
        ],
      },
      {
        type: "open_ended",
        title: "Overall Performance Review",
        questions: [
          "What are the employee's key strengths?",
          "What areas need improvement?",
          "Additional comments or recommendations:",
        ],
      },
    ],
  },
};

export function getTemplateForDepartment(department: string): EvaluationTemplate | null {
  return EVALUATION_TEMPLATES[department] || null;
}

export function getAvailableTemplates(): string[] {
  return Object.keys(EVALUATION_TEMPLATES);
}