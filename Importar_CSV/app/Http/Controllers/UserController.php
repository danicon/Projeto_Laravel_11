<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Listar Usuarios
    public function index() {
        // Recuperar os registros do BD
        $users = User::get();

        // dd($users);

        // Carregar a VIEW
        return view('users.index', ['users' => $users]);
    }

    public function import(Request $request) {
        // dd($request);

        // Validar o arquivo
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ],[
            'file.required' => 'O campo arquivo é obrigatorio.',
            'file.mimes' => 'Arquivo invalido, nescessario enviar arquivo CSV.',
            'file.max' => 'Tamanho do arquivo excede :max Mb.'
        ]);

        // dd('continuar');

        // Criar array com as colunas no BD
        $headers = ['name', 'email', 'password'];

        // dd(array_map('str_getcsv', file($request->file('file'))));

        // Receber o arquivo, ler os dados e converter a string em array
        $dataFile = array_map('str_getcsv', file($request->file('file')));

        // Recebe a quantidade de registros cadastrados
        $numberRegisteredRecords = 0;

        // Recebe email que estão cadastrados no BD
        $emailAlreadyRegistered = false;

        // Percorrer as linhas do arquivo CSV
        foreach ($dataFile as $keyData => $row) {
            // dd($dataFile);

            // Converter a linha em array
            $values = explode(';', $row[0]);
            // dd($values);

            // Percorrer as colunas do cabeçalho
            foreach ($headers as $key => $header) {

                // Atribuir o valor ao elemento do array
                $arrayValues[$keyData][$header] = $values[$key];

                // Verifica se a coluna é email
                if($header == 'email') {
                    // Verifica se o email está cadastrado no BD
                    if(User::where('email', $arrayValues[$keyData]['email'])->first()) {
                        // Atribua o email na lista de emails já cadastrados
                        $emailAlreadyRegistered .= $arrayValues[$keyData]['email'] . ", ";
                    }
                }

                // Verifica se a coluna é senha
                if($header == "password") {
                    // Criptografar a senha
                    // $arrayValues[$keyData][$header] = Hash::make($arrayValues[$keyData]['password'], ['rounds' => 12]);
                    // dd($arrayValues[$keyData]['password']);

                    // Atribuir a senha ao elemento do array. gerar uma senha aleatoria com 7 caracteres
                    $arrayValues[$keyData][$header] = Hash::make(Str::random(7), ['rounds' => 12]);
                    // $arrayValues[$keyData][$header] = Str::random(7);
                }

               
                
            }
            // Incrementa mais um registro na quantidade de registro que serão cadastrados
            $numberRegisteredRecords++;
        }
        // dd($arrayValues);

        // Verificar se existe email já cadatrado, retorna erro e não cadastra no BD
        if($emailAlreadyRegistered) {
            // Redireciona o usuario para a pagina anterior e envia a mensagem de erro
            return back()->with('error', 'Dados não importados. Exitem emails já cadastrados:<br> ' . $emailAlreadyRegistered);
        }

        // Cadastrar registros no BD
        User::insert($arrayValues);

        // Redirecionar o usuario para a pagina anterior e enviar a msg de sucesso
        return back()->with('success', 'Dados importados com sucesso. <br>Quantidade: ' . $numberRegisteredRecords);
    }
}

