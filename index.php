<?php
    function attribution($origine) {
        if (isset($_POST[$origine]) && !empty($_POST[$origine])) {
            return $_POST[$origine];
        }
    }
    try {
        $bd = new PDO ('mysql:host=localhost;dbname=todolist;charset=utf8', 'root', 'ananas');
    } catch (Exception $e) {
        print_r("Erreur:" .$e->getMessage());
    }
    $false = 0;
    $true = 1;
    $data = attribution('todo');
    $dataSanitized = filter_var($data, FILTER_SANITIZE_STRING);
    $last = $bd->query('select tâche from task where ID = (select max(ID) from task)');
    $lastdata = $last->fetch();
    if (!empty($dataSanitized) && isset($dataSanitized) && $dataSanitized != $lastdata['tâche']) {
        $bd->query("insert into task (tâche, archive) values ('$dataSanitized','$false')");
    }
    $archive = $bd->query("select tâche from task where archive = 0");
    if (isset($_POST['archiver'])&& isset($_POST['list'])){
        for ($i = 0 ; $i < count($_POST['list']); $i++){
            $bd->exec('update task set archive = 1 where tâche = "'.$_POST['list'][$i].'"');
        }
    }
    if(isset($_POST['delete']) &&  isset($_POST['deletion'])) {
        for ($a = 0; $a < count($_POST['deletion']); $a++) {
            $bd->exec('delete from task where tâche = "'.$_POST['deletion'][$a].'"');
        }
    }
    $test = $bd->query("select tâche from task where archive = $false");
    $arch = $bd->query("select tâche from task where archive = $true");
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="style/style.css">
        <meta charset="utf-8">
        <title>ToDo List</title>
    </head>
    <body>
        <section class="page">
            <h1>TO DO LIST</h1>
            <form class="aFaire" action="index.php" method="post">
                <section class="activity">
                    <h2>To do</h2>
                    <?php
                        $variabletest = $test->fetchAll();
                        foreach ($variabletest as  $value) {
                            echo '<label class="list"><input type="checkbox" name="list[]" value="'.$value['tâche'].'">'.$value['tâche'].'</label><br/>';
                        }
                    ?>
                    <button type="submit" name="archiver"><p>Archiver</p></button>
                </section>
                <section class="entry">
                    <h2>New Entry</h2>
                    <input type="text" name="todo" value="">
                    <button type="submit" class="send" name="button"><p>Envoyer</p></button>
                </section>
                <section class="archived">
                    <h2>Done</h2>
                    <?php
                        $testArch = $arch->fetchAll();
                        foreach ($testArch as $value) {
                            echo '<label class = "line"><input type="checkbox" name="deletion[]" value="'.$value['tâche'].'">'.$value['tâche'].'</label><br/>';
                        }
                    ?>
                    <button type="submit" name="delete"><p>Supprimer</p></button>
                </section>
            </form>
        </section>
    </body>
</html>
