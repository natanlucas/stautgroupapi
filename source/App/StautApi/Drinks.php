<?php

namespace Source\App\StautApi;

use Source\Models\Drink;

/**
 * Class Drinks
 */
class Drinks extends StautApi
{
    public function __construct()
    {
        parent::__construct();
    }

    public function drink(array $data): void
    {
        if(empty($data["drink_ml"]) || !$drink_ml = filter_var($data["drink_ml"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "Favor informar a quantidade de aguá em mL"
            )->back();
            return;
        }

        if ($data["user_id"] != $this->user->id) {
            $this->call(
                400,
                "invalid_data",
                "Informe o ID do seu usuário para adicionar a quantidade de agua que bebeu."
            )->back();
            return;
        } 

        $drink = new Drink();

        $drink->user_id = $data["user_id"];
        $drink->drink_ml = $data["drink_ml"];

        $drink->save();

        $response["drink"] = [
            "Message" => "Agua contabilizada com sucesso",
            "Quantidade" => $drink->drink_ml . "mL"
        ];

        $this->back($response);

    }

    public function history(array $data): void
    {

    }
}
