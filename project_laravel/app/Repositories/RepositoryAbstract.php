<?php

namespace App\Repositories;

use App\Enums\Constant;
use Illuminate\Container\Container as App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as AuthFace;
use Illuminate\Support\Facades\DB;

abstract class RepositoryAbstract implements RepositoryInterface
{

    /**
     * @var
     */
    protected $model;

    /**
     * @var App
     */
    private $app;

    /**
     * @var \stdClass
     */
    protected $REQUEST;

    /**
     * @var mixed
     */
    protected $ENV;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(App $app, Request $request)
    {
        $this->app = $app;
        $this->makeModel();

        $this->initiate($request);
    }

    /**
     * Initial methods to run
     *
     * @param Request $request
     * @return void
     */
    private function initiate(Request $request): void
    {
        if ($this->USER = AuthFace::user()) {
            $this->isLogin = true;
        };
        // Set global Parameter
//        $this->current_language($request);
//        $this->PERPAGE = $request->input('perpage', Constant::PER_PAGE);
        $this->PAGE = $request->input('page', 1);
        $this->REQUEST = $request->toArray();
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        return $this->model = $model->newQuery();
    }

    /**
     * @return mixed
     */
    abstract function model();

    /**
     * @param string[] $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
    }

    /**
     * @param $keyNeedUpdate
     * @param $data
     * @return mixed
     */
    public function updateOrCreate($keyNeedUpdate, $data)
    {
        return $this->updateOrCreate($keyNeedUpdate, $data);
    }

    /**
     * @param $perPage
     * @param $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*'))
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, string $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param string $table
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return int
     */
    public function updateV2(string $table, array $data, $id, string $attribute = "id"): int
    {
        return DB::table($table)->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, array $columns = ["*"])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param $field
     * @param $value
     * @param string[] $columns
     * @return mixed
     */
    public function findBy($field, $value, $columns = array('*'))
    {
        return $this->model->where($field, '=', $value)->first($columns);
    }
}
