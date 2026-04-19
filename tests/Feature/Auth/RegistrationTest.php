<?php

use App\Models\School;
use App\Models\User;

function createSchoolForRegistration(): School
{
    $schoolUser = User::factory()->create([
        'role' => 'school',
    ]);

    return School::create([
        'user_id' => $schoolUser->id,
        'name' => 'SMK Test InternHub',
        'address' => 'Jl. Uji Coba No. 1',
        'email' => 'school-test@example.com',
    ]);
}

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new student users can register', function () {
    $school = createSchoolForRegistration();

    $response = $this->post('/register', [
        'role' => 'student',
        'name' => 'Test User',
        'email' => 'test@example.com',
        'school_id' => $school->id,
        'nis' => '1234567890',
        'class' => 'XII RPL 1',
        'major' => 'Rekayasa Perangkat Lunak',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->role)->toBe('student');
    expect($user->student)->not->toBeNull();
    expect($user->student->school_id)->toBe($school->id);
});
