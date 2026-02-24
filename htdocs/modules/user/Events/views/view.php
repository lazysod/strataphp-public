<!-- events/view.php -->
<h1>Event Details</h1>
<?php if (isset($event)): ?>
    <h2><?= htmlspecialchars($event->title) ?></h2>
    <div><?= nl2br(htmlspecialchars($event->content)) ?></div>
    <p>Status: <strong><?= htmlspecialchars($event->status) ?></strong></p>
<?php else: ?>
    <p>Event not found.</p>
<?php endif; ?>
