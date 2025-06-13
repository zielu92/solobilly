<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function () {
    Permission::create(['name' => 'UPDATE_ADMIN_USERS', 'guard_name' => 'filament']);
    Permission::create(['name' => 'UPDATE_PERMISSIONS', 'guard_name' => 'filament']);

    $this->superAdminRole = Role::create(['name' => 'SUPER_ADMIN', 'guard_name' => 'filament']);
    $this->adminRole = Role::create(['name' => 'ADMIN', 'guard_name' => 'filament']);
    $this->userRole = Role::create(['name' => 'USER', 'guard_name' => 'filament']);

    $this->superAdminRole->givePermissionTo(['UPDATE_ADMIN_USERS', 'UPDATE_PERMISSIONS']);
});

describe('User Factory', function () {
    it('creates a user with default attributes', function () {
        $user = User::factory()->create();

        expect($user)
            ->first_name->not->toBeEmpty()
            ->last_name->not->toBeEmpty()
            ->email->toContain('@')
            ->password->not->toBeEmpty()
            ->expires_at->toBeNull()
            ->two_factor_code->toBeNull()
            ->two_factor_expires_at->toBeNull()
            ->and(strlen($user->remember_token))->toBe(60)
            ->and(Hash::check('password', $user->password))->toBeTrue();

    });

    it('handles email verification correctly', function () {
        $user = User::factory()->create();
        expect($user->email_verified_at)
                ->not->toBeNull()
                ->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('creates multiple unique users', function () {
        $users = User::factory()->count(3)->create();

        expect($users)->toHaveCount(3);

        $emails = $users->pluck('email')->toArray();
        expect($emails)->toEqual(array_unique($emails));
    });

    it('creates user with custom attributes', function () {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com'
        ]);

        expect($user)
            ->first_name->toBe('John')
            ->last_name->toBe('Doe')
            ->email->toBe('john@example.com');
    });
});

describe('User Factory States', function () {
    it('creates unverified user', function () {
        $user = User::factory()->unverified()->create();

        expect($user->email_verified_at)->toBeNull();
    });

    it('creates expired user', function () {
        $user = User::factory()->expired()->create();

        expect($user->expires_at)
            ->not->toBeNull()
            ->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and($user->expires_at->isPast())->toBeTrue();

    });

    it('creates unexpired user', function () {
        $user = User::factory()->unexpired()->create();

        expect($user->expires_at)
            ->not->toBeNull()
            ->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and($user->expires_at->isFuture())->toBeTrue()
            ->and($user->expires_at->diffInDays(now()))->toBeLessThanOrEqual(30);

    });

    it('creates user with two factor authentication', function () {
        $user = User::factory()->withTwoFactor()->create();

        expect($user)
            ->two_factor_code->not->toBeNull()
            ->two_factor_expires_at->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and(strlen($user->two_factor_code))->toBe(6)
            ->and($user->two_factor_code)->toMatch('/^\d{6}$/')
            ->and($user->two_factor_expires_at->isFuture())->toBeTrue()
            ->and($user->two_factor_expires_at->diffInMinutes(now()))->toBeLessThanOrEqual(5);

    });

    it('creates user with expired two factor authentication', function () {
        $user = User::factory()->withExpiredTwoFactor()->create();

        expect($user)
            ->two_factor_code->not->toBeNull()
            ->two_factor_expires_at->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and(strlen($user->two_factor_code))->toBe(6)
            ->and($user->two_factor_code)->toMatch('/^\d{6}$/')
            ->and($user->two_factor_expires_at->isPast())->toBeTrue();

    });
});

describe('User Authentication', function () {
    it('verifies user password', function () {
        $user = User::factory()->create();

        expect(Hash::check('password', $user->password))->toBeTrue()
            ->and(Hash::check('wrong-password', $user->password))->toBeFalse();
    });

    it('checks if user is verified', function () {
        $verifiedUser = User::factory()->create();
        $unverifiedUser = User::factory()->unverified()->create();

        expect($unverifiedUser->email_verified_at)->toBeNull()
            ->and($verifiedUser->email_verified_at)->not->toBeNull();
    });

    it('checks if user account is expired', function () {
        $activeUser = User::factory()->create();
        $expiredUser = User::factory()->expired()->create();
        $unexpiredUser = User::factory()->unexpired()->create();

        expect($activeUser->expires_at)->toBeNull()
            ->and($expiredUser->expires_at->isPast())->toBeTrue()
            ->and($unexpiredUser->expires_at->isFuture())->toBeTrue();
    });

    it('validates two factor authentication code timing', function () {
        $userWithValidCode = User::factory()->withTwoFactor()->create();
        $userWithExpiredCode = User::factory()->withExpiredTwoFactor()->create();

        expect($userWithValidCode->two_factor_expires_at->isFuture())->toBeTrue()
            ->and($userWithExpiredCode->two_factor_expires_at->isPast())->toBeTrue();
    });
});

describe('User Roles and Permissions', function () {
    it('assigns super admin role to user', function () {
        $user = User::factory()->create();
        $user->assignRole($this->superAdminRole);

        expect($user->hasRole('SUPER_ADMIN'))->toBeTrue()
            ->and($user->hasPermissionTo('UPDATE_ADMIN_USERS'))->toBeTrue()
            ->and($user->hasPermissionTo('UPDATE_PERMISSIONS'))->toBeTrue();
    });

    it('assigns multiple roles to user', function () {
        $user = User::factory()->create();
        $user->assignRole([$this->adminRole, $this->userRole]);

        expect($user->hasRole(['ADMIN', 'USER']))->toBeTrue()
            ->and($user->roles)->toHaveCount(2);
    });

    it('checks user permissions through roles', function () {
        $superAdmin = User::factory()->create();
        $regularUser = User::factory()->create();

        $superAdmin->assignRole($this->superAdminRole);
        $regularUser->assignRole($this->userRole);

        expect($superAdmin->hasPermissionTo('UPDATE_ADMIN_USERS'))->toBeTrue()
            ->and($superAdmin->hasPermissionTo('UPDATE_PERMISSIONS'))->toBeTrue()
            ->and($regularUser->hasPermissionTo('UPDATE_ADMIN_USERS'))->toBeFalse()
            ->and($regularUser->hasPermissionTo('UPDATE_PERMISSIONS'))->toBeFalse();
    });

    it('removes role from user', function () {
        $user = User::factory()->create();
        $user->assignRole($this->adminRole);

        expect($user->hasRole('ADMIN'))->toBeTrue();

        $user->removeRole($this->adminRole);

        expect($user->hasRole('ADMIN'))->toBeFalse();
    });

    it('syncs user roles', function () {
        $user = User::factory()->create();
        $user->assignRole([$this->adminRole, $this->userRole]);

        expect($user->roles)->toHaveCount(2);

        $user->syncRoles([$this->superAdminRole]);

        expect($user->roles)->toHaveCount(1)
            ->and($user->hasRole('SUPER_ADMIN'))->toBeTrue()
            ->and($user->hasRole('ADMIN'))->toBeFalse()
            ->and($user->hasRole('USER'))->toBeFalse();
    });
});

describe('User Model Relationships', function () {
    it('has many roles relationship', function () {
        $user = User::factory()->create();
        $user->assignRole([$this->adminRole, $this->userRole]);

        expect($user->roles()->count())->toBe(2)
            ->and($user->roles->pluck('name')->toArray())->toContain('ADMIN', 'USER');
    });

    it('has many permissions through roles', function () {
        $user = User::factory()->create();
        $user->assignRole($this->superAdminRole);

        $permissions = $user->getPermissionsViaRoles();

        expect($permissions)->toHaveCount(2)
            ->and($permissions->pluck('name')->toArray())
            ->toContain('UPDATE_ADMIN_USERS', 'UPDATE_PERMISSIONS');
    });
});

describe('User Factory with Roles', function () {
    it('creates user with super admin role', function () {
        $user = User::factory()->create();
        $user->assignRole('SUPER_ADMIN');

        expect($user->hasRole('SUPER_ADMIN'))->toBeTrue()
            ->and($user->can('UPDATE_ADMIN_USERS'))->toBeTrue()
            ->and($user->can('UPDATE_PERMISSIONS'))->toBeTrue();
    });

    it('creates multiple users with different roles', function () {
        $superAdmin = User::factory()->create();
        $admin = User::factory()->create();
        $regularUser = User::factory()->create();

        $superAdmin->assignRole('SUPER_ADMIN');
        $admin->assignRole('ADMIN');
        $regularUser->assignRole('USER');

        expect($superAdmin->hasRole('SUPER_ADMIN'))->toBeTrue()
            ->and($admin->hasRole('ADMIN'))->toBeTrue()
            ->and($regularUser->hasRole('USER'))->toBeTrue()
            ->and(User::role('SUPER_ADMIN')->count())->toBe(1)
            ->and(User::role('ADMIN')->count())->toBe(1)
            ->and(User::role('USER')->count())->toBe(1);

    });
});

describe('Complex User Scenarios', function () {
    it('creates expired user with two factor authentication', function () {
        $user = User::factory()
            ->expired()
            ->withTwoFactor()
            ->create();

        expect($user->expires_at->isPast())->toBeTrue()
            ->and($user->two_factor_code)->not->toBeNull()
            ->and($user->two_factor_expires_at->isFuture())->toBeTrue();
    });

    it('creates unverified user with expired two factor', function () {
        $user = User::factory()
            ->unverified()
            ->withExpiredTwoFactor()
            ->create();

        expect($user->email_verified_at)->toBeNull()
            ->and($user->two_factor_code)->not->toBeNull()
            ->and($user->two_factor_expires_at->isPast())->toBeTrue();
    });

    it('creates super admin with all states combined', function () {
        $user = User::factory()
            ->unexpired()
            ->withTwoFactor()
            ->create();

        $user->assignRole('SUPER_ADMIN');

        expect($user)
            ->email_verified_at->not->toBeNull()
            ->two_factor_code->not->toBeNull()
            ->and($user->expires_at->isFuture())->toBeTrue()
            ->and($user->two_factor_expires_at->isFuture())->toBeTrue()
            ->and($user->hasRole('SUPER_ADMIN'))->toBeTrue()
            ->and($user->can('UPDATE_ADMIN_USERS'))->toBeTrue()
            ->and($user->can('UPDATE_PERMISSIONS'))->toBeTrue();

    });
});

describe('User Database Constraints', function () {
    it('enforces unique email constraint', function () {
        $email = 'test@example.com';

        User::factory()->create(['email' => $email]);

        expect(fn() => User::factory()->create(['email' => $email]))
            ->toThrow(\Illuminate\Database\QueryException::class);
    });

    it('prevents same email for soft deleted users', function () {
        $email = 'test@example.com';

        $user1 = User::factory()->create(['email' => $email]);

        if (method_exists($user1, 'delete')) {
            $user1->delete();

            expect(fn() => User::factory()->create(['email' => $email]))
                ->toThrow(\Illuminate\Database\QueryException::class);
        }
    })->skip(!method_exists(User::class, 'delete'), 'Soft deletes not implemented');

    it('can restore soft deleted user', function () {
        $email = 'test@example.com';

        $user = User::factory()->create(['email' => $email]);

        if (method_exists($user, 'delete') && method_exists($user, 'restore')) {
            $originalId = $user->id;

            // Soft delete the user
            $user->delete();
            expect(User::find($originalId))->toBeNull()
                ->and(User::withTrashed()->find($originalId))->not->toBeNull();

            // Restore the user
            $user->restore();
            $restoredUser = User::find($originalId);

            expect($restoredUser)->not->toBeNull()
                ->and($restoredUser->email)->toBe($email)
                ->and($restoredUser->deleted_at)->toBeNull();
        }
    })->skip(!method_exists(User::class, 'delete') || !method_exists(User::class, 'restore'), 'Soft deletes not implemented');
});
