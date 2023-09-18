<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Category\CategoriesRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * @var CategoriesRepository
     */
    protected $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @param Request $request
     * @return array|Application[]|ResponseFactory[]|Response[]|\stdClass[]
     */
    public function index(Request $request)
    {
        return $this->categoriesRepository->index($request);
    }

    public function detail(Request $request)
    {
        return $this->categoriesRepository->detail($request);
    }

    public function edit(Request $request)
    {
        return $this->categoriesRepository->edit($request);
    }

    public function createModel(Request $request): array
    {
        return $this->categoriesRepository->createModel($request);
    }

    public function deleteModel(Request $request)
    {
        return $this->categoriesRepository->deleteModel($request);
    }

}
