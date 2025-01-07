<link rel="stylesheet" href="css/manage_roles.css">
<div id="manage_roles" class="content-section" style="display: none;">
    <h2>Manage roles</h2>
    <div class="roles-container">
        <div class="admins-container">
            <h3 class="roles-header">Admins</h3>
            <?php $admin->showAdmins(); ?>
        </div>

        <div class="players-container">
            <h3 class="roles-header">Players</h3>
            <?php $admin->showPlayers(); ?>
        </div>
    </div>
</div>