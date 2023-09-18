<?php


namespace App\Repositories\Category;

use App\Models\User;
use App\Traits\ApiResponseWithHttpSTatus;
use Illuminate\Container\Container as App;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use stdClass;

class CategoriesRepository
{
    use ApiResponseWithHttpSTatus;

    /**
     * $result payload Api
     * @var \stdClass
     */
    protected $responses;

    /**
     * Model Auth
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * @param Request $request
     * @param Application $app
     */
    public function __construct(
        Request     $request,
        Application $app
    )
    {
        $this->responses = new stdClass();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        try {

        } catch (\Exception $e) {
            return [
                'status' => false,
                'headerCode' => 200,
                'message' => $e
            ];
        }

        return [
            'status' => true,
            'headerCode' => 200,
            'responses' => $this->responses
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function detail(Request $request): array
    {
        $id = (int)$request->input('id', $request->id ?? null);
        try {
            $category = DB::table('categories')
                ->where('id', $id)
                ->first();

            if (empty($category)) {
                return [
                    'status' => false,
                    'headerCode' => 400,
                    'message' => 'Không tồn tại dữ liệu này'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'headerCode' => 200,
                'message' => $e
            ];
        }
        return [
            'status' => true,
            'headerCode' => 200,
            'responses' => json_decode(json_encode($category))
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function edit(Request $request): array
    {
        try {
            // Detail menu
            $this->responses = $this->detail($request);
            if (!$this->responses['status']) {
                return $this->responses;
            }

            // TODO: Edit Sample
        } catch (\Exception $e) {
            return [
                'status' => false,
                'headerCode' => 200,
                'message' => $e
            ];
        }

        return $this->detail($request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function createModel(Request $request): array
    {
        try {
            DB::beginTransaction();

            $category = array(
                'name' => $request->input('name'),
                'parent' => $request->input('parent'),
                'isused' => $request->input('isused'),
                'timecreated' => now(),
                'timemodified' => now(),
            );
            $request->id = DB::table('categories')->insertGetId($category);
        } catch (\Exception $e) {
            return [
                'status' => false,
                'headerCode' => 500,
                'message' => $e
            ];
        }
        return $this->detail($request);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function deleteModel($request): array
    {
        try {
            // Detail menu
            $this->responses = $this->detail($request);
            if (!$this->responses['status']) {
                return $this->responses;
            }

            // TODO: Delete Sample
        } catch (\Exception $e) {
            return [
                'status' => false,
                'headerCode' => 200,
                'message' => $e
            ];
        }

        return [
            'status' => true,
            'headerCode' => 200,
            'responses' => json_decode(json_encode(['message' => trans('messages.success_update')]), false)
        ];
    }
}
