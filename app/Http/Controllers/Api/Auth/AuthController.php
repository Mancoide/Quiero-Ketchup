<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class AuthController extends ApiController
{
    /**
     * @OA\Post(
     *   path="/api/auth/register",
     *   tags={"Auth"},
     *   summary="Registro de cliente",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         required={"name","email","password","password_confirmation"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="password", type="string"),
     *         @OA\Property(property="password_confirmation", type="string"),
     *         @OA\Property(
     *           property="addresses",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             required={"street","city"},
     *             @OA\Property(property="street", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string", nullable=true),
     *             @OA\Property(property="postal_code", type="string", nullable=true),
     *             @OA\Property(property="country", type="string", example="PY"),
     *             @OA\Property(property="location", type="object", nullable=true),
     *             @OA\Property(property="meta", type="object", nullable=true)
     *           )
     *         )
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         required={"name","email","password","password_confirmation"},
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="password", type="string"),
     *         @OA\Property(property="password_confirmation", type="string"),
     *         @OA\Property(property="addresses", type="string", description="JSON string"),
     *         @OA\Property(property="avatar", type="string", format="binary")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Creado",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="token", type="string"),
     *       @OA\Property(property="user", ref="#/components/schemas/User")
     *     )
     *   ),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $addresses = $this->normalizeAddressesInput($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->validateAddresses($addresses);

        $user = DB::transaction(function () use ($validated, $addresses, $request) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'status' => UserStatus::ACTIVE,
            ]);

            $role = Role::findOrCreate('cliente', guardName: config('auth.defaults.guard', 'web'));
            $user->assignRole($role);

            foreach ($addresses as $addressData) {
                Address::create([
                    'user_id' => $user->id,
                    ...$this->onlyAddressFillable($addressData),
                ]);
            }

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            }

            return $user;
        });

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/api/auth/login",
     *   tags={"Auth"},
     *   summary="Login de cliente",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="password", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="token", type="string"),
     *       @OA\Property(property="user", ref="#/components/schemas/User")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Invalid credentials"),
     *   @OA\Response(response=403, description="User not allowed")
     * )
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], (string) $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        if ($user->status !== UserStatus::ACTIVE) {
            return response()->json(['message' => 'Usuario no habilitado.'], 403);
        }

        if (! $user->hasRole('cliente')) {
            return response()->json(['message' => 'Usuario no autorizado como cliente.'], 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * @OA\Get(
     *   path="/api/auth/me",
     *   tags={"Auth"},
     *   summary="Perfil del cliente autenticado",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user();

        $this->assertClientUser($user);

        return new UserResource($user);
    }

    /**
     * @OA\Put(
     *   path="/api/auth/me",
     *   tags={"Auth"},
     *   summary="Actualizar datos del cliente autenticado (y agregar direcciones)",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="password", type="string", nullable=true, description="Solo se actualiza si viene con valor no vacío"),
     *         @OA\Property(property="password_confirmation", type="string", nullable=true),
     *         @OA\Property(
     *           property="addresses",
     *           type="array",
     *           @OA\Items(
     *             type="object",
     *             required={"street","city"},
     *             @OA\Property(property="street", type="string"),
     *             @OA\Property(property="city", type="string"),
     *             @OA\Property(property="state", type="string", nullable=true),
     *             @OA\Property(property="postal_code", type="string", nullable=true),
     *             @OA\Property(property="country", type="string", example="PY"),
     *             @OA\Property(property="location", type="object", nullable=true),
     *             @OA\Property(property="meta", type="object", nullable=true)
     *           )
     *         )
     *       )
     *     ),
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string"),
     *         @OA\Property(property="password", type="string", nullable=true),
     *         @OA\Property(property="password_confirmation", type="string", nullable=true),
     *         @OA\Property(property="addresses", type="string", description="JSON string"),
     *         @OA\Property(property="avatar", type="string", format="binary", description="Solo se actualiza si se envía un archivo")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/User")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->assertClientUser($user);

        $addresses = $this->normalizeAddressesInput($request);

        $avatarInput = $request->input('avatar');
        if (is_string($avatarInput) && trim($avatarInput) === '') {
            $request->request->remove('avatar');
        }

        $validator = validator($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],

            // Se valida en forma condicional: si viene vacío/null, se ignora sin error.
            'password' => ['nullable'],
            'password_confirmation' => ['nullable'],

            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $validator->sometimes('password', ['string', 'min:8', 'confirmed'], function ($input) {
            return is_string($input->password ?? null) && trim((string) $input->password) !== '';
        });

        $validator->validate();

        $this->validateAddresses($addresses);

        DB::transaction(function () use ($request, $user, $addresses) {
            if ($request->has('name')) {
                $user->name = (string) $request->input('name');
            }

            if ($request->has('email')) {
                $user->email = (string) $request->input('email');
            }

            if ($request->filled('password')) {
                $user->password = (string) $request->input('password');
            }

            $user->save();

            foreach ($addresses as $addressData) {
                Address::create([
                    'user_id' => $user->id,
                    ...$this->onlyAddressFillable($addressData),
                ]);
            }

            if ($request->hasFile('avatar')) {
                $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
            }
        });

        return new UserResource($user->fresh());
    }

    private function normalizeAddressesInput(Request $request): array
    {
        $addresses = $request->input('addresses', []);

        if (is_string($addresses)) {
            $decoded = json_decode($addresses, true);

            if (! is_array($decoded)) {
                return [];
            }

            $addresses = $decoded;
        }

        if (empty($addresses)) {
            return [];
        }

        if (Arr::isAssoc($addresses)) {
            return [$addresses];
        }

        return is_array($addresses) ? $addresses : [];
    }

    private function validateAddresses(array $addresses): void
    {
        validator(['addresses' => $addresses], [
            'addresses' => ['array'],
            'addresses.*.street' => ['required', 'string', 'max:255'],
            'addresses.*.city' => ['required', 'string', 'max:255'],
            'addresses.*.state' => ['nullable', 'string', 'max:255'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:50'],
            'addresses.*.country' => ['nullable', 'string', 'size:2'],
            'addresses.*.location' => ['nullable', 'array'],
            'addresses.*.location.lat' => ['nullable', 'numeric'],
            'addresses.*.location.lng' => ['nullable', 'numeric'],
            'addresses.*.meta' => ['nullable', 'array'],
        ])->validate();
    }

    private function onlyAddressFillable(array $addressData): array
    {
        $addressData = Arr::only($addressData, [
            'street',
            'city',
            'state',
            'postal_code',
            'country',
            'location',
            'meta',
        ]);

        if (isset($addressData['country']) && is_string($addressData['country'])) {
            $addressData['country'] = strtoupper($addressData['country']);
        }

        return $addressData;
    }

    private function assertClientUser(?User $user): void
    {
        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        if ($user->status !== UserStatus::ACTIVE) {
            abort(403, 'Usuario no habilitado.');
        }

        if (! $user->hasRole('cliente')) {
            abort(403, 'Usuario no autorizado como cliente.');
        }
    }
}
