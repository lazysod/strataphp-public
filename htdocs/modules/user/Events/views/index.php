<!-- events/index.php -->
<!-- events/view.php -->
<?php
require_once dirname(__DIR__, 5) . '/bootstrap.php';
require dirname(__DIR__, 4) . '/views/partials/header.php';
/* 

                <?php foreach (App\Modules\User\Events\Models\Event::all() as $event): ?>
                    <li class="list-group-item">
                        <a href="?action=show&id=<?= $event->id ?>">
                            <?= htmlspecialchars($event->title) ?> (<?= htmlspecialchars($event->status) ?>)
                        </a>
                    </li>
                <?php endforeach; ?>
*/

?>
<div class="container">
    <div class="row mt-4">
        <div class="col-md-8 mx-auto">
            <h1>User Events</h1>
            <?php if (!empty($_SESSION['event_saved'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Event saved successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['event_saved']); ?>
            <?php endif; ?>
            <div class="card bg-dark p-3">
                <form method="post" action="/user/events/store">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label text-white"><span class="text-danger">*</span>Event Title</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Enter event title" value="<?php echo htmlspecialchars($event->title ?? ''); ?>" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="content" class="form-label text-white"><span class="text-danger">*</span>Event Content</label>
                        <textarea class="form-control" placeholder="Enter event details" name="content" id="content" rows="5" required><?php echo htmlspecialchars($event->content ?? ''); ?></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label for="ev_link" class="form-label text-white">Link</label> <span class="text-faded">(optional)</span>
                        <input type="url" class="form-control" name="ev_link" placeholder="https://example.com" id="ev_link" value="<?php echo htmlspecialchars($event->link ?? ''); ?>">
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check form-switch">
                            <input id="status" name="status" class="form-check-input check-green" type="checkbox" value="active" <?php if ( isset($event->status) && $event->status > 0 ){ echo 'checked'; } ?>>
                            <label class="form-check-label text-white" for="status">Active</label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars(\App\TokenManager::csrf()); ?>">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require dirname(__DIR__, 4) . '/views/partials/footer.php'; ?>