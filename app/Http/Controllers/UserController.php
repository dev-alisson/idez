<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Index
     * Renderiza todos os usuários do
     * sistema ou somente aqueles que
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

        $users = User::all();
        return response(
            [
                'data' => $users,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Store
     * Verifica os dados informados e
     * cadastra o usuário no sistema
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): \Illuminate\Http\Response
    {
        /*
         * Verifica se o e-mail informado já está
         * sendo utilizado por outro usuário
         */
        if ($this->exists('email', $request->email)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The e-mail informed is already registered!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Verifica se o CPF informado já está
         * sendo utilizado por outro usuário
         */
        if ($this->exists('document', $request->document)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The document informed is already registered!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Realiza o cadastro do usuário
         * na base de dados do sistema
         */
        $user = new User;
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->document = $request->document;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        /*
         * Retorn o ID do usuário cadastrado
         * e também a mensagem de sucesso
         */
        return response(
            [
                'id' => $user->id,
                'error' => null,
                'message' => 'Registration successful!'
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Show
     * Retorna todos os dados do usuário
     * que atender ao identificador {$id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): \Illuminate\Http\Response
    {
        $user = User::find($id);
        return response(
            [
                'data' => $user,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Update
     * Verifica os dados informados e
     * atualiza o usuário no sistema
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): \Illuminate\Http\Response
    {
        /*
         * Verifica se o e-mail informado já está
         * sendo utilizado por outro usuário
         */
        if ($this->exists('email', $request->email, $id)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The e-mail informed is already registered!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Verifica se o CPF informado já está
         * sendo utilizado por outro usuário
         */
        if ($this->exists('document', $request->document, $id)) {
            return response(
                [
                    'error' => true,
                    'message' => 'The document informed is already registered!'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        /*
         * Realiza a atualização do usuário
         * na base de dados do sistema
         */
        $user = User::find($id);
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->document = $request->document;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

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
     * Remove o usuário do sistema que
     * atender ao identificador {$id}
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): \Illuminate\Http\Response
    {
        User::find($id)->delete();
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
     * @param $query
     * @return \Illuminate\Http\Response
     */
    public function get($query): \Illuminate\Http\Response
    {
        /*
         * Pesquisa por usuários que o nome, sobrenome,
         * documento ou e-mail que corresponda com a {$query}
         */
        $users = User::where('name', 'like', '%' . $query . '%')
            ->orWhere('lastname', 'like', '%' . $query . '%')
            ->orWhere('document', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->get();

        /*
         * Retorna todos os usuários que
         * satisfizeram a pesquisa
         */
        return response(
            [
                'data' => $users,
                'error' => null,
                'message' => null
            ],
            Response::HTTP_OK
        );
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
         * por outro usuário
         */
        if (!$id) {
            return User::where($key, '=', $value)
                ->exists();
        }

        /*
         * Update
         *
         * Verifica antes de atualizar se o
         * parâmetro {$key} está sendo utilizado
         * por outro usuário
         */
        if ($id) {
            return User::where($key, '=', $value)
                ->where('id', '!=', $id)
                ->exists();
        }
    }
}
