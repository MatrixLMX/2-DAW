
<form>
<button type="submit" name="orden" value="Nuevo"> Cliente Nuevo </button><br>
<br>

<table>
<tr>
    <td><button type="submit" name="id" value="asc">ASC</button><br><button type="submit" name="id" value="desc">DESC</button></td>
    <td><button type="submit" name="first_name" value="asc">ASC</button><br><button type="submit" name="first_name" value="desc">DESC</button></td>
    <td><button type="submit" name="email" value="asc">ASC</button><br><button type="submit" name="email" value="desc">DESC</button></td>
    <td><button type="submit" name="gender" value="asc">ASC</button><br><button type="submit" name="gender" value="desc">DESC</button></td>
    <td><button type="submit" name="ip_address" value="asc">ASC</button><br><button type="submit" name="ip_address" value="desc">DESC</button></td>
    <td></td>
</tr>
<tr><th>id</th><th>first_name</th><th>email</th>
<th>gender</th><th>ip_address</th><th>teléfono</th></tr>
<?php foreach ($tvalores as $valor): ?>
<tr>
<td><?= $valor->id ?> </td>
<td><?= $valor->first_name ?> </td>
<td><?= $valor->email ?> </td>
<td><?= $valor->gender ?> </td>
<td><?= $valor->ip_address ?> </td>
<td><?= $valor->telefono ?> </td>
<td><a href="#" onclick="confirmarBorrar('<?=$valor->first_name?>',<?=$valor->id?>);" >Borrar</a></td>
<td><a href="?orden=Modificar&id=<?=$valor->id?>">Modificar</a></td>
<td><a href="?orden=Detalles&id=<?=$valor->id?>" >Detalles</a></td>

<tr>
<?php endforeach ?>
</table>

<form>
<br>
<button type="submit" name="nav" value="Primero"> << </button>
<button type="submit" name="nav" value="Anterior"> < </button>
<button type="submit" name="nav" value="Siguiente"> > </button>
<button type="submit" name="nav" value="Ultimo"> >> </button>
<button type="submit" name="nav" value="Desconectar"> Salir </button>
</form>
