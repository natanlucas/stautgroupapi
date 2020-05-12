<?php

namespace Source\App\StautApi;

use Source\Models\User;
use Source\Support\Pager;
/**
 * Class Users
 * @package Source\App\StautApi
 */
class Users extends StautApi
{
    /**
     * Users constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * List user data
     * @return [type] [description]
     */
    public function index(): void
    {
        $user = $this->user->data();

        unset($user->password, $user->created_at, $user->update_at);

        $response["user"] = $user;

        $this->back($response);
        return; 
    }

    /**
     * [create description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function create(array $data): void
    {
        $request = $this->requestLimit("usersCreate", 5, 60);
        if (!$request) {
            return;
        }

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
        
        if (empty($data["email"])) {
            $this->call(
                400,
                "invalid_data",
                "Email incorreto"
            )->back();
            return;
        }

        if (empty($data["full_name"])) {
            $this->call(
                400,
                "invalid_data",
                "Nome incorreto"
            )->back();
            return;
        }

        if (empty($data["password"])) {
            $this->call(
                400,
                "invalid_data",
                "Password incorreto"
            )->back();
            return;
        }        

        $this->user = new User();

        $this->user->full_name = $data["full_name"];
        $this->user->email = $data["email"];
        $this->user->password = $data["password"];

        if (!$this->user->save()) {
            $this->call(
                400,
                "invalid_data",
                $this->user->message()->getText()
            )->back();
            return;
        }

        $this->index();        
    }

    /**
     * [login description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function login(array $data): void
    {
        $request = $this->requestLimit("usersLogin", 5, 60);

        if (!$request) {
            return;
        }

        $user = $this->user->data();
        unset($user->password, $user->created_at, $user->update_at);

        $response["user"] = $user;
    
        $this->back($response);
        return;
    }

    /**
     * [read description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function read(array $data): void
    {
        if (empty($data["user_id"]) || !$user_id = filter_var($data["user_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Favor informar um ID válido para a consulta"
            )->back();
            return;
        }

        $user = $this->user->find("id = :id", "id={$user_id}")->fetch();

        if (!$user) {
            $this->call(
                404,
                "not_found",
                "Usuário não encontrado"
            )->back();
            return;
        }

        $user = $user->data();
        unset($user->password, $user->created_at, $user->update_at);

        $response["user"] = $user;
        
        $this->back($response);
        return;
    }

    /**
     * [readList description]
     * @return [type] [description]
     */
    public function readList(): void
    {
        $values = $this->headers;
    
        $users = $this->user->find();

        if(!$this->user->count()) {
            $this->call(
                404,
                "not_found",
                "Nenhum dado foi encontrado na pesquisa"
            )->back();
            return;
        }

        $page = (!empty($values["page"]) ? $values["page"] : 1);
        $pager = new Pager(url("/users/"));
        $pager->pager($users->count(), 10, $page);

        $response["results"] = $users->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        foreach ($users->limit($pager->limit())->offset($pager->offset())->order("id ASC")->fetch(true) as $user) {
            $user = $user->data();
            unset($user->password, $user->created_at, $user->update_at);
            $response["users"][] = $user;
        }

        $this->back($response);
        return;
    }

    /**
     * [update description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function update(array $data): void
    {
        $request = $this->requestLimit("usersUpdate", 5, 60);
        if (!$request) {
            return;
        }

        $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

        $this->user->full_name = (!empty($data["full_name"]) ? $data["full_name"] : $this->user->full_name);
        $this->user->email = (!empty($data["email"]) ? $data["email"] : $this->user->email);
        $this->user->password = (!empty($data["password"]) ? $data["password"] : $this->user->password);

        if (!$this->user->save()) {
            $this->call(
                400,
                "invalid_data",
                $this->user->message()->getText()
            )->back();
            return;
        }

        $this->index();

    }

    /**
     * [delete description]
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function delete(array $data): void
    {

        if(empty($data["user_id"]) || !$user_id = filter_var($data["user_id"]. FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Favor informar um ID válido para remoção"
            )->back();
            return;
        }

        if ($data["user_id"] != $this->user->id) {
            $this->call(
                400,
                "invalid_data",
                "Não é possível remover outros usuários, informe o seu ID"
            )->back();
            return;
        }    

        $this->user->destroy();
        $this->call(
            200,
            "success",
            "Usuário removido com sucesso!",
            "accepted"
        )->back();      
    }    
}