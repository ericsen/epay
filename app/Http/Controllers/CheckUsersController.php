<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CheckUserCreateRequest;
use App\Http\Requests\CheckUserUpdateRequest;
use App\Repositories\CheckUserRepository;
use App\Validators\CheckUserValidator;

/**
 * Class CheckUsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class CheckUsersController extends Controller
{
    /**
     * @var CheckUserRepository
     */
    protected $repository;

    /**
     * @var CheckUserValidator
     */
    protected $validator;

    /**
     * CheckUsersController constructor.
     *
     * @param CheckUserRepository $repository
     * @param CheckUserValidator $validator
     */
    public function __construct(CheckUserRepository $repository, CheckUserValidator $validator)
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
        $checkUsers = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $checkUsers,
            ]);
        }

        return view('checkUsers.index', compact('checkUsers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CheckUserCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CheckUserCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $checkUser = $this->repository->create($request->all());

            $response = [
                'message' => 'CheckUser created.',
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
                    'message' => $e->getMessageBag()
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
        $checkUser = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $checkUser,
            ]);
        }

        return view('checkUsers.show', compact('checkUser'));
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
        $checkUser = $this->repository->find($id);

        return view('checkUsers.edit', compact('checkUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CheckUserUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(CheckUserUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $checkUser = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'CheckUser updated.',
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
                    'message' => $e->getMessageBag()
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
                'message' => 'CheckUser deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'CheckUser deleted.');
    }
}
