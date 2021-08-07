<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransferController extends Controller
{
    /**
     * Index
     * Renderiza todas as transferências
     * do sistema ou somente aquelas que
     * atenderem a consulta
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        $transfers = Transfer::all();
        return response(
            [
                'data' => $transfers,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Store
     * Verifica os dados informados e
     * cadastra a transferência no sistema
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        /*
         * Verifica se a conta de envio
         * existe na base de dados do sistema
         */
        if (!$this->exists($request->shipping_account_id)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The shipping account entered does not exist!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Verifica se a conta de recebimento
         * existe na base de dados do sistema
         */
        if (!$this->exists($request->receiving_account_id)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The receiving account entered does not exist!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Verifica se o saldo da conta de envio
         * é suficiente para completar a transação
         */
        if (!$this->balance($request->shipping_account_id, $request->amount)) {
            return response(
                [
                    'error' => true,
                    'message' => 'Insufficient balance!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Envia o saldo da transferência
         * para a conta de recebimento
         */
        $this->transaction(
            $request->shipping_account_id,
            $request->receiving_account_id,
            $request->amount
        );

        /*
         * Registra o histórico de transferência
         * na base de dados do sistema
         */
        $transfer = new Transfer;
        $transfer->shipping_account_id = $request->shipping_account_id;
        $transfer->receiving_account_id = $request->receiving_account_id;
        $transfer->amount = $request->amount;
        $transfer->save();

        /*
         * Retorn o ID da transferência cadastrada
         * e também a mensagem de sucesso
         */
        return response(
            [
                'id' => $transfer->id,
                'error' => null,
                'message' => 'Transfer successful!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Show
     * Retorna todos os dados da transferência
     * que atender ao identificador {$id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): \Illuminate\Http\Response
    {
        $transfer = Transfer::find($id);

        /*
         * Verifica se existe alguma transferência
         * que corresponda com o identificador {$id}
         */
        if ($transfer) {
            $transfer->shipping_account = Account::find($transfer->shipping_account_id);
            $transfer->shipping_account->user = User::find($transfer->shipping_account->user_id);
            $transfer->receiving_account = Account::find($transfer->receiving_account_id);
            $transfer->receiving_account->user = User::find($transfer->receiving_account->user_id);
        }

        /*
         * Retorna todos os dados da transferência
         * incluindo os dados do usuário e também
         * da conta
         */
        return response(
            [
                'data' => $transfer,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param int $shipping
     * @param int $receiving
     * @param float $amount
     */
    public function transaction(int $shipping, int $receiving, float $amount): void
    {
        /*
         * Diminui do saldo a quantia de {$amount}
         * da conta que está enviando a transferência
         */
        $shippingAccount = Account::find($shipping);
        $shippingAccount->balance -= $amount;
        $shippingAccount->save();

        /*
         * Aumenta no saldo a quantia de {$amount}
         * da conta que está recebendo a transferência
         */
        $receivingAccount = Account::find($receiving);
        $receivingAccount->balance += $amount;
        $receivingAccount->save();
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
        return Account::where('id', '=', $id)
            ->exists();
    }

    /**
     * Balance
     * Verifica se o saldo da conta de envio
     * é suficiente para completar a transação
     *
     * @param int $id
     * @param float $amount
     * @return bool
     */
    public function balance(int $id, float $amount): bool
    {
        return (Account::find($id)->balance >= $amount);
    }
}
