<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Ligacao\LigacaoIndex;
use App\Http\Requests\Ligacao\LigacaoStore;
use App\Http\Requests\Ligacao\LigacaoUpdate;
use App\Models\Feitas;
use App\Models\Ligacao;
use App\Services\QueryBuilders\LigacaoQueryBuilder;
use Illuminate\Http\JsonResponse;

class LigacaoController extends ApiController
{
    /**
     * @var Ligacao
     */
    private $ligacao;

    /**
     * @var Feitas
     */
    private $feitas;

    /**
     * ProfileController constructor.
     *
     * @param Ligacao $ligacao
     */
    public function __construct(Ligacao $ligacao, Feitas $feitas)
    {
        $this->ligacao = $ligacao;
        $this->feitas = $feitas;
    }

    /**
     * @param LigacaoIndex $request
     *
     * @return JsonResponse
     */
    public function index(LigacaoIndex $request): JsonResponse
    {
        $queryParams = $request->validatedOnly();
        $queryBuilder = new LigacaoQueryBuilder();
        $ligacaos = $this->ligacao->with(['feitas']);
        $ligacaos = $queryBuilder->setQuery($ligacaos)->setQueryParams($queryParams);
        $ligacaos = $ligacaos->paginate($request->get('per_page'));

        return $this->respond([
            'ligacaos' => $ligacaos,
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $ligacao = $this->ligacao
            ->with(['feitas'])
            ->findOrFail($id);

        return $this->respond($ligacao);
    }

    /**
     * @param LigacaoStore $request
     *
     * @return JsonResponse
     */
    public function store(LigacaoStore $request): JsonResponse
    {
        $params = $request->validatedOnly();
        $feitasParam = $params['feitas'];

        $ligacao = $this->ligacao->create($params);
        $ligacao->users()->attach(auth()->user()->id, [
            'avaliacao' => $feitasParam['avaliacao'],
            'data' => '',
        ]);
        return $this->respond([
            'message' => 'Ligacao successfully created'
        ]);
    }

    /**
     * @param LigacaoUpdate $request
     *
     * @return JsonResponse
     */
    public function update(LigacaoUpdate $request): JsonResponse
    {
        $params = $request->validatedOnly();
        $feitasParam = $params['feitas'];

        $ligacao = $this->ligacao
            ->findOrFail($params['ligacao_id']);
        $ligacao->update($params);
        $ligacao->users()->detach(auth()->user());
        $ligacao->users()->attach(auth()->user()->id, [
            'avaliacao' => $feitasParam['avaliacao'],
            'data' => '',
        ]);
        return $this->respond([
            'message' => 'Ligacao successfully updated',
            'ligacao' => $this->ligacao
                ->with(['feitas'])
                ->findOrFail($params['ligacao_id']),
        ]);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->ligacao->findOrFail($id)->delete();
        return $this->respond(['message' => 'Ligacao successfully deleted']);
    }
}
