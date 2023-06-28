<?php 
        
        // print_r('teste');

        $usuario = 'deusorte_root';
        $senha = 'Bwdmf1992@';
        $database = 'deusorte_rifa';
        $host = '127.0.0.1';


        $mysqli = new mysqli($host, $usuario, $senha, $database);

        $mysqli->set_charset("utf8");

        if($mysqli->error){
            
            die('Falha ao conectar ao banco de dados');
            
        }

        function consultarTelefone(){

            print_r('A função foi executada normalmente');

        }


        print_r('Conectado com Sucesso!');

        // print 'teste';
    
    ?>