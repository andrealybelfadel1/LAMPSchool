<?php

session_start();

/*
  Copyright (C) 2015 Pietro Tamburrano
  Questo programma è un software libero; potete redistribuirlo e/o modificarlo secondo i termini della
  GNU Affero General Public License come pubblicata
  dalla Free Software Foundation; sia la versione 3,
  sia (a vostra scelta) ogni versione successiva.

  Questo programma é distribuito nella speranza che sia utile
  ma SENZA ALCUNA GARANZIA; senza anche l'implicita garanzia di
  POTER ESSERE VENDUTO o di IDONEITA' A UN PROPOSITO PARTICOLARE.
  Vedere la GNU Affero General Public License per ulteriori dettagli.

  Dovreste aver ricevuto una copia della GNU Affero General Public License
  in questo programma; se non l'avete ricevuta, vedete http://www.gnu.org/licenses/
 */

@require_once("../php-ini" . $_SESSION['suffisso'] . ".php");
@require_once("../lib/funzioni.php");

// istruzioni per tornare alla pagina di login se non c'� una sessione valida
////session_start();
$tipoutente = $_SESSION["tipoutente"]; //prende la variabile presente nella sessione
if ($tipoutente == "")
{
    header("location: ../login/login.php?suffisso=" . $_SESSION['suffisso']);
    die;
}

//
//    Parte iniziale della pagina
//

$titolo = "Consigli orientativi";
$script = "<script type='text/javascript'>
         <!--
               
               function disabilitastampa(idalunno) 
               {
                   idimmagine='st'+idalunno;
                   document.getElementById(idimmagine).style.display = 'none';
                   document.getElementById('stampa').style.display = 'none';
                   
               }
         //-->
         </script>";
stampa_head($titolo, "", $script, "SPAD");
stampa_testata("<a href='../login/ele_ges.php'>PAGINA PRINCIPALE</a> - $titolo", "", "$nome_scuola", "$comune_scuola");


if ($livello_scuola == 2)
    $annocomp = "anno = '3'";
if ($livello_scuola == 3)
    $annocomp = "anno = '8'";



$idclasse = stringa_html('idclasse');

$con = mysqli_connect($db_server, $db_user, $db_password, $db_nome) or die("Errore durante la connessione: " . mysqli_error($con));






print ('
         <form method="post" action="cotabellone.php" name="voti">
   
         <p align="center">
         <table align="center">

         ');


//
//   Classi
//

print('
        <tr>
        <td width="50%"><b>Classe</b></td>
        <td width="50%">
        <SELECT ID="idclasse" NAME="idclasse" ONCHANGE="voti.submit()"><option value="">');

$query = "select idclasse, anno, sezione, specializzazione from tbl_classi where $annocomp "
        . " and idcoordinatore = ".$_SESSION['idutente']." order by anno, sezione, specializzazione";

$ris = mysqli_query($con, inspref($query)) or die("Errore: " . mysqli_error($con));
while ($nom = mysqli_fetch_array($ris))
{
    print "<option value='";
    print ($nom["idclasse"]);
    print "'";
    if ($idclasse == $nom["idclasse"])
    {
        print " selected";
    }
    print ">";
    print ($nom["anno"]);
    print "&nbsp;";
    print($nom["sezione"]);
    print "&nbsp;";
    print($nom["specializzazione"]);
}

echo('
      </SELECT>
      </td></tr></table><br></form>');


//
//  ALUNNI
//

if ($idclasse != '')
{



    $annoclasse = decodifica_anno_classe($idclasse, $con);


    $query = "select idalunno, cognome, nome, datanascita from tbl_alunni where idclasse='$idclasse' order by cognome,nome,datanascita";

    $ris = mysqli_query($con, inspref($query)) or die("Error " . mysqli_error($con));
    $numeroalunni = mysqli_num_rows($ris);

    print "<form action='coinserimento.php' method='post'>" ;
    print "<input type='hidden' name='idclasse' value='$idclasse'>";
    if ($numeroalunni > 0)
    {
        print("<table border=1 align=center>
                   <tr class='prima'>
                   <td width='50%'><b>Alunno</b></td><td>Consiglio orientativo</td><td>Stampa</td>");



        while ($nom = mysqli_fetch_array($ris))
        {
            $proposteimportate = false;
            $idalunno = $nom['idalunno'];

            print "<tr>";
            print "<td>" . $nom['cognome'] . " " . $nom['nome'] . " (" . data_italiana($nom['datanascita']) . ")</td>";

            $query = "select * from tbl_consorientativi where idalunno='$idalunno'";
            $ris2 = mysqli_query($con, inspref($query)) or die("Errore:" . mysqli_error($con) . " " . inspref($query));
            if ($recco = mysqli_fetch_array($ris2))
            {
                $consorientativo = $recco['consiglioorientativo'];
            } else
            {
                $consorientativo = "";
            }

            print "<td><input type='text' name='co_$idalunno' value='$consorientativo' onkeypress='disabilitastampa($idalunno);'><input type='hidden' name='coor_$idalunno' value='$consorientativo'></td>";
            print "<td><a href='costampa.php?idalunno=$idalunno'><img src='../immagini/stampa.png' id='st$idalunno' height='50%' width='50%'></td>";
            print "</tr>";
        }
        print "</table>";
    }
    print "<center><br><br><input type='submit' value='Salva modifiche'>";
    
    print "</form>";
    print "<br><a href='costampa.php?idclasse=$idclasse'><img src='../immagini/stampa.png' id='stampa'>";
}




mysqli_close($con);
stampa_piede("");
