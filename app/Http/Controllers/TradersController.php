<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\TraderCreateRequest;
use App\Http\Requests\TraderUpdateRequest;
use App\Repositories\TraderRepository;
use App\Validators\TraderValidator;

/**
 * Class TradersController.
 *
 * @package namespace App\Http\Controllers;
 */
class TradersController extends Controller
{
    /**
     * @var TraderRepository
     */
    protected $repository;

    /**
     * @var TraderValidator
     */
    protected $validator;

    /**
     * TradersController constructor.
     *
     * @param TraderRepository $repository
     * @param TraderValidator $validator
     */
    public function __construct(TraderRepository $repository, TraderValidator $validator)
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
        $traders = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $traders,
            ]);
        }

        return view('traders.index', compact('traders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  TraderCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(TraderCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $trader = $this->repository->create($request->all());

            $response = [
                'message' => 'Trader created.',
                'data'    => $trader->toArray(),
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
        $trader = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $trader,
            ]);
        }

        return view('traders.show', compact('trader'));
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
        $trader = $this->repository->find($id);

        return view('traders.edit', compact('trader'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  TraderUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(TraderUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $trader = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Trader updated.',
                'data'    => $trader->toArray(),
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
                'message' => 'Trader deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Trader deleted.');
    }
}
