<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\RetrieveUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use OpenApi\Annotations as OA;


class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get list of user, you can retrieve for your dataTable componente or raw format as well",
     *     description="The filters are not required, feel free to play with filters",
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Get users by role (administrador or profesor or empty)",
     *         example="profesor",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Get relationship of your resource: program.students or just program",
     *         example="program.students",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Indicate with 'false' or 'true' if you prefere your data paginated",
     *         example="true",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Indicate number of item per page only apply for paginate version",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(RetrieveUserRequest $request)
    {

        $model = User::class;

        $role = $request->input('role', 'all');

        if ($role !== 'all') {
            $model = User::whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }

        $paginate = $request->input('paginate', 'true') === 'true';

        $perPage = $request->input('per_page', config('pagination.per_page'));

        $query = QueryBuilder::for($model)
            ->allowedIncludes('program.students');;

        $rows = $paginate
            ? $query->paginate($perPage)->appends($request->query())
            : $query->get();

        return response()->json($rows);
    }

    /**
     * @OA\Post(
     *      path="/api/users",
     *      tags={"Users"},
     *      summary="Store new user account and assing role",
     *      @OA\Parameter(
     *          name="role",
     *          description="Role of user (administrador or profesor)",
     *          required=true,
     *          in="query",
     *          example="profesor",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="username",
     *          description="Username for the new account",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="Name for the new account",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="Email for the new account",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="program_id",
     *          description="1 = Arte, 2 = Canto, 3 = Baile",
     *          example="1",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="Password for the new account",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content"
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = new User();
        $user->fill($data);
        $user->save();

        $user->assignRole($data['role']);

        return $this->sendResponse($user->load('roles'), 'User stored successfully.', 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Get specific user resource",
     *     description="You can use include to specific what relationship you need ",
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         @OA\Schema(
     *              type="string",
     *         ),
     *         required=true,
     *         description="Numeric ID of the user to retrieve",
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Get relationship of your resource: 'program.students' or just 'program'",
     *         example="program.students",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Request $request, User $user)
    {
        if ($request->filled('include')) {
            $relations = explode(',', $request->input('include', ''));
            $user->load($relations);
        }

        return $this->sendResponse($user, 'User retrieved successfully.', 200);
    }


    /**
     * @OA\Patch(
     *      path="/api/users/{user}",
     *      tags={"Users"},
     *      summary="Update user account",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         @OA\Schema(
     *              type="string",
     *         ),
     *         required=true,
     *         description="Numeric ID of the user to update",
     *      ),
     *      @OA\Parameter(
     *          name="username",
     *          description="Username for the account to update",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="Name for the account  to update",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="Email for the account to update",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="Password for the account to update",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content"
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->fill($request->validated());

        $user->save();

        $user->load('roles', 'program');

        return $this->sendResponse($user, 'User updated successfully.', 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/users/{user}",
     *      tags={"Users"},
     *      summary="Delete user account",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         @OA\Schema(
     *              type="string",
     *         ),
     *         required=true,
     *         description="Numeric ID of the user to delete",
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Content"
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->sendResponse('', 'User deleted successfully.', 200);
    }
}
