<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Index
     * Renderiza todas as contas do
     * sistema ou somente aquelas que
     * atenderem a consulta
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): \Illuminate\Http\Response
    {
        if ($request->get('q')) {
            $query = $request->get('q');
            return $this->get($query);
        }

        $accounts = Account::all();
        return response(
            [
                'data' => $accounts,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Store
     * Verifica os dados informados e
     * cadastra a conta no sistema
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        /*
         * Verifica se a conta já
         * possui um CNPJ cadastrado
         */
        if ($request->cnpj) {
            /*
             * Verifica se o CNPJ informado já está
             * sendo utilizado por outra conta
             */
            if ($this->exists('cnpj', $request->cnpj)) {
                return response(
                    [
                        'error' => true,
                        'message' => 'The CNPJ informed is already registered!'
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            /*
             * Verifica se o usuário já possui
             * uma conta de pessoa jurídica
             */
            if ($this->limit($request->user_id, $request->cnpj)) {
                return response(
                    [
                        'error' => true,
                        'message' => 'You already have a PJ account '
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
        } else {
            /*
             * Verifica se o usuário já possui
             * uma conta de pessoa física
             */
            if ($this->limit($request->user_id)) {
                return response(
                    [
                        'error' => true,
                        'message' => 'You already have a PF account '
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }
        }

        /*
         * Realiza o cadastro da conta
         * na base de dados do sistema
         */
        $account = new Account;
        $account->user_id = $request->user_id;
        $account->agency = $request->agency;
        $account->number = $request->number;
        $account->digit = $request->digit;
        $account->cnpj = $request->cnpj;
        $account->corporate_name = $request->corporate_name;
        $account->fantasy_name = $request->fantasy_name;
        $account->type = $request->type;
        $account->save();

        /*
         * Retorn o ID da conta cadastrada
         * e também a mensagem de sucesso
         */
        return response(
            [
                'id' => $account->id,
                'error' => null,
                'message' => 'Registration successful!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Show
     * Retorna todos os dados da conta incluindo
     * transações de depósitos e de transferências
     * que atender ao identificador {$id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): \Illuminate\Http\Response
    {
        $account = Account::find($id);
        $account->user = User::find($account->user_id);
        $account->transactions = $this->transactions($account->id);

        return response(
            [
                'data' => $account,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Update
     * Verifica os dados informados e
     * atualiza a conta no sistema
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): \Illuminate\Http\Response
    {
        /*
         * Define os campos que seram
         * atualizados na base de dados
         */
        $account = Account::find($id);
        $account->agency = $request->agency;
        $account->number = $request->number;
        $account->digit = $request->digit;
        $account->type = $request->type;

        /*
         * Verifica antes de atualizar se
         * a conta já possui um CNPJ e se
         * o CNPJ não pertence a outra conta
         */
        if ($account->cnpj && $request->cnpj) {
            if ($this->exists('cnpj', $request->cnpj, $id)) {
                return response(
                    [
                        'error' => true,
                        'message' => 'The CNPJ informed is already registered!'
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            }

            /*
             * Define os campos que seram
             * atualizados na base de dados
             */
            $account->cnpj = $request->cnpj;
            $account->corporate_name = $request->corporate_name;
            $account->fantasy_name = $request->fantasy_name;
        }

        /*
         * Realiza a atualização da conta
         * na base de dados do sistema
         */
        $account->save();

        /*
         * Retorn mensagem de sucesso
         */
        return response(
            [
                'error' => null,
                'message' => 'Registration updated successfully!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Destroy
     * Remove a conta do sistema que
     * atender ao identificador {$id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): \Illuminate\Http\Response
    {
        Account::find($id)->delete();
        return response(
            [
                'error' => null,
                'message' => 'Registration successfully removed!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Get
     * Responsável por auxiliar as
     * buscas por usuários no sistema
     *
     * @param mixed $query
     * @return \Illuminate\Http\Response
     */
    public function get($query): \Illuminate\Http\Response
    {
        /*
         * Pesquisa por contas que a agência, número,
         * CNPJ, razão social ou nome fantasia que
         * corresponda com a {$query}
         */
        $accounts = Account::where('user_id', '=', $query)
            ->orWhere('agency', 'like', '%' . $query . '%')
            ->orWhere('number', 'like', '%' . $query . '%')
            ->orWhere('cnpj', 'like', '%' . $query . '%')
            ->orWhere('corporate_name', 'like', '%' . $query . '%')
            ->orWhere('fantasy_name', 'like', '%' . $query . '%')
            ->get();

        /*
         * Retorna todos as contas que
         * satisfizeram a pesquisa
         */
        return response(
            [
                'data' => $accounts,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Transactions
     * Retorna as transações de depósitos
     * e de transferências que atender ao
     * identificador {$id}
     *
     * @param int $id
     * @return \stdClass
     */
    public function transactions(int $id): \stdClass
    {
        $transactions = new \stdClass;

        /*
         * Retorna os depósitos que
         * atender ao identificar {$id}
         */
        $transactions->deposits = Deposit::where('account_id', '=', $id)->get();

        /*
         * Retorna as transferências que
         * atender ao identificar {$id}
         */
        $transactions->transfers = new \stdClass;
        $transactions->transfers->shipping = Transfer::where('shipping_account_id', '=', $id)->get();
        $transactions->transfers->receiving = Transfer::where('receiving_account_id', '=', $id)->get();

        /**
         * Retorna todas as transações da conta
         * que correspode ao identificador {$id}
         */
        return $transactions;
    }

    /**
     * Exists
     * Retorna verdadeiro ou falso
     * para as rotinas de cadastro
     * e atualização do sistema
     *
     * @param string $key
     * @param mixed $value
     * @param null $id
     * @return bool
     */
    public function exists(string $key, $value, $id = null): bool
    {
        /*
         * Store
         *
         * Verifica antes de cadastrar se o
         * parâmetro {$key} está sendo utilizado
         * por outra conta
         */
        if (!$id) {
            return Account::where($key, '=', $value)
                ->exists();
        }

        /*
         * Update
         *
         * Verifica antes de atualizar se o
         * parâmetro {$key} está sendo utilizado
         * por outra conta
         */
        if ($id) {
            return Account::where($key, '=', $value)
                ->where('id', '!=', $id)
                ->exists();
        }
    }

    /**
     * Limit
     * Método que valida para que usuários
     * possuam no máximo uma conta PF e no
     * máximo uma conta PJ
     *
     * @param int $user
     * @param string|null $cnpj
     * @return bool
     */
    public function limit(int $user, string $cnpj = null): bool
    {
        /*
         * PF
         *
         * Verifica se a usuário já possui
         * uma conta de pessoa física
         */
        if (!$cnpj) {
            return Account::where('user_id', '=', $user)
                ->whereNull('cnpj')
                ->exists();
        }

        /*
         * PJ
         *
         * Verifica se a usuário já possui
         * uma conta de pessoa jurídica
         */
        if ($cnpj) {
            return Account::where('user_id', '=', $user)
                ->whereNotNull('cnpj')
                ->exists();
        }
    }
}
