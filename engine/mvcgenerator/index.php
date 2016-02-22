<style>
    #setup-contents{
        background-color:#f0f0f0;
        padding:20px;
    }
</style>
<div id="setup-contents">
    <div style="text-align: center;"><img src="/media/img/oxylogo.png"></div>
    <h2 style="text-align: center; margin-top:0px;">Oxy - Object Oriented MVC Framework - PHP Edition - Setup Screen</h2>
    <br>
    <p>
        Oxy framework reads your application's database to know what tables it contains. It assumes that each table represents one 
        potential module.
    </p>
    <p>
        To create your module's MVC's, you can click on "create controller", "create model" or "create views". Click on "create MVC"
        to automatically create all of those. Each MVC set will be created with the same name of matching table and based on it's structure.
        If a Model, View and/or Controller already exists within the module, creating a new one will overwrite the former file(s).
    </p>
    <p>
        After creating a module's MVC set(a Model, a View and/or a Controller), you can edit each file created to fulfill your application's needs.
        You can find them in the "/controllers", "/models" and "/views" directories.
    </p>
    <p style="color:#990000;">*: Do not forget to grant the required permissions for the application, as it will automatically create files in the directories.</p>
    <p><b>Your application's databases has these tables listed bellow:</b></p>

    <div style="box-shadow: 0px 0px 2px;">
        <table class='table table-hover table-striped'>
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php // Put database's tables list into an organized table with actions.
                foreach ($tables as $t): ?>
                    <tr>
                        <td><?php echo $t; ?></td>
                        <td>
                            <a href="/mvcgenerator/createmvc/<?php echo $t; ?>/model">Create Model</a>
                            &nbsp;
                            <a href="/mvcgenerator/createmvc/<?php echo $t; ?>/views">Create Views</a>
                            &nbsp;
                            <a href="/mvcgenerator/createmvc/<?php echo $t; ?>/controller">Create Controller</a>
                            &nbsp;
                            <a href="/mvcgenerator/createmvc/<?php echo $t; ?>/all">Create MVC</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>