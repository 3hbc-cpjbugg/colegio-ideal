<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Program;
use Spatie\QueryBuilder\QueryBuilder;

class ProgramController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/programs",
     *     tags={"Programs"},
     *     summary="Get list of programs, you can retrieve for your dataTable componente or raw format as well",
     *     description="The filters are not required, feel free to play with filters",
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Indicate with 'false' or 'true' if you prefere your data paginated",
     *         example="false",
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
    public function index(Request $request)
    {

        $paginate = $request->input('paginate', 'true') === 'true';

        $perPage = $request->input('per_page', config('pagination.per_page'));

        $query = QueryBuilder::for(Program::class)->with('students');

        $rows = $paginate
            ? $query->paginate($perPage)->appends($request->query())
            : $query->get();

        return response()->json($rows);
    }

    /**
     * @OA\Get(
     *     path="/api/programs/{program}",
     *     tags={"Programs"},
     *     summary="Get specific program resource",
     *     @OA\Parameter(
     *         name="program",
     *         in="path",
     *         @OA\Schema(
     *              type="string",
     *         ),
     *         required=true,
     *         description="Numeric ID of the program to retrieve",
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Request $request, Program $program)
    {
        return $this->sendResponse($program->load('students'), 'Program retrieved successfully.', 200);
    }

}
