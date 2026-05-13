<?php
$localConfig = include dirname(__DIR__, 4) . '/app/config.php';
$sessionPrefix = $localConfig['session_prefix'] ?? 'app_';
$csrfToken = \App\TokenManager::csrf();
if (empty($_SESSION[$sessionPrefix . 'admin'])) {
    header('Location: /admin');
    exit;
}
require __DIR__ . '/../../../../views/partials/admin_header.php'; ?>
<section class="py-5">
    <div class="container px-5">
        <?php if (!empty($_SESSION['error'])) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <!-- Breadcrumbs -->
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active" aria-current="page">User List</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- Contact form-->
        <div class="bg-light rounded-3 py-5 px-4 px-md-5 mb-5">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>User Management</h1>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Display Name</th>
                                <th>First Name</th>
                                <th>Second Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)) : ?>
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']) ?></td>
                                        <td><?php echo htmlspecialchars($user['display_name'] ?? '') ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name'] ?? '') ?></td>
                                        <td><?php echo htmlspecialchars($user['second_name'] ?? '') ?></td>
                                        <td><?php echo htmlspecialchars($user['email']) ?></td>
                                        <?php 
                                            if($user['dead_switch'] === 0 && $user['active'] === 0){
                                                $status = 'Inactive';
                                            }else if($user['dead_switch'] === 1 && $user['active'] === 0){
                                                $status = 'Suspended';
                                            }else{
                                                $status = 'Active';
                                            }
                                        ?>
                                        <td><?php echo $status; ?></td>
                                        <td>
                                            <a href="/admin/users/edit/<?php echo $user['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <?php
                                            $currentUserId = $_SESSION[$sessionPrefix . 'user_id'] ?? ($_SESSION[$sessionPrefix . 'user']['id'] ?? null);
                                            $isSelf = $currentUserId !== null && (int)$currentUserId === (int)$user['id'];

                                            if ($user['dead_switch'] === 0 && $user['active'] === 1) {
                                                if ($isSelf) {
                                                    echo '<button type="button" class="btn btn-sm btn-secondary" disabled title="You cannot suspend your own account">Suspend</button>';
                                                } else {
                                                    echo '<form method="post" action="/admin/users/suspend/' . $user['id'] . '" class="d-inline" onsubmit="return confirm(\'Suspend this user?\')">';
                                                    echo '<input type="hidden" name="token" value="' . htmlspecialchars($csrfToken) . '">';
                                                    echo '<button type="submit" class="btn btn-sm btn-secondary">Suspend</button>';
                                                    echo '</form>';
                                                }
                                            } else if ($user['dead_switch'] === 1 && $user['active'] === 0){
                                                echo '<a href="/admin/users/unsuspend/' . $user['id'] . '" class="btn btn-sm btn-secondary">Unsuspend</a>';
                                            }else{
                                                echo '<a href="/admin/users/activate/' . $user['id'] . '" class="btn btn-sm btn-success">Activate</a>';
                                            }
                                            ?>
                                            <?php if ($isSelf) : ?>
                                                <button type="button" class="btn btn-sm btn-danger" disabled title="You cannot delete your own account">Delete</button>
                                            <?php else : ?>
                                                <form method="post" action="/admin/users/delete/<?php echo $user['id'] ?>" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8">No users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Contact cards-->
        <a href="/admin/users/add" class="btn btn-primary">Add User</a>

        <!-- Pagination Controls -->
        <?php if (isset($totalPages) && $totalPages > 1) : ?>
            <nav aria-label="User pagination">
                <ul class="pagination mt-3 justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item<?php echo $i == $page ? ' active' : '' ?>">
                            <a class="page-link" href="?page=<?php echo $i ?>"><?php echo $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../../../../views/partials/footer.php'; ?>