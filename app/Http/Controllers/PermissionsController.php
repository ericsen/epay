<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionCreateRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Repositories\PermissionRepository;
use App\Validators\PermissionValidator;
use Illuminate\Http\Request;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class PermissionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PermissionsController extends Controller
{
    /**
     * @var PermissionRepository
     */
    protected $repository;

    /**
     * @var PermissionValidator
     */
    protected $validator;

    /**
     * PermissionsController constructor.
     *
     * @param PermissionRepository $repository
     * @param PermissionValidator $validator
     */
    public function __construct(PermissionRepository $repository, PermissionValidator $validator)
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
        $permissions = $this->repository->orderBy('id', 'desc')->all(['id', 'name', 'display_name', 'use_in', 'description']);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $permissions,
            ]);
        }

        return view('admin.permissions.read', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PermissionCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(PermissionCreateRequest $request)
    {
        try {
            //載入欄位語系檔
            $this->validator->setAttributes(__('validation.attributes.permissions'));

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $permission = collect($this->repository->create($request->all()))->except(['created_at', 'updated_at']);

            $response = [
                'message' => __('contents.general.created'),
                'data'    => $permission->toArray(),
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
        $permission = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $permission,
            ]);
        }

        return view('permissions.show', compact('permission'));
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
        $permission = $this->repository->find($id);

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PermissionUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        try {
            //載入欄位語系檔
            $this->validator->setAttributes(__('validation.attributes.permissions'));
            //載入訊息語系檔
            // $this->validator->setMessages(trans('validation.messages.permissions'));

            // 排除 unique 驗證
            $this->validator->setId($id);

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $permission = $this->repository->update($request->all(), $id);

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $permission->toArray(),
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
}
