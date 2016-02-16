<h2 style="text-align: center;">Oxy - Object Oriented MVC Framework - PHP Edition - Module Generator</h2>
<br>
<p>
    The system read your application's database to know what tables it contains. It assumes that each table represents one 
    potential module.
</p>
<p>
    To create your module's MVC's, you can click on "create controller", "create model", "create views". Click on "create MVC"
    to automatically create all of these. Each MVC will be created with the same name of matching table and based on it's structure.
</p>
<p>
    After creating a MVC module(a Model, a View and a Controller), you can edit each file created to fulfill your application's needs.
    You can find them in the "/controllers", "/models" and "/views" directories.
</p>
<p>Your application's database has these tables listed bellow:</p>

<table class='table table-hover table-striped'>
    <thead>
        <tr>
            <th>Table Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tables as $t): ?>
            <tr>
                <td><?php echo $t; ?></td>
                <td>
                    <a href="/mvcgenerator/createmodel/<?php echo $t; ?>">Create Model</a>
                    <a href="/mvcgenerator/createviews/<?php echo $t; ?>">Create Views</a>
                    <a href="/mvcgenerator/createcontroller/<?php echo $t; ?>">Create Controller</a>
                    <a href="/mvcgenerator/createall/<?php echo $t; ?>">Create MVC</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>