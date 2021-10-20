<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\AgentCreateRequest;
use App\Http\Requests\AgentUpdateRequest;
use App\Repositories\AgentRepository;
use App\Validators\AgentValidator;

/**
 * Class AgentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class AgentsController extends Controller
{
    /**
     * @var AgentRepository
     */
    protected $repository;

    /**
     * @var AgentValidator
     */
    protected $validator;

    /**
     * AgentsController constructor.
     *
     * @param AgentRepository $repository
     * @param AgentValidator $validator
     */
    public function __construct(AgentRepository $repository, AgentValidator $validator)
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
        $agents = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $agents,
            ]);
        }

        return view('agents.index', compact('agents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AgentCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(AgentCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $agent = $this->repository->create($request->all());

            $response = [
                'message' => 'Agent created.',
                'data'    => $agent->toArray(),
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
        $agent = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $agent,
            ]);
        }

        return view('agents.show', compact('agent'));
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
        $agent = $this->repository->find($id);

        return view('agents.edit', compact('agent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  AgentUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(AgentUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $agent = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Agent updated.',
                'data'    => $agent->toArray(),
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
                'message' => 'Agent deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Agent deleted.');
    }
}
