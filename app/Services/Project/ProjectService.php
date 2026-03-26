<?php

namespace App\Services\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ProjectService
{
    /**
     * إنشاء مشروع جديد
     */
    public function createProject(User $employer, array $data): Project
    {
        DB::beginTransaction();
        
        try {
            // التحقق من أن المستخدم صاحب عمل
            if (!$employer->isEmployer() && !$employer->isAdmin()) {
                throw new Exception('Only employers can create projects');
            }

            // التحقق من حالة المستخدم
            if (!$employer->is_active || $employer->is_banned) {
                throw new Exception('User account is not active');
            }

            // إنشاء المشروع
            $project = Project::create([
                'employer_id' => $employer->id,
                'title' => $data['title'],
                'slug' => $this->generateUniqueSlug($data['title']),
                'description' => $data['description'],
                'requirements' => $data['requirements'] ?? null,
                'budget_min' => $data['budget_min'],
                'budget_max' => $data['budget_max'] ?? null,
                'budget_type' => $data['budget_type'] ?? 'fixed',
                'currency' => $data['currency'] ?? 'SAR',
                'duration_days' => $data['duration_days'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'status' => 'draft',
                'is_featured' => $data['is_featured'] ?? false,
                'is_urgent' => $data['is_urgent'] ?? false,
                'is_hidden' => $data['is_hidden'] ?? false,
                'published_at' => null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            // ربط المهارات إذا وجدت
            if (isset($data['skills']) && is_array($data['skills'])) {
                $project->skills()->sync($data['skills']);
            }

            DB::commit();

            Log::info('Project created', [
                'project_id' => $project->id,
                'employer_id' => $employer->id,
                'title' => $project->title,
            ]);

            return $project;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * نشر مشروع
     */
    public function publishProject(Project $project): Project
    {
        DB::beginTransaction();
        
        try {
            if ($project->status !== 'draft') {
                throw new Exception('Only draft projects can be published');
            }

            $project->update([
                'status' => 'open',
                'published_at' => now(),
            ]);

            DB::commit();

            Log::info('Project published', [
                'project_id' => $project->id,
                'published_at' => $project->published_at,
            ]);

            return $project;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * إلغاء مشروع
     */
    public function cancelProject(Project $project, User $user, string $reason): Project
    {
        DB::beginTransaction();
        
        try {
            // التحقق من الصلاحية
            if ($user->id !== $project->employer_id && !$user->isAdmin()) {
                throw new Exception('Unauthorized');
            }

            // لا يمكن إلغاء مشروع به عقد نشط
            if ($project->contract && $project->contract->isActive()) {
                throw new Exception('Cannot cancel project with active contract');
            }

            $project->update([
                'status' => 'cancelled',
            ]);

            DB::commit();

            Log::info('Project cancelled', [
                'project_id' => $project->id,
                'user_id' => $user->id,
                'reason' => $reason,
            ]);

            return $project;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * توليد Slug فريد
     */
    protected function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Project::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * الحصول على مشاريع المستخدم
     */
    public function getUserProjects(User $user, string $type = 'all'): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Project::where('employer_id', $user->id);

        if ($type === 'open') {
            $query->where('status', 'open');
        } elseif ($type === 'completed') {
            $query->where('status', 'completed');
        } elseif ($type === 'cancelled') {
            $query->where('status', 'cancelled');
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }
}