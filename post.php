<?php

require_once 'functions.php';

$calc = new simulador();
$data = $_POST;
$html = array();

//var_dump($data);
if (!empty($data)) {
    if ($data['type'] == 'poupanca') {
        $result = $calc->getPoupanca($data['p_inicial'], $data['p_mensal'], $data['p_meses'], $data['p_juros']);
        if ($calc->error) {
            echo '<div class="alert-info">' . $calc->error . '</div>';
        } else {
            $html[] = '<strong>Total poupança no periodo:</strong> R$ ' . number_format($result->total, 2, ',', '.') . ' (' . $data['p_meses'] . ' meses)';
            $html[] = '<br /><strong>Tempo necessário para o primeiro milhão: </strong>' . $result->milhao->anos . ' anos e ' . $result->milhao->anos_meses . ' meses';
            $html[] = '<br /><strong>Total no final:</strong> R$ ' . number_format($result->milhao->total, 2, ',', '.');
            $html[] = '<br />';
            $html[] = '<br /><strong>Total investimentimento no periodo:</strong> R$ ' . number_format($result->investimento, 2, ',', '.');
            $html[] = '<br /><strong>Tempo necessário para o primeiro milhão: </strong>' . $result->milhao->inv_anos . ' anos e ' . $result->milhao->inv_anos_meses . ' meses';
            $html[] = '<br /><strong>Total no final:</strong> R$ ' . number_format($result->milhao->total_investimento, 2, ',', '.');
            $html[] = '<br /><strong>Imposto pago no investimento:</strong> R$ ' . number_format($result->milhao->imposto, 2, ',', '.');

            echo implode('', $html);
        }
    } elseif ($data['type'] == 'cambio') {
        $result = $calc->getCambio($data['c_quant'], $data['c_compra'], $data['c_venda'], $data['c_meses']);
        if ($calc->error) {
            echo '<div class="alert-info">' . $calc->error . '</div>';
        } else {
            $html[] = '<strong>Valor Necessário:</strong> $ ' . number_format(floatval($data['c_quant']), 2, '.', ',');
            $html[] = '<br /><strong>Valor de Compra:</strong> R$ ' . number_format($result->pago, 2, ',', '.');
            $html[] = '<br /><strong>Valor de Venda:</strong> R$ ' . number_format($result->recebido, 2, ',', '.');
            $html[] = '<br /><strong>Diferença:</strong> R$ ' . number_format($result->dif, 2, ',', '.') . ' em ' . $data['c_meses'] . ' meses';
            $html[] = '<br />';
            $html[] = '<br /><strong>Rendimento na poupança:</strong> R$ ' . number_format($result->poupanca, 2, ',', '.');
            $html[] = '<br /><strong>Diferença:</strong> R$ ' . number_format($result->poupanca_dif, 2, ',', '.');
            $html[] = '<br />';
            $html[] = '<br /><strong>Rendimento no tesouro:</strong> R$ ' . number_format($result->tesouro, 2, ',', '.');
            $html[] = '<br /><strong>Diferença:</strong> R$ ' . number_format($result->tesouro_dif, 2, ',', '.');

            echo implode('', $html);
        }
    } elseif ($data['type'] == 'quitacao') {
        $result = $calc->getQuitacao($data['q_prestacao'], $data['q_quitar'], $data['q_meses'], $data['q_juros'], $data['q_reajuste'], $data['q_tipoparcela'], $data['q_tipojuros']);
        if ($calc->error) {
            echo '<div class="alert-info">' . $calc->error . '</div>';
        } else {
            //var_dump($data);
            $html[] = '<strong>Valor hoje das parcelas:</strong> $ ' . number_format($result->inicial, 2, ',', '.');
            $html[] = '<br /><strong>Valor final das parcelas (com juros e reajuste):</strong> R$ ' . number_format($result->final, 2, ',', '.');
            $html[] = '<br /><strong>Valor de Quitação:</strong> R$ ' . number_format($result->quitar, 2, ',', '.');
            $html[] = '<br /><strong>Economia real:</strong> R$ ' . number_format($result->economia, 2, ',', '.');
            if ($data['q_tipoparcela'] == 'unica') {
                $html[] = '<br />';
                $html[] = '<br /><strong>Valor na poupança antes de quitar:</strong> R$ ' . number_format($result->inicial_poupanca, 2, ',', '.');
                $html[] = '<br /><strong>Valor no tesouro antes de quitar:</strong> R$ ' . number_format($result->inicial_tesouro, 2, ',', '.');
            }
            $html[] = '<br />';
            $html[] = '<br /><strong>Valor ao final na poupança (se não quitar):</strong> R$ ' . number_format($result->inicial_poupanca_sobra, 2, ',', '.');
            $html[] = '<br /><strong>Valor ao final na poupança (se quitar):</strong> R$ ' . number_format($result->inicial_poupanca_quitado, 2, ',', '.');
            $html[] = '<br /><strong>Diferença poupança (caso quite):</strong> R$ ' . number_format($result->dif, 2, ',', '.');
            $html[] = '<br />';
            
            $html[] = '<br /><strong>Valor ao final no tesouro (se não quitar):</strong> R$ ' . number_format($result->inicial_tesouro_sobra, 2, ',', '.');
            $html[] = '<br /><strong>Valor ao final no tesouro (se quitar):</strong> R$ ' . number_format($result->inicial_tesouro_quitado, 2, ',', '.');
            $html[] = '<br /><strong>Diferença tesouro (caso quite):</strong> R$ ' . number_format($result->dif2, 2, ',', '.');
            
            echo implode('', $html);
        }
    } else {
        die('Acesso Negado!');
    }
} else {
    die('Acesso Negado!');
}