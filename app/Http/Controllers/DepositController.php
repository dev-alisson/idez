<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepositController extends Controller
{
    /**
     * Index
     * Renderiza todos os depósitos do
     * sistema ou somente aqueles que
     * atenderem a consulta
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        $deposits = Deposit::all();
        return response(
            [
                'data' => $deposits,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Store
     * Verifica os dados informados e
     * cadastra o depósito no sistema
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        /*
         * Verifica se a conta existe
         * na base de dados do sistema
         */
        if (!$this->exists($request->account_id)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The account entered does not exist!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Envia o saldo do depósito
         * para a conta correspondente
         */
        $this->transaction(
            $request->account_id,
            $request->amount
        );

        /*
         * Registra o histórico de depósito
         * na base de dados do sistema
         */
        $deposit = new Deposit;
        $deposit->account_id = $request->account_id;
        $deposit->amount = $request->amount;
        $deposit->save();

        /*
         * Retorn o ID do depósito cadastrado
         * e também a mensagem de sucesso
         */
        return response(
            [
                'id' => $deposit->id,
                'error' => null,
                'message' => 'Deposit successful!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Show
     * Retorna todos os dados do depósito
     * que atender ao identificador {$id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): \Illuminate\Http\Response
    {
        $deposit = Deposit::find($id);

        /*
         * Verifica se existe algum depósito que
         * corresponda com o identificador {$id}
         */
        if ($deposit) {
            $deposit->account = Account::find($deposit->account_id);
            $deposit->account->user = User::find($deposit->account->id);
        }

        /*
         * Retorna todos os dados do depósito
         * incluindo os dados do usuário e
         * também da conta
         */
        return response(
            [
                'data' => $deposit,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Transaction
     * Envia o saldo do depósito para a conta
     * que atender ao identificador {$id}
     *
     * @param int $id
     * @param float $amount
     */
    public function transaction(int $id, float $amount): void
    {
        $account = Account::find($id);
        $account->balance += $amount;
        $account->save();
    }

    /**
     * Exists
     * Retorna verdadeiro ou falso
     * para a rotina de cadastro
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return Account::find($id)->exists();
    }
}
