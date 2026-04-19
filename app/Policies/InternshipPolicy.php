<?php

namespace App\Policies;

use App\Models\Internship;
use App\Models\User;

class InternshipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSupervisor() || $user->isCompany();
    }

    public function view(User $user, Internship $internship): bool
    {
        return $user->isAdmin()
            || ($user->isStudent() && $user->id === $internship->student_id)
            || ($user->isSupervisor() && $user->supervisor?->id === $internship->supervisor_id)
            || ($user->isCompany() && $user->company?->id === $internship->application?->program?->company_id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Internship $internship): bool
    {
        return $user->isAdmin()
            || ($user->isSupervisor() && $user->supervisor?->id === $internship->supervisor_id)
            || ($user->isCompany() && $user->company?->id === $internship->application?->program?->company_id);
    }

    public function delete(User $user, Internship $internship): bool
    {
        return $user->isAdmin();
    }

    public function evaluate(User $user, Internship $internship): bool
    {
        return ($user->isSupervisor() && $user->supervisor?->id === $internship->supervisor_id)
            || ($user->isCompany() && $user->company?->id === $internship->application?->program?->company_id);
    }

    public function viewAttendance(User $user, Internship $internship): bool
    {
        return $this->view($user, $internship);
    }

    public function viewReports(User $user, Internship $internship): bool
    {
        return $this->view($user, $internship);
    }
}
