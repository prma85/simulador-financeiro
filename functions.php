<?php

class simulador {

    public $error = '';
    public $juros_poupanca = 0.00645; // 0.5 ao mes de juros + TR
    public $juros_tesouro = 0.008355;  // 10.5 ao ano de juros + TR

    public function getPoupanca($inicial = 0.0, $mensal = 0.0, $meses = 0, $juros = 0.0) {
        $result = new stdClass();
        $juros = floatval($juros);

        if ($juros == 0.0) {
            $juros = 0.105;
        } else {
            $juros = $juros / 100;
        }

        $inicial = floatval(str_replace(',', '.', $inicial));
        $mensal = floatval(str_replace(',', '.', $mensal));
        $meses = intval($meses);

        if ($meses == 0) {
            $this->error = 'Quantidade de meses não informada';
            return false;
        } elseif ($inicial == 0.0 && $mensal == 0.0) {
            $this->error = 'Não foram informados os valores para ocalculo';
            return false;
        }

        $poupanca = $this->poupanca($inicial, $mensal, $meses);
        $result->total = $poupanca->total;
        $result->investimento = $poupanca->investimento;
        $result->milhao = $this->primeiroMilhao($inicial, $mensal, $meses, $juros);

        return $result;
    }

    public function getCambio($precisa = 0.0, $compra = 0.0, $venda = 0.0, $meses = 0) {
        $result = new stdClass();

        $precisa = floatval(str_replace(',', '.', $precisa));
        $compra = floatval(str_replace(',', '.', $compra));
        $venda = floatval(str_replace(',', '.', $venda));
        $meses = intval($meses);

        if ($meses == 0) {
            $this->error = 'Quantidade de meses não informada';
            return false;
        } elseif ($precisa == 0.0 || $compra == 0.0 || $venda == 0.0) {
            $this->error = 'Não foram informados todos os valores para o calculo';
            return false;
        }

        $result->pago = $precisa * $compra;
        $result->recebido = $precisa * $venda;
        $result->dif = $result->recebido - $result->pago;

        $poupanca = $this->poupanca($result->pago, 0, $meses);
        $result->poupanca = $poupanca->total;
        $result->tesouro = $poupanca->investimento;

        $result->poupanca_dif = $result->poupanca - $result->recebido;
        $result->tesouro_dif = $result->tesouro - $result->recebido;

        return $result;
    }

    public function getQuitacao($prestacao = 0.0, $quitar = 0.0, $meses = 0, $juros = 0, $reajuste = 0, $tipoparcela = 'unica', $tipojuros = 'fixo') {
        
        $result = new stdClass();
        
        $prestacao = floatval(str_replace(',', '.', $prestacao));
        $quitar = floatval(str_replace(',', '.', $quitar));
        $juros = floatval(str_replace(',', '.', $juros));
        $juros = $juros / 100;
        $reajuste = floatval(str_replace(',', '.', $reajuste));
        $reajuste = $reajuste / 100;
        $meses = intval($meses);

        $prestacoes = array();


        if ($prestacao == 0.0 || $quitar == 0.0 || $meses == 0) {
            $this->error = 'Não foram informados todos os valores para o calculo';
            return false;
        }
        
        if ($tipoparcela == 'unica') {
            $result->inicial = $prestacao;
        } else {
            $result->inicial = $prestacao * $meses;
        }

        if ($tipojuros == 'fixo') {
            $result->final = $result->inicial;
        } else {
            $result->final = $prestacao;
            $mes_atual = intval(date('m'));
            $i = $mes_atual + $meses;

            if ($tipoparcela == 'unica') {
                while ($mes_atual < $i) {
                    if ($mes_atual == 6 && $reajuste != 0) {
                        $result->final = ($result->final * (1 + $reajuste));
                    } else {
                        $result->final = ($result->final * (1 + $juros));
                    }

                    //echo $result->final.'<br>';
                    $mes_atual++;
                    if ($mes_atual == 12) {
                        $mes_atual = 1;
                        $i = $i - 12;
                    }
                }
            } else {
                $nova_prestacao = $prestacao;
                $prestacoes[] = $prestacao;
                //echo $prestacao;
                $result->final = $prestacao;
                while ($mes_atual < $i-1) {
                    if ($mes_atual == 6 && $reajuste != 0) {
                        $nova_prestacao = ($nova_prestacao * (1 + $reajuste));
                    } else {
                        $nova_prestacao = ($nova_prestacao * (1 + $juros));
                    }

                    $prestacoes[] = $nova_prestacao;

                    $result->final = $result->final + $nova_prestacao;

                    //echo $result->final.'<br>';

                    $mes_atual++;
                    if ($mes_atual == 12) {
                        $mes_atual = 1;
                        $i = $i - 12;
                    }
                }
            }
        }
        
        //var_dump($prestacoes);

        if ($quitar > $result->final) {
            $this->error = 'O valor de quitação não pode ser maior que o valor final';
            return false;
        }

        //calcula o valor hoje no dia da quitação
        $result->inicial = round($result->inicial, 2);
        $result->final = round($result->final, 2);
        $result->quitar = $quitar;
        $result->economia = $result->final - $result->quitar;

        //ver o valor, para pagamento mensal na poupança
        if ($tipoparcela == 'unica') {
            $poupanca = $this->poupanca($result->inicial, 0, $meses);
            $poupanca_quitar = $this->poupanca($result->inicial-$result->quitar, 0, $meses);

            //verifica quanto teria se não pagar a quitação
            $result->inicial_poupanca = $poupanca->total;
            $result->inicial_poupanca_sobra = $poupanca->total - $result->final;
            
            
            $result->inicial_tesouro = $poupanca->investimento;
            $result->inicial_tesouro_sobra = $poupanca->investimento - $result->final;

            //verifica quanto teria se pagar a quitação
            $result->inicial_poupanca_quitado = $poupanca_quitar->total;
            $result->inicial_tesouro_quitado = $poupanca_quitar->investimento;
            
        } else {
            $a = 0;
            $poupanca = $result->final;
            $tesouro = $result->final;
            $poupanca2 = $result->economia;
            $tesouro2 = $result->economia;
            while ($a < $meses) {
                if ($tipojuros == 'fixo') {
                    $poupanca = ($poupanca - $prestacao) * (1 + $this->juros_poupanca);
                    $tesouro = ($tesouro - $prestacao) * (1 + $this->juros_tesouro);
                } else {
                    $poupanca = ($poupanca - $prestacoes[$a]) * (1 + $this->juros_poupanca);
                    $tesouro = ($tesouro - $prestacao) * (1 + $this->juros_tesouro);
                }
                $poupanca2 = $poupanca2 * (1 + $this->juros_poupanca);
                $tesouro2 = $tesouro2 * (1 + $this->juros_tesouro);
                $a++;
            }

            //verifica quanto teria se não pagar a quitação
            $result->inicial_poupanca = $result->final;
            $result->inicial_poupanca_sobra = $poupanca;
            $result->inicial_tesouro_sobra = $tesouro;

            //verifica quanto teria se pagar a quitação
            $result->inicial_poupanca_quitado = $poupanca2;
            $result->inicial_tesouro_quitado = $tesouro2;
        }
        
        $result->dif = $result->inicial_poupanca_quitado - $result->inicial_poupanca_sobra;
        $result->dif2 = $result->inicial_tesouro_quitado - $result->inicial_tesouro_sobra;

        //var_dump($result);

        return $result;
    }

    private function poupanca($inicial, $mensal, $meses, $juros = 0.105) {
        $total = $inicial;
        $i = 1;
        $juros_poupanca = $this->juros_poupanca; // 0.005 + TR
        $juros_investimento = pow(1 + $juros, (1 / 12)) - 1; // 1+anual = (1+mensal)ˆmeses

        $investimento = $inicial;

        while ($i <= $meses) {
            $total = $total + ($total * $juros_poupanca) + $mensal;
            $investimento = $investimento + ($investimento * $juros_investimento) + $mensal;

            //echo $total . '<br>';
            $i++;
        }

        $result = new stdClass();
        $result->total = round($total, 2);
        $result->investimento = round($investimento, 2);
        $result->poucanca_rendimento = $result->total - $inicial;
        $result->investimento_rendimento = $result->investimento - $inicial;

        return $result;
    }

    private function primeiroMilhao($inicial, $mensal, $meses, $jinvestimento) {
        $milhao = new stdClass();
        $milhao->meses = 0;
        $milhao->mensal = 0;
        $total = $inicial;
        $total_investimento = $inicial;
        $total_investimento2 = $total_investimento;
        $i = 1;
        $j = 1;
        $juros = $this->juros_poupanca; // 0.005 + TR;
        $jinvestimento = pow(1 + $jinvestimento, (1 / 12)) - 1; // 1+anual = (1+mensal)ˆmeses
        $jinvestimento_imposto = $jinvestimento - ($jinvestimento * 0.15);


        // calcula os meses necessarios para chegar em 1 milhao
        while ($total <= 1000000) {
            $total = ($total * (1 + $juros)) + $mensal;
            /* if ($i <= $meses) {
              $inicial = $inicial + ($inicial * $juros);
              } */
            $i++;
        }

        // calcula os meses necessarios para chegar em 1 milhao
        while ($total_investimento <= 1000000) {
            $total_investimento = ($total_investimento * (1 + $jinvestimento_imposto)) + $mensal;
            $total_investimento2 = ($total_investimento * (1 + $jinvestimento)) + $mensal;

            $j++;
        }

        $milhao->meses = $i - 1;
        $anos_poucanca = $this->calculaAnos($milhao->meses);
        $milhao->anos = $anos_poucanca->anos;
        $milhao->anos_meses = $anos_poucanca->anos_meses;

        $milhao->inv_meses = $j - 1;
        $anos_inv = $this->calculaAnos($milhao->inv_meses);
        $milhao->inv_anos = $anos_inv->anos;
        $milhao->inv_anos_meses = $anos_inv->anos_meses;
        $milhao->imposto = round($total_investimento2 - $total_investimento, 2);


        $milhao->total = round($total, 2);
        $milhao->total_investimento = round($total_investimento, 2);

        return $milhao;
    }

    private function calculaAnos($meses) {
        $result = new stdClass();
        $result->anos = intval($meses / 12);
        $result->anos_meses = $meses - ($result->anos * 12);

        return $result;
    }

}
