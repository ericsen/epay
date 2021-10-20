<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CompanyBankCreateRequest;
use App\Http\Requests\CompanyBankUpdateRequest;
use App\Repositories\CompanyBankRepository;
use App\Validators\CompanyBankValidator;

/**
 * Class CompanyBanksController.
 *
 * @package namespace App\Http\Controllers;
 */
class CompanyBanksController extends Controller
{
    /**
     * @var CompanyBankRepository
     */
    protected $repository;

    /**
     * @var CompanyBankValidator
     */
    protected $validator;

    /**
     * CompanyBanksController constructor.
     *
     * @param CompanyBankRepository $repository
     * @param CompanyBankValidator $validator
     */
    public function __construct(CompanyBankRepository $repository, CompanyBankValidator $validator)
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
        $companyBanks = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $companyBanks,
            ]);
        }

        return view('admin.company_banks.read', compact('companyBanks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CompanyBankCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CompanyBankCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $companyBank = $this->repository->create($request->all());

            $response = [
                'message' => __('contents.general.created'),
                'data'    => $companyBank->toArray(),
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
        $companyBank = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $companyBank,
            ]);
        }

        return view('companyBanks.show', compact('companyBank'));
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
        $companyBank = $this->repository->find($id);

        return view('companyBanks.edit', compact('companyBank'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CompanyBankUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(CompanyBankUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $companyBank = $this->repository->update($request->all(), $id);

            $response = [
                'message' => __('contents.general.updated'),
                'data'    => $companyBank->toArray(),
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
                'message' => __('contents.general.deleted'),
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', __('contents.general.deleted'));
    }
}
