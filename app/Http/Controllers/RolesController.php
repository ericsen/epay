<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Repositories\RoleRepository;
use App\Validators\RoleValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class RolesController.
 *
 * @package namespace App\Http\Controllers;
 */
class RolesController extends Controller
{

    /**
     * @var RoleRepository
     */
    protected $repository;

    /**
     * @var RoleValidator
     */
    protected $validator;

    /**
     * RolesController constructor.
     *
     * @param RoleRepository $repository
     * @param RoleValidator $validator
     */
    public function __construct(RoleRepository $repository, RoleValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        //排除非super角色的使用者，看到super角色
        if (Auth::user()->hasRole('super')) {
            $roles = $this->repository->orderBy('id', 'desc')->all(['id', 'name', 'display_name', 'use_for', 'description']);
        } else {
            $roles = $this->repository->orderBy('id', 'desc')->findWhere([['name', '!=', 'super']], ['id', 'name', 'display_name', 'use_for', 'description']);
        }

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $roles,
            ]);
        }

        return view('admin.roles.read', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RoleCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(RoleCreateRequest $request)
    {
        try {
            //載入欄位語系檔
            $this->validator->setAttributes(__('validation.attributes.roles'));

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $role = collect($this->repository->create($request->all()))->except(['created_at', 'updated_at']);

            $response = [
                'message' => __('contents.general.created'),
                'data'    => $role->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $role,
            ]);
        }

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->repository->find($id);

        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RoleUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        try {
            //載入欄位語系檔
            $this->validator->setAttributes(__('validation.attributes.roles'));

            // 排除 unique 驗證
            $this->validator->setId($id);

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $role = $this->repository->update($request->all(), $id);

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $role->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag(),
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => __('contents.general.deleted'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', __('contents.general.deleted'));
    }

    /**
     * 列出所有角色
     *
     * @return void
     */
    public function roles()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        //排除權限的新刪修查
        if (Auth::user()->hasRole('super')) {
            $roles = $this->repository
                ->with('permissions')
                ->orderBy('use_for', 'desc')
                ->orderBy('display_name', 'desc')
                ->all(['id', 'name', 'display_name', 'use_for', 'description']);
        } else {
            $roles = $this->repository
                ->with('permissions')
                ->orderBy('use_for', 'desc')
                ->orderBy('display_name', 'desc')
                ->findWhere([['name', '!=', 'super']], ['id', 'name', 'display_name', 'use_for', 'description']);
        }

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $roles,
            ]);
        }

        return view('admin.permission_role.read', compact('roles'));

    }

    /**
     * 單一角色選擇權限介面
     *
     * @param [type] $id
     * @return void
     */
    public function role_permissions_edit($roleId)
    {
        $role               = $this->repository->getHasPermissionNames($roleId);
        $roleDisplayName    = $role->display_name;
        $roleDescription    = $role->description;
        $roleId             = $role->id;
        $hasPermissionNames = $role->permissions->pluck('name');

        // debug($hasPermissionIds);

        $permissionRepository = app('App\Repositories\PermissionRepository');
        if (Auth::user()->hasRole('super')) {
            $allPermissions = $permissionRepository->all(['name', 'display_name', 'description']);
        } else {
            $allPermissions = $permissionRepository->findWhere([['name', 'NOT LIKE', 'permissions.%']], ['name', 'display_name', 'description']);
        }

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $role,
            ]);
        }

        return view('admin.permission_role.edit', compact('roleId', 'roleDescription', 'roleDisplayName', 'allPermissions', 'hasPermissionNames'));

    }

    /**
     * 單一角色調整權限請求
     *
     * @param RoleUpdateRequest $request
     * @param [type] $id
     * @return void
     */
    public function role_permissions_update(RoleUpdateRequest $request, $roleId)
    {
        $role = $this->repository->find($roleId)->syncPermissions($request->all());

        $response = [
            'message' => __('contents.general.updated'),
            'data'    => $role->toArray(),
        ];

        if ($request->wantsJson()) {
            return response()->json($response);
        }

        return redirect()->back()->with('message', $response['message']);
    }

    /**
     * 單一角色清空所有權限
     *
     * @param [type] $id
     * @return void
     */
    public function role_permissions_to_empty($roleId)
    {

    }

}
