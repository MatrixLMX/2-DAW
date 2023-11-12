<?php 
function contarPalabras($comentario){
    return str_word_count($comentario,0);
}

function letraRepetida($comentario){
    $tamaño = strlen($comentario);
    $letraMax = "a";
    $vecesMax = 0;
    for ($i = 0;$i < $tamaño;$i++){
        $veces = 1;
        $letrai = $comentario[$i];
        for($j=$i+1;$j<$tamaño;$j++){
            if($letrai==$comentario[$j]){
                $veces++;
            }
        }
        if($veces > $vecesMax){
            $letraMax = $letrai;
            $vecesMax = $veces;
        }
    }
    return $letraMax;
}

function palabraRepetida($comentario){
    $npalabras = str_word_count($comentario,1);
    $vecesPalabras = array_count_values($npalabras);
    asort($vecesPalabras);
    return array_key_last($vecesPalabras);
}
?>

<div>
<b> Detalles:</b><br>
<table>
<tr><td>Longitud:          </td><td><?= strlen($_REQUEST['comentario']) ?></td></tr>
<tr><td>Nº de palabras:    </td><td><?= contarPalabras($_REQUEST['comentario']) ?></td></tr>
<tr><td>Letra + repetida:  </td><td><?= letraRepetida(($_REQUEST['comentario'])) ?></td></tr>
<tr><td>Palabra + repetida:</td><td><?= palabraRepetida(($_REQUEST['comentario'])) ?></td></tr>
</table>
</div>