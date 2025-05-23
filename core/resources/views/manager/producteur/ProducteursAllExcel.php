<style>
    #categories {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #categories td, #categories th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #categories tr:nth-child(even){background-color: #f2f2f2;}

    #categories tr:hover {background-color: #ddd;}

    #categories th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>

<table id="categories" width="100%">
    <thead>
    <tr>
        <td>ID</td>
        <td>cooperative</td>
        <td>village</td>
        <td>campement</td>
        <td>nom</td>
        <td>prenoms</td>
        <td>code Producteur</td>
        <td>code Producteur app</td>
        <td>sexe</td>
        <td>Statut/Plantation</td>
        <td>n° de la pièce</td>
        <td>n° de carte ccc</td>
        <td>date naissance</td>
        <td>numero de téléphone</td>
        <td>niveau d'instruction</td>
        <td>autre niveau d'instruction</td>
        <td>statut scolaire</td>
        <td>catégorie ethnique</td>
        <td>Date enreg</td> 
    </tr>
    </thead> 
    <?php
 
    foreach($producteurs as $c)
    {
    ?>
        <tbody>
        <tr>
            <td><?php echo $c->id; ?></td>
            <td><?php echo @$c->localite->section->cooperative->name; ?></td>
            <td><?php echo @$c->localite->section->libelle; ?></td>
            <td><?php echo @$c->localite->nom; ?></td>
            <!-- <td><?php //echo @$c->programme->libelle; ?></td> -->
            <td><?php echo $c->nom; ?></td>
            <td><?php echo $c->prenoms; ?></td>
            <td><?php echo $c->codeProd; ?></td>
            <td><?php echo $c->codeProdapp; ?></td>
            <td><?php echo $c->sexe; ?></td>
            <td><?php echo $c->proprietaires; ?></td>
            <td><?php echo $c->numPiece; ?></td>
            <td><?php echo $c->num_ccc; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->dateNaiss)); ?></td>
            <td><?php echo $c->phone1; ?></td>
            <td><?php echo $c->niveau_etude; ?></td>
            <td><?php echo $c->autre_instruction; ?></td>
            <td><?php echo $c->statut_scolaire; ?></td>
            <td><?php echo $c->categorie_ethnique; ?></td>
            <td><?php echo date('d-m-Y', strtotime($c->created_at)); ?></td>
        </tr>
        </tbody>
        <?php
    }
    ?>

</table>