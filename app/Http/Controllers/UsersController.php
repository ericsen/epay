<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckUserUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UsersController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var UserValidator
     */
    protected $validator;

    /**
     * 當前查詢的帳號身份
     *
     * @var [type]
     */
    public $identity;

    /**
     * 當前查詢的帳號上代身份
     *
     * @var [type]
     */
    public $parentIdentity;

    /**
     * 當前角色作用視圖
     *
     * @var [type]
     */
    public $scopeType;

    /**
     * UsersController constructor.
     *
     * @param UserRepository $repository
     * @param UserValidator $validator
     */
    public function __construct(UserRepository $repository, UserValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
        // debug(Route::currentRouteName());

        //當前角色作用視圖
        $this->scopeType = collect(explode('.', Route::currentRouteName()))->get(1);

        //後台帳號角色代號
        $identityAry = [
            'admins'    => 'B', //後台管理者
            'traders'   => 'T', //交易員
            'agents'    => 'A', //代理商
            'customers' => 'C', //商戶
        ];

        //當前操作角色
        if (!empty($identityAry[$this->scopeType])) {
            $this->identity = $identityAry[$this->scopeType];

            //如果是商戶，上代帳號身份轉為代理商，因為商戶的上代是代理商
            if ($this->identity == 'C') {
                $this->parentIdentity = 'A';
            } else {
                $this->parentIdentity = $identityAry[$this->scopeType];
            }
        } else {
            return redirect('/');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $search = '';

        if (!empty(request()->search)) {
            $search = request()->search;
        }

        $page = $this->repository
            ->getUserInfoWithSearch($search)
            ->where('identity', '=', $this->identity)
            ->orderByRaw('-passed_at asc')
            ->orderBy('created_at', 'desc')
            ->paginate();

        $users = collect($page->all());

        if (request()->wantsJson()) {
            return response()->json([
                'data' => $users,
            ]);
        }

        return view('admin.users.read', [
            'page'      => $page,
            'users'     => $users,
            'identity'  => $this->identity,
            'scopeType' => $this->scopeType,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(UserCreateRequest $request)
    {
        try {
            $createData  = $request->all();
            $roles       = $createData['roles'];
            $permissions = $createData['permissions'];
            reset($createData['roles']);
            reset($createData['permissions']);

            //資料驗證
            $this->validator->with($createData)->setAttributes(__('validation.attributes.users'))->passesOrFail(ValidatorInterface::RULE_CREATE);

            //建立密碼
            $createData['password'] = Hash::make($createData['password']);

            //帳號身份
            $createData['identity'] = $this->identity;

            //寫入審核人員
            if ($createData['enable'] == 'on') {
                $createData['inspector_id'] = Auth::user()->id;
                $createData['passed_at']    = date('Y-m-d H:i:s');
            }

            $user = $this->repository->create($createData);

            //同步角色與權限
            $user->syncRoles($roles);
            $user->syncPermissions($permissions);

            //增加帳號額外資料
            $createData['extra_data']['user_id'] = $user->id;
            $userExtra                           = $this->repository->syncExtraData($createData['extra_data'], $user);

            $response = [
                'message' => __('contents.general.created'),
                'data'    => $user->toArray(),
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

        $user = $this->repository->getUserDetailInfo()->findWhere([
            'id'       => $id,
            'identity' => $this->identity,
        ])->first();

        if (empty($user)) {
            abort(404);
        }

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $user,
            ]);
        }

        // debug($request->url());

        return view('admin.users.show', [
            'user'      => $user,
            'scopeType' => $this->scopeType,
        ]);
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

        $roleRepository = app('App\Repositories\RoleRepository');
        //所有角色
        $roles = $roleRepository->getAllRolePermissionsForEdit([$this->identity]);

        //所有可能的上代帳號
        $parents = $this->repository->getParentsByIdentity($this->parentIdentity, $id)->get();

        //取得欲修改的帳號
        $user = $this->repository
            ->getUserForEdit()
            ->findWhere([
                'id'       => $id,
                'identity' => $this->identity,
            ])->first();
        // dd($user);

        if (empty($user)) {
            abort(404);
        }

        //產生user身上的驗證qrcode
        if (!empty($user->checks) && count($user->checks) > 0) {
            foreach ($user->checks as $key => $value) {
                $user->checks[$key]->qrcode_data = QrCode::size(300)->margin(1)->generate($value->qrcode_data);
            }
        }

        //取得所有權限
        $permissionRepository = app('App\Repositories\PermissionRepository');
        $permissions          = $permissionRepository->getAllPermissionsForEdit();

        return view('admin.users.edit', [
            'user'        => $user,
            'roles'       => $roles,
            'parents'     => $parents,
            'permissions' => $permissions,
            'scopeType'   => $this->scopeType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UserUpdateRequest $request, $id)
    {

        $updateData = $request->all();

        $roles       = $updateData['roles'];
        $permissions = $updateData['permissions'];
        reset($updateData['roles']);
        reset($updateData['permissions']);

        //寫入審核人員
        if ($updateData['enable'] == 'on' && empty($updateData['inspector_id'])) {
            $updateData['inspector_id'] = Auth::user()->id;
            $updateData['passed_at']    = date('Y-m-d H:i:s');
        }

        try {
            //載入欄位語系檔
            $this->validator->setAttributes(__('validation.attributes.users'));

            // 排除 unique 驗證
            $this->validator->setId($id);

            //資料驗證
            $this->validator->with($updateData)->passesOrFail(ValidatorInterface::RULE_UPDATE);

            if (!empty($updateData['password'])) {
                $updateData['password'] = Hash::make($updateData['password']);
            }

            $user = $this->repository->find($id);

            //上代人員變更
            if ($user->parent_id !== $updateData['parent_id']) {
                if (!empty($updateData['parent_id'])) {
                    $newParent                        = $this->repository->find($updateData['parent_id']);
                    $updateData['hierarchical_path']  = empty($newParent->hierarchical_path) ? $newParent->id : $newParent->hierarchical_path . '/' . $newParent->id;
                    $updateData['hierarchical_level'] = $newParent->hierarchical_level + 1;
                } else {
                    $updateData['hierarchical_path']  = null;
                    $updateData['hierarchical_level'] = 1;
                }
            }

            //更新帳號資料
            $user = $this->repository->update($updateData, $id);

            //如果修改人員不是super角色，被修改擁有super角色時，添加回super角色
            if (!Auth::user()->hasRole('super') && $user->hasRole('super')) {
                $roleRepository = app('App\Repositories\RoleRepository');
                $superRoleId    = $roleRepository->findByField('name', 'super')->first()->id;
                $roles[]        = $superRoleId;
            }

            //同步角色與權限
            $user->syncRoles($roles);
            $user->syncPermissions($permissions);

            //更新帳號額外資料
            $userExtra = $this->repository->syncExtraData($updateData['extra_data'], $user);

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $user->toArray(),
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
     * 建立帳號
     *
     * @return void
     */
    public function create()
    {
        $roleRepository = app('App\Repositories\RoleRepository');
        $roles          = $roleRepository->getAllRolePermissionsForEdit([$this->identity]);

        $permissionRepository = app('App\Repositories\PermissionRepository');
        $permissions          = $permissionRepository->getAllPermissionsForEdit();

        $parents = $this->repository->getParentsByIdentity($this->parentIdentity)->get();

        // $extraData = app('App\Repositories\TraderRepository')->getAllFields();
        // debug();

        $scope = Str::title(Str::singular($this->scopeType));
        //修正後端管理人員namespace
        if ($scope == 'Admin') {
            $scope = 'User';
        }

        $objName   = 'App\Entities\\' . $scope;
        $extraData = collect($objName::getAllFields())->flip()->map(function ($name) {
            return '';
        });
        // dd($extraData);

        return view('admin.users.create', [
            'roles'       => $roles,
            'permissions' => $permissions,
            'parents'     => $parents,
            'identity'    => $this->identity,
            'extraData'   => $extraData,
        ]);
    }

    /**
     * 驗證帳號身份 QrCode
     *
     * @param [type] $id
     * @return void
     */
    public function check(CheckUserUpdateRequest $request, $id)
    {

        $updateData = $request->all();

        $checkUserRepository = app('App\Repositories\CheckUserRepository');

        try {
            //載入欄位語系檔
            $this->validator->setAttributes(__('validation.attributes.users'));

            $updateData['is_checked']   = 'yes';
            $updateData['inspector_id'] = Auth::user()->id;
            $updateData['checked_at']   = date('Y-m-d H:i:s');

            $checkUser = $checkUserRepository->update($updateData, $id);
            $checkUser = $checkUser->with('inspector')->get();

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $checkUser->toArray(),
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
}
