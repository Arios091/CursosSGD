<?php

namespace Tests\Feature;

use App\Models\PendingUser;
use App\Models\User;
use App\Notifications\VerifyPendingEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_usuario_puede_registrarse()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'primer_nombre' => 'Juan',
            'primer_apellido' => 'Perez',
            'segundo_apellido' => 'Lopez',
            'email' => 'juan.perez@unas.edu.pe',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertRedirect(route('pending-verification.notice'));

        $this->assertDatabaseHas('pending_users', [
            'email' => 'juan.perez@unas.edu.pe',
            'role' => 'docente',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'juan.perez@unas.edu.pe',
        ]);
    }

    public function test_registro_envia_correo_de_verificacion()
    {
        Notification::fake();

        $this->post('/register', [
            'primer_nombre' => 'Maria',
            'primer_apellido' => 'Garcia',
            'segundo_apellido' => 'Torres',
            'email' => 'maria.garcia@unas.edu.pe',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $pendingUser = PendingUser::where('email', 'maria.garcia@unas.edu.pe')->first();

        Notification::assertSentTo($pendingUser, VerifyPendingEmail::class);
    }

    public function test_usuario_sin_verificar_no_puede_iniciar_sesion()
    {
        $user = User::create([
            'name' => 'Test User',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'User',
            'email' => 'unverified@test.com',
            'password' => bcrypt('Password1'),
            'role' => 'estudiante',
            'email_verified_at' => null,
        ]);

        $response = $this->post('/login', [
            'email' => 'unverified@test.com',
            'password' => 'Password1',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertGuest();
    }

    public function test_usuario_verificado_puede_iniciar_sesion()
    {
        $user = User::create([
            'name' => 'Verified User',
            'primer_nombre' => 'Verified',
            'primer_apellido' => 'User',
            'email' => 'verified_login@test.com',
            'password' => bcrypt('Password1'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $this->assertTrue($user->hasVerifiedEmail());

        $response = $this->post('/login', [
            'email' => 'verified_login@test.com',
            'password' => 'Password1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertAuthenticated('web');
    }

    public function test_usuario_puede_verificar_email()
    {
        $user = User::create([
            'name' => 'Verify Me',
            'primer_nombre' => 'Verify',
            'primer_apellido' => 'Me',
            'email' => 'verify@test.com',
            'password' => bcrypt('Password1'),
            'role' => 'estudiante',
            'email_verified_at' => null,
        ]);

        $this->assertNull($user->email_verified_at);

        $verificationUrl = $this->app->make('url')->temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('home'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_registro_rechaza_email_no_institucional()
    {
        $response = $this->post('/register', [
            'primer_nombre' => 'Fake',
            'primer_apellido' => 'User',
            'segundo_apellido' => 'Test',
            'email' => 'fake@gmail.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_token_verificacion_crea_usuario()
    {
        $pendingUser = PendingUser::create([
            'name' => 'Token Test',
            'primer_nombre' => 'Token',
            'primer_apellido' => 'Test',
            'segundo_apellido' => 'User',
            'email' => 'token.test@unas.edu.pe',
            'password' => bcrypt('Password1'),
            'role' => 'docente',
            'token' => 'test-token-12345',
            'expires_at' => now()->addHours(1),
        ]);

        $response = $this->get(route('pending-verification.verify', 'test-token-12345'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'token.test@unas.edu.pe',
        ]);

        $user = User::where('email', 'token.test@unas.edu.pe')->first();
        $this->assertNotNull($user->email_verified_at);

        $this->assertDatabaseMissing('pending_users', [
            'email' => 'token.test@unas.edu.pe',
        ]);
    }

    public function test_token_expirado_rechaza()
    {
        $pendingUser = PendingUser::create([
            'name' => 'Expired',
            'primer_nombre' => 'Expired',
            'primer_apellido' => 'User',
            'segundo_apellido' => 'Test',
            'email' => 'expired@unas.edu.pe',
            'password' => bcrypt('Password1'),
            'role' => 'docente',
            'token' => 'expired-token',
            'expires_at' => now()->subHour(),
        ]);

        $response = $this->get(route('pending-verification.verify', 'expired-token'));

        $response->assertRedirect(route('register'));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('users', [
            'email' => 'expired@unas.edu.pe',
        ]);
    }

    public function test_login_fallido_con_credenciales_incorrectas()
    {
        $user = User::create([
            'name' => 'Test',
            'primer_nombre' => 'Test',
            'primer_apellido' => 'User',
            'email' => 'test@test.com',
            'password' => bcrypt('CorrectPass1'),
            'role' => 'estudiante',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@test.com',
            'password' => 'WrongPass1',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
