<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    // ── Job Postings ────────────────────────────────────────
    public function index(Request $request)
    {
        $query = JobPosting::with('department')
            ->withCount('applicants')
            ->orderByDesc('posting_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $postings    = $query->paginate(12)->withQueryString();
        $departments = Department::orderBy('name')->get();

        $openCount   = JobPosting::where('status', 'open')->count();
        $closedCount = JobPosting::where('status', 'closed')->count();
        $filledCount = JobPosting::where('status', 'filled')->count();
        $totalApplicants = Applicant::count();

        return view('recruitment.index', compact(
            'postings', 'departments',
            'openCount', 'closedCount', 'filledCount', 'totalApplicants'
        ));
    }

    public function storePosting(Request $request)
    {
        $validated = $request->validate([
            'job_title'     => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'slots'         => 'required|integer|min:1',
            'posting_date'  => 'required|date',
            'deadline'      => 'nullable|date|after_or_equal:posting_date',
            'description'   => 'nullable|string|max:1000',
        ]);

        JobPosting::create($validated + ['status' => 'open']);

        return back()->with('success', "Job posting for \"{$validated['job_title']}\" created.");
    }

    public function updatePosting(Request $request, JobPosting $posting)
    {
        $validated = $request->validate([
            'job_title'     => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
            'slots'         => 'required|integer|min:1',
            'posting_date'  => 'required|date',
            'deadline'      => 'nullable|date',
            'status'        => 'required|in:open,closed,filled',
            'description'   => 'nullable|string|max:1000',
        ]);

        $posting->update($validated);
        return back()->with('success', "Job posting updated.");
    }

    public function destroyPosting(JobPosting $posting)
    {
        $posting->delete();
        return back()->with('success', "Job posting deleted.");
    }

    // ── Applicants ──────────────────────────────────────────
    public function applicants(Request $request)
    {
        $query = Applicant::with('jobPosting')
            ->orderByDesc('applied_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        $applicants  = $query->paginate(15)->withQueryString();
        $postings    = JobPosting::where('status', 'open')->orderBy('job_title')->get();

        $pendingCount     = Applicant::where('status', 'pending')->count();
        $shortlistedCount = Applicant::where('status', 'shortlisted')->count();
        $hiredCount       = Applicant::where('status', 'hired')->count();
        $rejectedCount    = Applicant::where('status', 'rejected')->count();

        return view('recruitment.applicants', compact(
            'applicants', 'postings',
            'pendingCount', 'shortlistedCount', 'hiredCount', 'rejectedCount'
        ));
    }

    public function storeApplicant(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'email'            => 'nullable|email|max:150',
            'phone'            => 'nullable|string|max:30',
            'applied_position' => 'required|string|max:100',
            'job_posting_id'   => 'nullable|exists:job_postings,id',
            'applied_date'     => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        Applicant::create($validated + ['status' => 'pending']);
        return back()->with('success', "Applicant \"{$validated['name']}\" added.");
    }

    public function updateApplicantStatus(Request $request, Applicant $applicant)
    {
        $request->validate([
            'status' => 'required|in:pending,shortlisted,hired,rejected',
        ]);

        $applicant->update(['status' => $request->status]);

        $labels = [
            'shortlisted' => 'shortlisted',
            'hired'       => 'marked as hired',
            'rejected'    => 'rejected',
            'pending'     => 'moved back to pending',
        ];

        return back()->with('success', "{$applicant->name} has been {$labels[$request->status]}.");
    }

    public function destroyApplicant(Applicant $applicant)
    {
        $name = $applicant->name;
        $applicant->delete();
        return back()->with('success', "Applicant {$name} removed.");
    }

    // ── Interviews ──────────────────────────────────────────
    public function interviews(Request $request)
    {
        $query = Interview::with('applicant', 'interviewer')
            ->orderBy('schedule_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $interviews  = $query->paginate(15)->withQueryString();
        $applicants  = Applicant::whereIn('status', ['pending', 'shortlisted'])->orderBy('name')->get();
        $interviewers = User::orderBy('name')->get();

        $scheduledCount = Interview::where('status', 'scheduled')->count();
        $doneCount      = Interview::where('status', 'done')->count();
        $cancelledCount = Interview::where('status', 'cancelled')->count();

        return view('recruitment.interviews', compact(
            'interviews', 'applicants', 'interviewers',
            'scheduledCount', 'doneCount', 'cancelledCount'
        ));
    }

    public function storeInterview(Request $request)
    {
        $validated = $request->validate([
            'applicant_id'   => 'required|exists:applicants,id',
            'interviewer_id' => 'required|exists:users,id',
            'schedule_date'  => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        Interview::create($validated + ['status' => 'scheduled']);

        // Auto-shortlist applicant
        Applicant::find($validated['applicant_id'])->update(['status' => 'shortlisted']);

        return back()->with('success', 'Interview scheduled successfully.');
    }

    public function updateInterviewStatus(Request $request, Interview $interview)
    {
        $request->validate(['status' => 'required|in:scheduled,done,cancelled']);
        $interview->update(['status' => $request->status]);
        return back()->with('success', "Interview marked as {$request->status}.");
    }
}