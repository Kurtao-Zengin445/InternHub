# Task: Tambah Fitur Penilaian Siswa Magang (Role School)

## Plan Breakdown & Progress

### ✅ [DONE] Planning
- No existing school evaluation
- School/students/index.blade.php has students table with internships
- Create school_evaluations table/model
- Controller/routes/views

### ⏳ 1. Create Migration school_evaluations
- `php artisan make:migration create_school_evaluations_table`
- Fields: internship_id, school_id, score (0-100), comments, evaluated_at
- FK to internships/schools

### ⏳ 2. Create Model SchoolEvaluation
- app/Models/SchoolEvaluation.php
- Relations: internship, school

### ⏳ 3. Create Controller School/EvaluationController
- index(): list active internships for school
- create($internship)
- store(Request $request, $internship)
- Link from students index

### ⏳ 4. Add Routes
- routes/web.php: school.evaluations

### ⏳ 5. Create Views
- resources/views/school/evaluations/index.blade.php (list)
- resources/views/school/evaluations/form.blade.php

### ⏳ 6. Update school/students/index.blade.php
- Add "Penilaian" column/button for students with internship

### ⏳ 7. Update Internship model
- Add relation hasOne SchoolEvaluation

### ⏳ 8. Test & migrate
- php artisan migrate
- php artisan serve, test school role

**Next: Create migration → execute `php artisan make:migration create_school_evaluations_table`**
